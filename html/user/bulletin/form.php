<?php include(HTML_DIR . '/partials/error.php') ?>

<div class="form">
  <form action="<?php echo get_uri((isset($do_delete_image)) ? 'user/bulletin/edit.php' : 'user/bulletin/create.php') ?>" method="POST" enctype="multipart/form-data">
    <label for="name">名前</label>
    <input id="name" name="name" type="text" placeholder="必ず入力してください!" value="<?php echo h((isset($name)) ? $name : '') ?>">
    <label for="title">タイトル</label>
    <input id="title" name="title" type="text" placeholder="必ず入力してください！" value="<?php echo h((isset($title)) ? $title : '') ?>">
    <label for="content">投稿内容</label>
    <textarea id="content" name="content" cols="30" rows="10" placeholder="必ず入力してください！"><?php echo h((isset($content)) ? $content : '') ?></textarea>
    <?php if (!$is_logged_in) : ?>
      <label for="password">パスワード</label>
      <input id="password" name="password" type="text">
    <?php endif ?>
    <input name="image" type="file">
    <input class="form btn" type="submit" value="投稿">
  </form>
</div>
