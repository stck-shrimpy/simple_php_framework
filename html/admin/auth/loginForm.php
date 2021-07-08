<?php require_once(HTML_DIR . '/admin/partials/header.php') ?>

<div class="contents">
  <div class="admin">
    <?php include(HTML_DIR . '/partials/error.php') ?>
    <form action="<?php echo get_uri('admin/auth/login.php') ?>" method="POST" enctype="multipart/form-data">
      <label for="admin-id">ID</label>
      <input id="admin-id" name="login_id" type="text" placeholder="必ず入力してください！" value="<?php echo h((isset($admin_id)) ? $admin_id : '') ?>">
      <label for="password">パスワード</label>
      <input id="password" name="password" type="password">
      <input class="form btn" type="submit" value="ログイン"">
    </form>
  </div>
</div>

<?php require_once(HTML_DIR . '/admin/partials/footer.php') ?>
