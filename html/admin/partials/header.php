<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="<?php echo get_uri('css/styles.css') ?>">
  </head>
  <body>
    <main>
      <div class="admin">
        <?php if ($is_logged_in) : ?>
          <a href="<?php echo get_uri('admin/auth/logout.php') ?>">ログアウト</a>
        <?php endif ?>
      </div>
