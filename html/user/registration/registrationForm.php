<?php require_once(HTML_DIR . '/user/partials/header.php') ?>

<div class="contents">
  <?php if ($is_registered) : ?>
    <h3>ようこそ！<?php echo h($user_name) ?>さん</h3>
    <p>会員登録ありがとうございます。<br>登録用リンクをメールアドレスに送信しました。<br>リンクを開いて会員登録を完了してください。</p>
    <a href="<?php echo get_uri('index.php') ?>"><button class="prev btn" type="button">ホーム画面に戻る</button></a>
  <?php else : ?>
    <div class="registration">
      <?php include(HTML_DIR . '/partials/error.php') ?>
      <?php if ($is_register_confirm) : ?>
        <p>名前: <?php echo h($user_name) ?></p>
        <p>メールアドレス: <?php echo h($email) ?></p>
        <p>パスワード: <?php echo h($password) ?></p>
        <form action="<?php echo get_uri('user/registration/register.php') ?>" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="user_name" value="<?php echo h($user_name) ?>">
          <input type="hidden" name="email" value="<?php echo h($email) ?>">
          <input type="hidden" name="password" value="<?php echo h($password) ?>">
          <input type="hidden" name="do_register" value="true">
          <input class="btn" type="submit" value="登録">
        </form>
        <a href="<?php echo get_uri('index.php') ?>"><button class="prev btn" type="button">戻る</button></a>
      <?php else : ?>
        <form action="<?php echo get_uri('user/registration/register.php') ?>" method="POST" enctype="multipart/form-data">
          <label>名前</label>
          <input name="user_name" type="text" placeholder="必ず入力してください！" value="<?php echo h((isset($user_name)) ? $user_name : '') ?>">
          <label>メールアドレス</label>
          <input name="email" type="text" placeholder="必ず入力してください！" value="<?php echo h((isset($email)) ? $email : '') ?>">
          <label>パスワード</label>
          <input name="password" type="text">
          <input class="form btn" type="submit" value="会員登録"">
        </form>
      <?php endif ?>
    </div>
  <?php endif ?>
</div>

<?php require_once(HTML_DIR . '/user/partials/footer.php') ?>
