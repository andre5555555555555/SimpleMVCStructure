<?php include "header.php"; ?>

<h1 class="page-title">Upload a Gaming Chair</h1>

<form class="upload" action="index.php?url=insert" method="POST" enctype="multipart/form-data" data-validate-form="item-create" novalidate>
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

    <?php if (!empty($formError)): ?>
        <p class="auth-message auth-message-error"><?= htmlspecialchars($formError) ?></p>
    <?php endif; ?>

    <label>Front Image</label>
    <input type="file" name="item_front" accept=".jpg,.jpeg,.png,.webp" data-label="Front Image" required>

    <label>Right Image</label>
    <input type="file" name="item_right" accept=".jpg,.jpeg,.png,.webp" data-label="Right Image" required>

    <label>Left Image</label>
    <input type="file" name="item_left" accept=".jpg,.jpeg,.png,.webp" data-label="Left Image" required>

    <label>Back Image</label>
    <input type="file" name="item_back" accept=".jpg,.jpeg,.png,.webp" data-label="Back Image" required>

    <label>Item Name</label>
    <input type="text" name="item_upload" value="<?= htmlspecialchars($chair['item'] ?? '') ?>" placeholder="Gaming Chair" minlength="3" maxlength="255" data-label="Item Name" required>

    <label>Price</label>
    <input type="number" name="item_price" value="<?= htmlspecialchars((string) ($chair['price'] ?? '')) ?>" placeholder="99" min="1" step="1" data-label="Price" required>

    <label>Short Description</label>
    <textarea name="short_desc" placeholder="A cool new chair" minlength="10" maxlength="255" data-label="Short Description" required><?= htmlspecialchars($chair['short_desc'] ?? '') ?></textarea>

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
    <textarea name="full_desc" placeholder="Full description here..." minlength="20" data-label="Full Description" required><?= htmlspecialchars($chair['description'] ?? '') ?></textarea>

    <p class="auth-message auth-message-error validation-message" hidden></p>

    <button type="submit">SUBMIT</button>
</form>

<?php include "footer.php"; ?>
