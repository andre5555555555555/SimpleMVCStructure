<?php
require_once __DIR__ . "/BaseController.php";
require_once __DIR__ . "/../models/UserModel.php";
require_once __DIR__ . "/../models/ValidationModel.php";

class AuthController extends BaseController {
    private $userModel;
    private $validationModel;

    public function __construct(){
        parent::__construct();
        $this->userModel = new UserModel();
        $this->validationModel = new ValidationModel();
    }

    private function getLoginViewData(){
        return [
            'csrfToken' => $this->generateCSRFToken('login_user'),
            'authError' => $this->pullFlash('auth_error'),
            'authSuccess' => $this->pullFlash('auth_success'),
            'oldInput' => $this->pullFlash('login_old', [])
        ];
    }

    private function getRegisterViewData(){
        return [
            'roles' => [
                self::BUYER_ROLE_ID => 'Buyer',
                self::SELLER_ROLE_ID => 'Seller'
            ],
            'csrfToken' => $this->generateCSRFToken('register_user'),
            'registerError' => $this->pullFlash('register_error'),
            'oldInput' => $this->pullFlash('register_old', [])
        ];
    }

    function login(){
        if ($this->currentUser()) {
            $this->redirect();
        }

        $this->render("login", $this->getLoginViewData());
    }

    function authenticate(){
        $this->verifyCSRFToken('login_user');
        $validation = $this->validationModel->validateLogin($_POST);

        $this->flash('login_old', ['username' => $validation['data']['username']]);

        if (!empty($validation['errors'])) {
            $this->flash('auth_error', implode(' ', $validation['errors']));
            $this->redirect("index.php?url=login");
        }

        $user = $this->userModel->authenticate($validation['data']['username'], $validation['data']['password']);
        if (!$user) {
            $this->flash('auth_error', "Invalid username or password.");
            $this->redirect("index.php?url=login");
        }

        unset($_SESSION['login_old']);
        $_SESSION['user'] = $user;
        $this->redirect();
    }

    function register(){
        if ($this->currentUser()) {
            $this->redirect();
        }

        $this->render("register", $this->getRegisterViewData());
    }

    function storeUser(){
        $this->verifyCSRFToken('register_user');
        $validation = $this->validationModel->validateRegistration($_POST, [self::BUYER_ROLE_ID, self::SELLER_ROLE_ID]);

        $this->flash('register_old', [
            'username' => $validation['data']['username'],
            'role_id' => $validation['data']['role_id']
        ]);

        if (!empty($validation['errors'])) {
            $this->flash('register_error', implode(' ', $validation['errors']));
            $this->redirect("index.php?url=register");
        }

        if ($this->userModel->getByUsername($validation['data']['username'])) {
            $this->flash('register_error', "Username already exists.");
            $this->redirect("index.php?url=register");
        }

        $this->userModel->insert(
            $validation['data']['username'],
            $validation['data']['password'],
            $validation['data']['role_id']
        );

        unset($_SESSION['register_old']);
        $this->flash('auth_success', "Account created. You can log in now.");
        $this->redirect("index.php?url=login");
    }

    function logout(){
        unset($_SESSION['user']);
        $this->flash('auth_success', "You have been logged out.");
        $this->redirect("index.php?url=login");
    }
}
