<?php

class BaseController {
    protected const SELLER_ROLE_ID = 1;
    protected const BUYER_ROLE_ID = 2;

    public $url;

    public function __construct(){
        $scriptName = str_replace("\\", "/", dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $basePath = rtrim($scriptName, "/");
        $this->url = ($basePath === '' || $basePath === '.') ? "/" : $basePath . "/";
    }

    protected function currentUser(){
        return $_SESSION['user'] ?? null;
    }

    protected function isSeller(){
        $user = $this->currentUser();
        return !empty($user) && (int) $user['role_id'] === self::SELLER_ROLE_ID;
    }

    protected function isBuyer(){
        $user = $this->currentUser();
        return !empty($user) && (int) $user['role_id'] === self::BUYER_ROLE_ID;
    }

    protected function flash($key, $value){
        $_SESSION[$key] = $value;
    }

    protected function pullFlash($key, $default = null){
        $value = $_SESSION[$key] ?? $default;
        unset($_SESSION[$key]);
        return $value;
    }

    protected function render($view, array $data = []){
        extract($data);
        include __DIR__ . "/../views/" . $view . ".php";
    }

    protected function redirect($path = ""){
        $target = $path === "" ? $this->url : $this->url . ltrim($path, "/");
        header("Location: " . $target);
        exit;
    }

    protected function redirectBack($fallback = ""){
        $target = $fallback !== "" ? $fallback : $this->url;

        if (!empty($_SERVER['HTTP_REFERER'])) {
            $target = $_SERVER['HTTP_REFERER'];
        }

        header("Location: " . $target);
        exit;
    }

    protected function requireSeller(){
        if (!$this->currentUser()) {
            $this->redirect("index.php?url=login");
        }

        if (!$this->isSeller()) {
            $this->noAccess();
        }
    }

    protected function requireBuyer(){
        if (!$this->currentUser()) {
            $this->redirect("index.php?url=login");
        }

        if (!$this->isBuyer()) {
            $this->noAccess();
        }
    }

    function generateCSRFToken($form) {
        $_SESSION["csrf_token"][$form] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'][$form];
    }

    function verifyCSRFToken($form) {
        if (
            !isset($_SESSION['csrf_token'][$form]) ||
            !isset($_POST["csrf_token"]) ||
            !hash_equals($_SESSION['csrf_token'][$form], $_POST["csrf_token"])
        ) {
            $this->noAccess();
        }
    }

    protected function addPrimaryPictures(array $chairs, $pictureModel){
        foreach ($chairs as &$chair) {
            $pictures = $pictureModel->getByItem($chair['item_id']);
            $chair['pic_loc'] = $pictures[0]['pic_loc'] ?? 'images/default.png';
        }
        unset($chair);

        return $chairs;
    }

    public function getImageSources($path){
        $normalizedPath = ltrim((string) $path, "/\\");
        $sources = [
            'src' => $normalizedPath,
            'webp' => null
        ];

        if ($normalizedPath === '' || strpos($normalizedPath, 'images/') !== 0) {
            return $sources;
        }

        $absolutePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $normalizedPath);
        if (!is_file($absolutePath)) {
            return $sources;
        }

        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
        if ($extension === 'webp') {
            $sources['webp'] = $normalizedPath;
            return $sources;
        }

        if (!in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
            return $sources;
        }

        $webpAbsolutePath = preg_replace('/\.[^.]+$/', '.webp', $absolutePath);
        $webpRelativePath = preg_replace('/\.[^.]+$/', '.webp', $normalizedPath);

        if (!is_file($webpAbsolutePath)) {
            $this->createWebpVariant($absolutePath, $webpAbsolutePath, $extension);
        }

        if (is_file($webpAbsolutePath)) {
            $sources['webp'] = $webpRelativePath;
        }

        return $sources;
    }

    private function createWebpVariant($sourcePath, $webpPath, $extension){
        if (!function_exists('imagewebp')) {
            return;
        }

        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                if (!function_exists('imagecreatefromjpeg')) {
                    return;
                }
                $image = @imagecreatefromjpeg($sourcePath);
                break;
            case 'png':
                if (!function_exists('imagecreatefrompng')) {
                    return;
                }
                $image = @imagecreatefrompng($sourcePath);
                if ($image !== false) {
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                }
                break;
            default:
                return;
        }

        if ($image === false) {
            return;
        }

        @imagewebp($image, $webpPath, 82);
        imagedestroy($image);
    }

    function noAccess(){
        http_response_code(403);
        $this->render("no-access");
        exit;
    }

    function notFound(){
        http_response_code(404);
        $this->render("not-found");
        exit;
    }
}
