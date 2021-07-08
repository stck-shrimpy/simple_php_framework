<?php require_once(HTML_DIR . '/user/partials/header.php') ?>

<div class="contents">
  <?php require_once(HTML_DIR . '/partials/error.php') ?>

  <?php if (empty($post['user_id'])) : ?>
    <h3><?php echo h($post['title']) ?> (非会員)</h3>
  <?php else : ?>
    <h3><?php echo h($post['title']) ?> (会員) [ID: <?php echo h($post['user_id']) ?> ]</h3>
  <?php endif ?>

  <p><?php echo nl2br(h($post['content'])) ?></p>

  <?php if ($post['image']) : ?>
    <img src="<?php echo h(get_uri("{$image_dir}/{$post['image']}")) ?>">
  <?php endif ?>

  <small><?php echo h($post['created_at']) ?></small>
  <hr>

  <?php if ($password_verified) : ?>
    <p>本当に削除しますか？</p>
    <form class="delete" action="<?php echo get_uri('user/bulletin/delete.php') ?>" method="POST">
      <input type="hidden" name="post_id" value="<?php echo h($id) ?>">
      <input type="hidden" name="password" value="<?php echo h($password) ?>">
      <input type="hidden" name="page" value="<?php echo h($page) ?>">
      <input type="hidden" name="do_delete" value="true">
      <input class="delete btn" type="submit" value="削除">
    </form>
  <?php elseif (!empty($post['password']) && !$password_verified) : ?>
    <form class="delete" action="<?php echo get_uri('user/bulletin/delete.php', ['page' => $page]) ?>" method="POST">
      <input type="hidden" name="post_id" value="<?php echo h($id) ?>">
      <input type="hidden" name="page" value="<?php echo h($page) ?>">
      <input type="text" name="password">
      <input class="delete btn" type="submit" value="削除">
    </form>
  <?php endif ?>
  <a href="<?php echo get_uri('index.php', ['page' => $page]) ?>"><button class="prev btn" type="button">戻る</button></a>
</div>

<?php require_once(HTML_DIR . '/user/partials/footer.php') ?>
