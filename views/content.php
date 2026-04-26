<div class="Content<?= !empty($contentClass) ? ' ' . htmlspecialchars($contentClass) : '' ?>" id="Content">
    <?php if(!empty($chairs)): ?>
        <?php foreach($chairs as $index => $chair): ?>
            <?php if($chair['item_id'] == ($current_id ?? 0)) continue; ?>
            <div class="item">
                <?php $imageSources = $this->getImageSources($chair["pic_loc"]); ?>
                <a href="index.php?url=chair/<?= $chair['item_id'] ?>">
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
                             alt="<?= htmlspecialchars($chair["item"]) ?>">
                    </picture>
                    <h2><?= htmlspecialchars($chair["item"]) ?></h2>
                    <p>Php <?= htmlspecialchars($chair["price"]) ?></p>
                    <p><?= htmlspecialchars($chair["short_desc"]) ?></p>
                </a>
                <a class="check" href="index.php?url=chair/<?= $chair['item_id'] ?>">View</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="empty-state">No chairs available.</p>
    <?php endif; ?>
</div>
