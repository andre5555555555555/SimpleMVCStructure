<?php
require_once "BaseModel.php";

class ItemModel extends BaseModel {

    function getAll(){
        $stmt = $this->connect->prepare("
            SELECT * FROM 2300692_item
        ");
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    function search($search){
    $stmt = $this->connect->prepare("
        SELECT * FROM 2300692_item
        WHERE item LIKE ?
    ");

    $like = "%$search%";
    $stmt->bind_param("s", $like);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

    function getById($id){
        $stmt = $this->connect->prepare("
            SELECT * FROM 2300692_item WHERE item_id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    function insert($data){
        $stmt = $this->connect->prepare("
            INSERT INTO 2300692_item (item, price, short_desc, description, user_id)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sissi",
            $data["item"],
            $data["price"],
            $data["short_desc"],
            $data["description"],
            $data["user_id"]
        );
        $stmt->execute();

        return $this->connect->insert_id;
    }

    function update($data, $id){
        $stmt = $this->connect->prepare("
            UPDATE 2300692_item
            SET item=?, price=?, short_desc=?, description=?
            WHERE item_id=?
        ");
        $stmt->bind_param("sissi",
            $data["item"],
            $data["price"],
            $data["short_desc"],
            $data["description"],
            $id
        );
        return $stmt->execute();
    }

    function delete($id){
        $stmt = $this->connect->prepare("
            DELETE FROM 2300692_item WHERE item_id=?
        ");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
