<?php include "header.php"; ?>

<?php if (!empty($chair)): ?>
    <?php $product = $chair; ?>

    <h1 class="page-title">Edit Item</h1>

    <form class="upload" action="index.php?url=update/<?= $product['item_id'] ?>" method="POST" data-validate-form="item-edit" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
        <input type="hidden" name="id" value="<?= $product['item_id'] ?>">

        <?php if (!empty($formError)): ?>
            <p class="auth-message auth-message-error"><?= htmlspecialchars($formError) ?></p>
        <?php endif; ?>

        <label>Item</label>
        <input type="text" name="item_upload" value="<?= htmlspecialchars($product['item']) ?>" minlength="3" maxlength="255" data-label="Item Name" required>

        <label>Price</label>
        <input type="number" name="item_price" value="<?= htmlspecialchars((string) $product['price']) ?>" min="1" step="1" data-label="Price" required>

        <label>Short Description</label>
        <textarea name="short_desc" minlength="10" maxlength="255" data-label="Short Description" required><?= htmlspecialchars($product['short_desc']) ?></textarea>

        <?php if (!empty($categories)): ?>
            <label>Categories</label>
            <div class="category-group">
                <?php foreach ($categories as $category): ?>
                    <label class="category-option">
                        <input
                            type="checkbox"
                            name="categories[]"
                            value="<?= (int) $category['category_id'] ?>"
                            <?= in_array((int) $category['category_id'], array_map('intval', $selectedCategories ?? []), true) ? 'checked' : '' ?>
                        >
                        <span><?= htmlspecialchars($category['category_name']) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <label>Full Description</label>
        <textarea name="full_desc" minlength="20" data-label="Full Description" required><?= htmlspecialchars($product['description']) ?></textarea>

        <p class="auth-message auth-message-error validation-message" hidden></p>

        <button type="submit">SAVE</button>
    </form>

<?php else: ?>
    <p class="empty-state">Item not found.</p>
<?php endif; ?>

<?php include "footer.php"; ?>
