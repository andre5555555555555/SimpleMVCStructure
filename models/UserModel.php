<?php
require_once "BaseModel.php";

class UserModel extends BaseModel {

    function getByUsername($username){
        $stmt = $this->connect->prepare("
            SELECT user_id, username, password, role_id
            FROM 2300692_user
            WHERE username = ?
        ");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    function authenticate($username, $password){
        $user = $this->getByUsername($username);

        if (!$user) {
            return null;
        }

        if (!password_verify($password, $user['password'])) {
            return null;
        }

        unset($user['password']);
        return $user;
    }

    function insert($username, $password, $roleId){
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->connect->prepare("
            INSERT INTO 2300692_user (username, password, role_id)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("ssi", $username, $hashedPassword, $roleId);
        return $stmt->execute();
    }
}
