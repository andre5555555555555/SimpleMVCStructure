<?php
require_once "BaseModel.php";

class BuyerListModel extends BaseModel {

    public function __construct(){
        parent::__construct();
        $this->ensureTable();
    }

    private function ensureTable(){
        $this->connect->query("
            CREATE TABLE IF NOT EXISTS 2300692_buyer_list (
                user_id INT(11) NOT NULL,
                item_id INT(11) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (user_id, item_id),
                CONSTRAINT fk_buyer_list_user
                    FOREIGN KEY (user_id) REFERENCES 2300692_user(user_id)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT fk_buyer_list_item
                    FOREIGN KEY (item_id) REFERENCES 2300692_item(item_id)
                    ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
    }

    function hasItem($userId, $itemId){
        $stmt = $this->connect->prepare("
            SELECT 1
            FROM 2300692_buyer_list
            WHERE user_id = ? AND item_id = ?
        ");
        $stmt->bind_param("ii", $userId, $itemId);
        $stmt->execute();
        return (bool) $stmt->get_result()->fetch_assoc();
    }

    function addItem($userId, $itemId){
        $stmt = $this->connect->prepare("
            INSERT IGNORE INTO 2300692_buyer_list (user_id, item_id)
            VALUES (?, ?)
        ");
        $stmt->bind_param("ii", $userId, $itemId);
        return $stmt->execute();
    }

    function removeItem($userId, $itemId){
        $stmt = $this->connect->prepare("
            DELETE FROM 2300692_buyer_list
            WHERE user_id = ? AND item_id = ?
        ");
        $stmt->bind_param("ii", $userId, $itemId);
        return $stmt->execute();
    }

    function getItemsByUser($userId){
        $stmt = $this->connect->prepare("
            SELECT i.*
            FROM 2300692_buyer_list bl
            INNER JOIN 2300692_item i ON i.item_id = bl.item_id
            WHERE bl.user_id = ?
            ORDER BY bl.created_at DESC
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
