<?php
require_once "BaseModel.php";

class CategoryModel extends BaseModel {

    function getAll(){
        $stmt = $this->connect->prepare("
            SELECT * FROM 2300692_category ORDER BY category_name ASC
        ");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function getByItem($itemId){
        $stmt = $this->connect->prepare("
            SELECT c.*
            FROM 2300692_category c
            INNER JOIN 2300692_item_category ic ON c.category_id = ic.category_id
            WHERE ic.item_id = ?
            ORDER BY c.category_name ASC
        ");
        $stmt->bind_param("i", $itemId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function deleteByItem($itemId){
        $stmt = $this->connect->prepare("
            DELETE FROM 2300692_item_category WHERE item_id = ?
        ");
        $stmt->bind_param("i", $itemId);
        return $stmt->execute();
    }

    function assignToItem($itemId, $categoryId, $userId){
        $stmt = $this->connect->prepare("
            INSERT INTO 2300692_item_category (item_id, category_id, user_id)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iii", $itemId, $categoryId, $userId);
        return $stmt->execute();
    }
}
