<?php require_once(HTML_DIR . '/user/partials/header.php') ?>

<div class="contents">
  <div class="registration">
    <?php include(HTML_DIR . '/partials/error.php') ?>
    <?php if ($is_email_verified) : ?>
      <h3>ようこそ、cho_bbsへ!</h3>
      <p>メールアドレスの認証が完了しました</p>
    <?php endif ?>
    <a href="<?php echo get_uri('index.php') ?>"><button class="prev btn" type="button">戻る</button></a>
  </div>
</div>

<?php require_once(HTML_DIR . '/user/partials/footer.php') ?>
