<?php
require_once __DIR__ . "/BaseController.php";
require_once __DIR__ . "/../models/CategoryModel.php";
require_once __DIR__ . "/../models/ItemModel.php";
require_once __DIR__ . "/../models/PictureModel.php";
require_once __DIR__ . "/../models/BuyerListModel.php";
require_once __DIR__ . "/../models/UploadService.php";
require_once __DIR__ . "/../models/ValidationModel.php";

class ItemController extends BaseController {
    private $itemModel;
    private $categoryModel;
    private $pictureModel;
    private $uploadService;
    private $validationModel;

    public function __construct(){
        parent::__construct();
        $this->categoryModel = new CategoryModel();
        $this->itemModel = new ItemModel();
        $this->pictureModel = new PictureModel();
        $this->uploadService = new UploadService();
        $this->validationModel = new ValidationModel();
    }

    private function getItemOr404($id){
        if (!is_numeric($id)) {
            $this->notFound();
        }

        $item = $this->itemModel->getById((int) $id);
        if (empty($item)) {
            $this->notFound();
        }

        return $item;
    }

    private function canManageItem($item){
        $user = $this->currentUser();
        return !empty($item) && !empty($user) && (int) $user['user_id'] === (int) $item['user_id'];
    }

    private function getItemFormViewData($formName, array $chair = []){
        $oldInput = $this->pullFlash($formName . '_old', []);
        $formError = $this->pullFlash($formName . '_error');
        $categories = $this->categoryModel->getAll();
        $selectedCategories = array_column($this->categoryModel->getByItem($chair['item_id'] ?? 0), 'category_id');

        if (!empty($oldInput)) {
            $chair = array_merge($chair, [
                'item' => $oldInput['item_upload'] ?? ($chair['item'] ?? ''),
                'price' => $oldInput['item_price'] ?? ($chair['price'] ?? ''),
                'short_desc' => $oldInput['short_desc'] ?? ($chair['short_desc'] ?? ''),
                'description' => $oldInput['full_desc'] ?? ($chair['description'] ?? '')
            ]);
            $selectedCategories = $oldInput['categories'] ?? $selectedCategories;
        }

        return [
            'chair' => $chair,
            'categories' => $categories,
            'selectedCategories' => $selectedCategories,
            'csrfToken' => $this->generateCSRFToken($formName),
            'formError' => $formError
        ];
    }

    private function validateItemSubmission($formName, $requireFiles = true){
        $itemValidation = $this->validationModel->validateItem($_POST);
        $allowedCategoryIds = array_map('intval', array_column($this->categoryModel->getAll(), 'category_id'));
        $categoryValidation = $this->validationModel->validateCategories($_POST['categories'] ?? [], $allowedCategoryIds);
        $fileErrors = $this->validationModel->validateImageUploads($_FILES, $requireFiles);
        $errors = array_merge($itemValidation['errors'], $categoryValidation['errors'], $fileErrors);

        if (!empty($errors)) {
            $this->flash($formName . '_error', implode(' ', $errors));
            $this->flash($formName . '_old', [
                'item_upload' => trim($_POST['item_upload'] ?? ''),
                'item_price' => trim((string) ($_POST['item_price'] ?? '')),
                'short_desc' => trim($_POST['short_desc'] ?? ''),
                'full_desc' => trim($_POST['full_desc'] ?? ''),
                'categories' => $categoryValidation['data']
            ]);
        }

        return [
            'data' => $itemValidation['data'] + ['categories' => $categoryValidation['data']],
            'errors' => $errors
        ];
    }

    function index(){
        $chairs = $this->addPrimaryPictures($this->itemModel->getAll(), $this->pictureModel);
        $current_id = 0;
        $this->render("home", compact("chairs", "current_id"));
    }

    function shop(){
        $chairs = $this->addPrimaryPictures($this->itemModel->getAll(), $this->pictureModel);
        $this->render("shop", compact("chairs"));
    }

    function search($query){
        $results = $this->addPrimaryPictures($this->itemModel->search(trim($query)), $this->pictureModel);
        $this->render("search", compact("results"));
    }

    function show($id){
        $chair = $this->getItemOr404($id);
        $pictures = $this->pictureModel->getByItem($chair['item_id']);
        $categories = $this->categoryModel->getByItem($chair['item_id']);
        $chairs = $this->addPrimaryPictures($this->itemModel->getAll(), $this->pictureModel);
        $canManageItem = $this->canManageItem($chair);
        $isInBuyerList = false;

        if ($this->isBuyer()) {
            $buyerListModel = new BuyerListModel();
            $isInBuyerList = $buyerListModel->hasItem($this->currentUser()['user_id'], $chair['item_id']);
        }

        $this->render("info", compact("chair", "pictures", "categories", "chairs", "canManageItem", "isInBuyerList"));
    }

    function add(){
        $this->requireSeller();
        $this->render("upload", $this->getItemFormViewData('upload_item'));
    }

    function insert(){
        $this->requireSeller();
        $this->verifyCSRFToken('upload_item');

        $validation = $this->validateItemSubmission('upload_item', true);
        if (!empty($validation['errors'])) {
            $this->redirect("index.php?url=add");
        }

        $user = $this->currentUser();
        $itemId = $this->itemModel->insert($validation['data'] + ["user_id" => $user["user_id"]]);

        try {
            $pictures = [
                [$this->uploadService->upload($_FILES["item_front"]), 'yes', $user["user_id"]],
                [$this->uploadService->upload($_FILES["item_right"]), '', $user["user_id"]],
                [$this->uploadService->upload($_FILES["item_left"]), '', $user["user_id"]],
                [$this->uploadService->upload($_FILES["item_back"]), '', $user["user_id"]]
            ];

            $this->pictureModel->insertPictures($itemId, $pictures);
            foreach ($validation['data']['categories'] as $categoryId) {
                $this->categoryModel->assignToItem($itemId, $categoryId, $user["user_id"]);
            }
        } catch (RuntimeException $exception) {
            $this->itemModel->delete($itemId);
            $this->flash('upload_item_error', $exception->getMessage());
            $this->flash('upload_item_old', [
                'item_upload' => trim($_POST['item_upload'] ?? ''),
                'item_price' => trim((string) ($_POST['item_price'] ?? '')),
                'short_desc' => trim($_POST['short_desc'] ?? ''),
                'full_desc' => trim($_POST['full_desc'] ?? ''),
                'categories' => $validation['data']['categories']
            ]);
            $this->redirect("index.php?url=add");
        }

        $this->redirect();
    }

    function edit($id){
        $this->requireSeller();
        $chair = $this->getItemOr404($id);

        if (!$this->canManageItem($chair)) {
            $this->noAccess();
        }

        $this->render("edit", $this->getItemFormViewData('edit_item_' . $chair['item_id'], $chair));
    }

    function update($id){
        $this->requireSeller();
        $chair = $this->getItemOr404($id);
        $this->verifyCSRFToken('edit_item_' . $chair['item_id']);

        if (!$this->canManageItem($chair)) {
            $this->noAccess();
        }

        $validation = $this->validateItemSubmission('edit_item_' . $chair['item_id'], false);
        if (!empty($validation['errors'])) {
            $this->redirect("index.php?url=edit/" . $chair['item_id']);
        }

        $this->itemModel->update($validation['data'], $chair['item_id']);
        $this->categoryModel->deleteByItem($chair['item_id']);
        foreach ($validation['data']['categories'] as $categoryId) {
            $this->categoryModel->assignToItem($chair['item_id'], $categoryId, $this->currentUser()['user_id']);
        }
        $this->redirect();
    }

    function delete(){
        $this->requireSeller();
        $this->verifyCSRFToken('delete_item');

        $id = (int) ($_POST["item_id"] ?? 0);
        $item = $this->getItemOr404($id);

        if (!$this->canManageItem($item)) {
            $this->noAccess();
        }

        $pictures = $this->pictureModel->getByItem($id);
        foreach ($pictures as $picture) {
            if (file_exists($picture['pic_loc'])) {
                unlink($picture['pic_loc']);
            }
        }

        $this->pictureModel->deleteByItem($id);
        $this->itemModel->delete($id);
        $this->redirect();
    }
}
