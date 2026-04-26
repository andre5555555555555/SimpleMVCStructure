<?php

class ValidationModel {
    private const MAX_IMAGE_SIZE = 5242880;
    private const ALLOWED_IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    function validateLogin(array $input){
        $username = trim($input['username'] ?? '');
        $password = (string) ($input['password'] ?? '');
        $errors = [];

        if ($username === '' || strlen($username) < 3 || strlen($username) > 50) {
            $errors[] = "Username must be between 3 and 50 characters.";
        }

        if ($password === '' || strlen($password) < 6 || strlen($password) > 100) {
            $errors[] = "Password must be between 6 and 100 characters.";
        }

        return [
            'data' => [
                'username' => $username,
                'password' => $password
            ],
            'errors' => $errors
        ];
    }

    function validateRegistration(array $input, array $allowedRoles){
        $username = trim($input['username'] ?? '');
        $password = (string) ($input['password'] ?? '');
        $confirmPassword = (string) ($input['confirm_password'] ?? '');
        $roleId = (int) ($input['role_id'] ?? 0);
        $errors = [];

        if ($username === '' || strlen($username) < 3 || strlen($username) > 50) {
            $errors[] = "Username must be between 3 and 50 characters.";
        }

        if ($password === '' || strlen($password) < 6 || strlen($password) > 100) {
            $errors[] = "Password must be between 6 and 100 characters.";
        }

        if ($password !== $confirmPassword) {
            $errors[] = "Passwords do not match.";
        }

        if (!in_array($roleId, $allowedRoles, true)) {
            $errors[] = "Select a valid account role.";
        }

        return [
            'data' => [
                'username' => $username,
                'password' => $password,
                'confirm_password' => $confirmPassword,
                'role_id' => $roleId
            ],
            'errors' => $errors
        ];
    }

    function validateItem(array $input){
        $item = trim($input['item_upload'] ?? '');
        $price = trim((string) ($input['item_price'] ?? ''));
        $shortDesc = trim($input['short_desc'] ?? '');
        $description = trim($input['full_desc'] ?? '');
        $errors = [];

        if ($item === '' || strlen($item) < 3 || strlen($item) > 255) {
            $errors[] = "Item name must be between 3 and 255 characters.";
        }

        if ($price === '' || !ctype_digit($price) || (int) $price <= 0) {
            $errors[] = "Price must be a positive whole number.";
        }

        if ($shortDesc === '' || strlen($shortDesc) < 10 || strlen($shortDesc) > 255) {
            $errors[] = "Short description must be between 10 and 255 characters.";
        }

        if ($description === '' || strlen($description) < 20) {
            $errors[] = "Full description must be at least 20 characters.";
        }

        return [
            'data' => [
                'item' => $item,
                'price' => (int) $price,
                'short_desc' => $shortDesc,
                'description' => $description
            ],
            'errors' => $errors
        ];
    }

    function validateCategories(array $categories, array $allowedCategoryIds){
        $selectedCategories = array_values(array_unique(array_map('intval', $categories)));
        $errors = [];

        if (empty($selectedCategories)) {
            $errors[] = "Select at least one category.";
        }

        foreach ($selectedCategories as $categoryId) {
            if (!in_array($categoryId, $allowedCategoryIds, true)) {
                $errors[] = "One or more selected categories are invalid.";
                break;
            }
        }

        return [
            'data' => $selectedCategories,
            'errors' => $errors
        ];
    }

    function validateImageUploads(array $files, $required = true){
        $errors = [];
        $fieldNames = [
            'item_front' => 'Front image',
            'item_right' => 'Right image',
            'item_left' => 'Left image',
            'item_back' => 'Back image'
        ];

        foreach ($fieldNames as $field => $label) {
            if (!isset($files[$field])) {
                if ($required) {
                    $errors[] = $label . " is required.";
                }
                continue;
            }

            $file = $files[$field];

            if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                if ($required) {
                    $errors[] = $label . " is required.";
                }
                continue;
            }

            if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
                $errors[] = $label . " upload failed.";
                continue;
            }

            if (($file['size'] ?? 0) > self::MAX_IMAGE_SIZE) {
                $errors[] = $label . " must not be larger than 5MB.";
            }

            $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
            if (!in_array($extension, self::ALLOWED_IMAGE_EXTENSIONS, true)) {
                $errors[] = $label . " must be a JPG, JPEG, PNG, or WEBP file.";
            }
        }

        return $errors;
    }
}
