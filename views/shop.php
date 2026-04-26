<?php include __DIR__ . "/header.php"; ?>

<h1>SHOP</h1>

<?php
$current_id = $product['item_id'] ?? 0;

include __DIR__ . "/content.php";
?>
<?php include __DIR__ . "/footer.php"; ?>
