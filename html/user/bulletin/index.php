<?php require_once(HTML_DIR . '/user/partials/header.php') ?>

<div class="contents">
  <?php require_once(HTML_DIR . '/user/bulletin/form.php') ?>
  <div class="posts">
    <?php foreach ($posts as $post) : ?>
      <div class="post">
        <?php if (isset($post['user_id'])) : ?>
          <h3><?php echo h((isset($post['name'])) ? $post['name'] : '名無し') ?> (会員) [ID: <?php echo h($post['user_id']) ?>]</h3>
        <?php else : ?>
          <h3><?php echo h((isset($post['name'])) ? $post['name'] : '名無し') ?> (非会員)</h3>
        <?php endif ?>
        <p><?php echo h($post['title']) ?></p>
        <p><?php echo nl2br(h($post['content'])) ?></p>

        <?php if (($post['image']) !== null) : ?>
          <img src="<?php echo get_uri("{$image_dir}/{$post['image']}") ?>">
        <?php endif ?>

        <small><?php echo h($post['created_at']) ?></small>
        <form class="delete" method="POST">
          <input type="hidden" name="post_id" value="<?php echo h($post['id']) ?>">
          <input type="hidden" name="page" value="<?php echo h($paginator->getCurrentPage()) ?>">
          <?php if ($is_logged_in) : ?>
            <?php if (isset($post['user_id']) && $post['user_id'] === $user['id']) : ?>
              <input class="delete btn" formaction="<?php echo get_uri('user/bulletin/delete.php') ?>" type="submit" value="削除">
              <input class="edit btn" formaction="<?php echo get_uri('user/bulletin/edit.php') ?>" type="submit" value="修正">
            <?php endif ?>
          <?php else : ?>
            <?php if (!isset($post['user_id'])) : ?>
              <input type="text" name="password">
              <input class="delete btn" formaction="<?php echo get_uri('user/bulletin/delete.php') ?>" type="submit" value="削除">
              <input class="edit btn" formaction="<?php echo get_uri('user/bulletin/edit.php') ?>" type="submit" value="修正">
            <?php endif ?>
          <?php endif ?>
        </form>
        <hr>
      </div>
    <?php endforeach ?>
  </div>
  <?php require_once(HTML_DIR . '/partials/paginator.php') ?>
</div>

<?php require_once(HTML_DIR . '/user/partials/footer.php') ?>
