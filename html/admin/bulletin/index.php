<?php require_once(HTML_DIR . '/admin/partials/header.php') ?>

<div class="contents">
  <div class="posts">
    <form method="GET" id="admin-search" action="<?php echo get_uri('admin/bulletin/index.php') ?>">
      <label for="title">タイトル</label>
      <input id="title" type="search" name="title" value="<?php echo h((isset($title)) ? $title : '') ?>">
      <label for="content">投稿内容</label>
      <input id="content" type="search" name="content" value="<?php echo h((isset($content)) ? $content : '') ?>">
      <label for="image-option">画像</label>
      <input type="radio" name="image_option" value="with"
        <?php echo (isset($image_option) && $image_option === 'with') ? 'checked="checked"' : '' ?>
      >含む
      <input type="radio" name="image_option" value="without"
        <?php echo (isset($image_option) && $image_option === 'without') ? 'checked="checked"' : '' ?>
      >含まない
      <input type="radio" name="image_option" value="unspecified"
        <?php echo (isset($image_option) && $image_option === 'unspecified') ? 'checked="checked"' : '' ?>
      >指定なし
      <label>投稿状態</label>
      <input type="radio" name="status_option" value="available"
        <?php echo (isset($status_option) && $status_option === 'available') ? 'checked="checked"' : '' ?>
      >あり
      <input type="radio" name="status_option" value="deleted"
        <?php echo (isset($status_option) && $status_option === 'deleted') ? 'checked="checked"' : '' ?>
      >削除済
      <input type="radio" name="status_option" value="unspecified"
        <?php echo (isset($status_option) && $status_option === 'unspecified') ? 'checked="checked"' : '' ?>
      >指定なし
      <input type="submit" value="検索">
    </form>
    <form method="POST" action="<?php echo get_uri('admin/bulletin/delete.php') ?>">
      <table>
        <tr>
          <th>
            <input id="js-check-all" type="checkbox">
          </th>
          <th>ID</th>
          <th>タイトル</th>
          <th>投稿内容</th>
          <th>画像</th>
          <th>日付</th>
          <th></th>
        </tr>
        <?php foreach ($posts as $post) : ?>
          <tr <?php echo h(($post['deleted_at'] !== null) ? 'class=deleted-post' : '') ?>>
            <td>
              <?php if ($post['deleted_at'] === null) : ?>
                <input class="admin-btn" type="checkbox" name="post_ids[]" value="<?php echo h($post['id']) ?>">
              <?php endif ?>
            </td>
            <td><?php echo h($post['id']) ?></td>
            <td><?php echo h($post['title']) ?></td>
            <td><?php echo h($post['content']) ?></td>
            <td>
              <?php if ($post['deleted_at'] === null && $post['image'] !== null) : ?>
                <img src="<?php echo get_uri("{$image_dir}/{$post['image']}") ?>">
                <a class="js-delete-image-btn" post_id="<?php echo h($post['id']) ?>" form_action="<?php echo get_uri('admin/bulletin/deleteImage.php') ?>">削除</a>
              <?php endif ?>
            </td>
            <td><?php echo h($post['created_at']) ?></td>
            <td>
              <?php if ($post['deleted_at'] === null) : ?>
                <a class="js-delete-one-btn" post_id="<?php echo h($post['id']) ?>" form_action="<?php echo get_uri('admin/bulletin/delete.php') ?>">削除</a>
              <?php else : ?>
                <a class="js-recover-one-btn" post_id="<?php echo h($post['id']) ?>" form_action="<?php echo get_uri('admin/bulletin/recover.php') ?>">復旧</a>
              <?php endif ?>
            </td>
          </tr>
        <?php endforeach ?>
      </table>
      <input class="admin-btn" type="submit" id="js-delete-many-btn" value="選択した投稿を削除">
    </form>
    <form id="js-hidden-form" method="POST">
      <input id="js-hidden-input" type="hidden">
    </form>
  <?php require_once(HTML_DIR . '/partials/paginator.php') ?>
</div>

<?php require_once(HTML_DIR . '/admin/partials/footer.php') ?>
