<?php if (isset($errors) && is_array($errors)) : ?>
  <?php foreach ($errors as $error) : ?>
    <p class="error"><?php echo h($error) ?></p>
  <?php endforeach ?>
<?php endif ?>
