<?php require_once(HTML_DIR . '/user/partials/header.php') ?>

<div class="contents">
  <div class="registration">
    <?php include(HTML_DIR . '/partials/error.php') ?>
    <form action="<?php echo get_uri('user/auth/login.php') ?>" method="POST" enctype="multipart/form-data">
        <label>メールアドレス</label>
        <input name="email" type="text" placeholder="必ず入力してください！" value="<?php echo h((isset($email)) ? $email : '') ?>">
      <label>パスワード</label>
      <input name="password" type="password">
      <input class="form btn" type="submit" value="ログイン"">
    </form>
    <a href="<?php echo get_uri('index.php') ?>"><button class="prev btn" type="button">戻る</button></a>
  </div>
</div>

<?php require_once(HTML_DIR . '/user/partials/footer.php') ?>
