<?php require_once(HTML_DIR . '/partials/header.php') ?>

<div id="contents">
  <h2>400 Bad Request</h2>
  <p>要求形式が正しくありません</p>
  <?php if (!empty($message)) : ?>
    <strong><?php echo $message ?></strong>
  <?php endif ?>
</div>

<?php require_once(HTML_DIR . '/partials/footer.php') ?>
