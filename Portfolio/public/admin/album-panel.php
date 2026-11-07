<?php $albumsForPanel = albums_data(); ?>
<section class="admin-panel" id="albums">
  <div class="panel-title"><span class="section-kicker">06 / MUSIC COLLECTION</span><h2>Album covers <span><?= count($albumsForPanel) ?>/3</span></h2></div>
  <p class="album-admin-help">Add up to three square cover images. The album name appears under each cover on your public homepage. To replace a cover, remove it and add it again.</p>
  <div class="album-admin-grid">
    <?php for ($slot = 0; $slot < 3; $slot++): $album = $albumsForPanel[$slot] ?? null; ?>
      <article class="album-admin-card">
        <?php if ($album && ($cover = public_asset((string)($album['image'] ?? '')))): ?>
          <img src="<?= h($cover) ?>" alt="<?= h($album['alt'] ?? ('Album cover for ' . $album['title'])) ?>">
          <strong><?= h($album['title']) ?></strong>
          <form method="post" action="/admin/save.php" data-confirm="Remove this album from the public music section?">
            <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>"><input type="hidden" name="action" value="delete-album"><input type="hidden" name="id" value="<?= h($album['id']) ?>">
            <button class="danger-link" type="submit">REMOVE ALBUM</button>
          </form>
        <?php else: ?>
          <div class="album-admin-empty"><span>ALBUM COVER</span><b>0<?= $slot + 1 ?></b></div><strong>OPEN SLOT</strong>
        <?php endif; ?>
      </article>
    <?php endfor; ?>
  </div>
  <?php if (count($albumsForPanel) < 3): ?>
    <form class="album-add-form" method="post" action="/admin/save.php" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>"><input type="hidden" name="action" value="add-album">
      <?php field('Album name','album_title'); field('Image description (optional)','album_alt'); ?>
      <label>Square album cover<input type="file" name="album_image" accept="image/jpeg,image/png,image/webp" required><small>JPG, PNG, or WebP · max 5 MB. Square artwork is recommended.</small></label>
      <button class="admin-button" type="submit">ADD ALBUM ↗</button>
    </form>
  <?php else: ?>
    <p class="album-admin-help">All three slots are in use. Remove an album to add a different one.</p>
  <?php endif; ?>
</section>
