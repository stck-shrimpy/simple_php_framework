<?php require_once(HTML_DIR . '/partials/header.php') ?>

<div id="contents">
  <h2>500 Internal Server Error</h2>
  <p>サーバーに問題があります</p>
  <?php if (!empty($message)) : ?>
    <strong><?php echo $message ?></strong>
  <?php endif ?>
</div>

<?php require_once(HTML_DIR . '/partials/footer.php') ?>
