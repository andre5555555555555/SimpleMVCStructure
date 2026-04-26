<?php
require_once "BaseModel.php";

class PictureModel extends BaseModel {

    function insertPictures($item_id, $pictures){
        $stmt = $this->connect->prepare("
            INSERT INTO 2300692_picture (item_id, pic_loc, front, display, user_id)
            VALUES (?, ?, ?, 'yes', ?)
        ");

        foreach($pictures as $pic){
            $stmt->bind_param("issi", $item_id, $pic[0], $pic[1], $pic[2]);
            $stmt->execute();
        }
    }

    function getByItem($id){
        $stmt = $this->connect->prepare("
            SELECT * FROM 2300692_picture WHERE item_id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function deleteByItem($id){
        $stmt = $this->connect->prepare("
            DELETE FROM 2300692_picture WHERE item_id = ?
        ");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
