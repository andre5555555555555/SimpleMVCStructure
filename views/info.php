<?php include __DIR__ . "/header.php"; ?>

<div class="Content-info">

<?php if (!empty($chair)): ?>
    <?php $product = $chair; ?>

    <div class="item-info">


        <div class="image-info">
            <?php
            $count = 0;
            if (!empty($pictures)) {
                foreach ($pictures as $img) {
                    if (!empty($img['pic_loc']) && $count < 4) {
                        $loading = $count === 0 ? 'eager' : 'lazy';
                        $fetchPriority = $count === 0 ? 'high' : 'auto';
                        $imageSources = $this->getImageSources($img['pic_loc']);
                        echo '<picture>';
                        if (!empty($imageSources['webp'])) {
                            echo '<source srcset="' . htmlspecialchars($imageSources['webp']) . '" type="image/webp">';
                        }
                        echo '<img src="' . htmlspecialchars($imageSources['src']) . '" alt="' . htmlspecialchars($product['item']) . '" loading="' . $loading . '" fetchpriority="' . $fetchPriority . '" decoding="async" width="900" height="900">';
                        echo '</picture>';
                        $count++;
                    }
                }
            }
            ?>
        </div>
        <div class="description">
            <h1><?= htmlspecialchars($product['item']) ?></h1>
            <p>PHP <?= htmlspecialchars($product['price']) ?></p>

            <?php if (!empty($categories)): ?>
                <div class="category-list">
                    <?php foreach ($categories as $category): ?>
                        <span class="category-badge"><?= htmlspecialchars($category['category_name']) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <p id="subtag">DESCRIPTION</p>
            <p><?= htmlspecialchars($product['description']) ?></p>

            <?php if (!empty($_SESSION['user']) && (int) $_SESSION['user']['role_id'] === 2): ?>
                <?php if (!empty($isInBuyerList)): ?>
                    <form method="POST" action="index.php?url=remove-from-list" style="display:inline">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($this->generateCSRFToken('buyer_list')) ?>">
                        <input type="hidden" name="item_id" value="<?= $product['item_id'] ?>">
                        <button type="submit" class="buy">Remove From My List</button>
                    </form>
                <?php else: ?>
                    <form method="POST" action="index.php?url=add-to-list" style="display:inline">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($this->generateCSRFToken('buyer_list')) ?>">
                        <input type="hidden" name="item_id" value="<?= $product['item_id'] ?>">
                        <button type="submit" class="buy">Add To My List</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (!empty($canManageItem)): ?>
                <form method="POST" action="index.php?url=delete" style="display:inline" onsubmit="return confirm('Are you sure?');">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($this->generateCSRFToken('delete_item')) ?>">
                    <input type="hidden" name="item_id" value="<?= $product['item_id'] ?>">
                    <button type="submit" class="buy">Delete</button>
                </form>

                <a class="buy" href="index.php?url=edit/<?= $product['item_id'] ?>">Edit</a>
            <?php endif; ?>
        </div>

    </div>

<?php else: ?>
    <p class="empty-state">Item not found.</p>
<?php endif; ?>

</div>

<h1 id="cut-similar">Similar items</h1>

<?php
$current_id = $product['item_id'] ?? 0;


include __DIR__ . "/content.php";
?>

<?php include __DIR__ . "/footer.php"; ?>
