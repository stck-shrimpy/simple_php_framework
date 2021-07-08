<?php require_once(HTML_DIR . '/partials/header.php') ?>

<div id="contents">
  <h2>404 Not Found </h2>
  <p>ページが見つかりません</p>
  <?php if (!empty($message)) : ?>
    <strong><?php echo $message ?></strong>
  <?php endif ?>
</div>

<?php require_once(HTML_DIR . '/partials/footer.php') ?>
