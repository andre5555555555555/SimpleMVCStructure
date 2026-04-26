<?php
require_once __DIR__ . "/BaseController.php";
require_once __DIR__ . "/../models/BuyerListModel.php";
require_once __DIR__ . "/../models/ItemModel.php";
require_once __DIR__ . "/../models/PictureModel.php";

class BuyerListController extends BaseController {
    private $buyerListModel;
    private $itemModel;
    private $pictureModel;

    public function __construct(){
        parent::__construct();
        $this->buyerListModel = new BuyerListModel();
        $this->itemModel = new ItemModel();
        $this->pictureModel = new PictureModel();
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

    function myList(){
        $this->requireBuyer();
        $chairs = $this->addPrimaryPictures(
            $this->buyerListModel->getItemsByUser($this->currentUser()['user_id']),
            $this->pictureModel
        );
        $current_id = 0;
        $this->render("my-list", compact("chairs", "current_id"));
    }

    function addToList(){
        $this->requireBuyer();
        $this->verifyCSRFToken('buyer_list');

        $itemId = (int) ($_POST['item_id'] ?? 0);
        $item = $this->getItemOr404($itemId);
        $this->buyerListModel->addItem($this->currentUser()['user_id'], $item['item_id']);
        $this->redirectBack($this->url . "index.php?url=chair/" . $item['item_id']);
    }

    function removeFromList(){
        $this->requireBuyer();
        $this->verifyCSRFToken('buyer_list');

        $itemId = (int) ($_POST['item_id'] ?? 0);
        if ($itemId > 0) {
            $this->buyerListModel->removeItem($this->currentUser()['user_id'], $itemId);
        }

        $fallback = $itemId > 0
            ? $this->url . "index.php?url=chair/" . $itemId
            : $this->url . "index.php?url=my-list";

        $this->redirectBack($fallback);
    }
}
