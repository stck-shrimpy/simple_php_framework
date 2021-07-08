<?php if ($paginator->getMaxPage() > 1) : ?>
  <div class="pagination">
    <?php if (!$paginator->isFirstPage()) : ?>
      <a class="first-page-btn" href="<?php echo h($paginator->createUri(1)) ?>">&laquo;</a>
      <a class="previous-btn" href="<?php echo h($paginator->createUri($paginator->getPreviousPage())) ?>">&lt;</a>
    <?php endif ?>
    <?php foreach ($paginator->getPageNumbers() as $page_number) : ?>
      <?php if ($paginator->getCurrentPage() === $page_number) : ?>
        <a class="active"><?php echo h($page_number) ?></a>
      <?php else : ?>
        <a href="<?php echo h($paginator->createUri($page_number)) ?>"><?php echo h($page_number) ?></a>
      <?php endif ?>
    <?php endforeach ?>
    <?php if (!$paginator->isLastPage()) : ?>
      <a class="next-btn" href="<?php echo h($paginator->createUri($paginator->getNextPage())) ?>">&gt;</a>
      <a class="last-page-btn" href="<?php echo h($paginator->createUri($paginator->getMaxPage())) ?>">&raquo;</a>
    <?php endif ?>
  </div>
<?php endif ?>
