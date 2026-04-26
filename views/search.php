<?php include __DIR__ . "/header.php"; ?>

<h1 class="page-title">Searched for "<?php echo htmlspecialchars($_GET['query']); ?>"</h1>

<div class="Content" id="Content">
    <?php if (!empty($results)): ?>
        <?php foreach ($results as $index => $front): ?>
            <div class="item">
                <?php $imageSources = $this->getImageSources($front["pic_loc"]); ?>
                <a href="index.php?url=chair/<?= $front['item_id'] ?>">
                    <picture>
                        <?php if (!empty($imageSources['webp'])): ?>
                            <source srcset="<?= htmlspecialchars($imageSources['webp']) ?>" type="image/webp">
                        <?php endif; ?>
                        <img class="img-thumb"
                            loading="<?= $index < 2 ? 'eager' : 'lazy' ?>"
                            fetchpriority="<?= $index === 0 ? 'high' : 'auto' ?>"
                            decoding="async"
                            width="640"
                            height="640"
                            sizes="(max-width: 640px) 100vw, (max-width: 1180px) 50vw, 280px"
                            src="<?= htmlspecialchars($imageSources['src']) ?>" 
                            alt="<?= htmlspecialchars($front["item"]) ?>">
                    </picture>
                    <h2><?= htmlspecialchars($front['item']) ?></h2>
                    <p><?= htmlspecialchars($front['short_desc']) ?></p>
                </a>
                <a class="check" href="index.php?url=chair/<?= $front['item_id'] ?>">View</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="empty-state">No results found.</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . "/footer.php"; ?>
