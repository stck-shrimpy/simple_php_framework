<?php require_once(HTML_DIR . '/user/partials/header.php') ?>

<div class="contents">
  <?php require_once(HTML_DIR . '/partials/error.php') ?>
  <?php if ($password_verified) : ?>
    <form class="edit" action="<?php echo get_uri('user/bulletin/edit.php') ?>" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="post_id" value="<?php echo h($id) ?>">
      <input type="hidden" name="password" value="<?php echo h($password) ?>">
      <input type="hidden" name="page" value="<?php echo h($page) ?>">
      <input type="hidden" name="do_edit" value="true">
      <label for="name">名前</label>
      <input id="name" name="name" type="text" value="<?php echo h($name) ?>">
      <label for="title">タイトル</label>
      <input id="title" name="title" type="text" placeholder="必ず入力してください！" value="<?php echo h($title) ?>">
      <label for="content">投稿内容</label>
      <textarea id="content" name="content" cols="30" rows="10" placeholder="必ず入力してください！"><?php echo h($content) ?></textarea>
      <div class="image">
        <?php if (!empty($post['image'])) : ?>
          <img src="<?php echo get_uri("{$image_dir}/{$post['image']}") ?>">
          <span>画像削除:</span>
          <input type="checkbox" name="do_delete_image" value="true">
        <?php endif ?>
        <input name="image" type="file">
      </div>
      <a href="<?php echo get_uri('index.php', ['page' => $page]) ?>"><button class="prev btn" type="button">戻る</button></a>
      <input class="edit btn" type="submit" value="修正">
    </form>
  <?php else : ?>
    <?php if (empty($post['user_id'])) : ?>
      <h3><?php echo h($post['title']) ?> (非会員)</h3>
    <?php else : ?>
      <h3><?php echo h($post['title']) ?> (会員) [ID: <?php echo h($post['user_id']) ?> ]</h3>
    <?php endif ?>

    <p><?php echo nl2br(h($post['content'])) ?></p>

    <?php if (!empty($post['image'])) : ?>
      <img src="<?php echo get_uri("{$image_dir}/{$post['image']}") ?>">
    <?php endif ?>

    <small><?php echo h($post['created_at']) ?></small>
    <hr>
    <a href="<?php echo get_uri('index.php', ['page' => $page]) ?>"><button class="prev btn" type="button">戻る</button></a>
  <?php endif ?>
</div>

<?php require_once(HTML_DIR . '/user/partials/footer.php') ?>

