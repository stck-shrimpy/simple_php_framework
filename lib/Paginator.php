<?php

namespace lib;
class Paginator
{
    protected $page_uri            = '/';
    protected $params              = [];
    protected $item_count_per_page = 10;
    protected $link_count          = 5;
    protected $current_page        = 1;
    protected $item_count          = 0;

    public function __construct(int $item_count)
    {
        $this->item_count = max($item_count, 0);
    }

    public function setCurrentPage($page)
    {
        $current_page       = max((int) $page, 1);
        $this->current_page = min($current_page, $this->getMaxPage());
    }

    public function getCurrentPage(): int
    {
        return $this->current_page;
    }

    public function setItemCountPerPage(int $item_count_per_page)
    {
        $this->item_count_per_page = $item_count_per_page;
        $this->setCurrentPage($this->current_page);
    }

    public function getItemCountPerPage(): int
    {
        return $this->item_count_per_page;
    }

    public function setLinkCount(int $link_count)
    {
        $this->link_count = $link_count;
    }

    public function getLinkCount(): int
    {
        return $this->link_count;
    }

    public function getMaxPage(): int
    {
        return ($this->item_count > 0) ? (int) ceil($this->item_count / $this->item_count_per_page) : 1;
    }

    public function getPageNumbers(): array
    {
        $max        = $this->getMaxPage();
        $link_count = $this->link_count;
        $current    = $this->current_page;
        $middle     = (int) ceil($this->link_count / 2);
        if ($current <= $middle) {
            $start = 1;
            $end   = min($max, $this->link_count);
        } else {
            $end   = min($max, $current + ($link_count - $middle));
            $start = max(1, $end - ($link_count - 1));
        }

        $page_numbers = [];
        for ($i = $start; $i <= $end; $i++) {
            $page_numbers[] = $i;
        }

        return $page_numbers;
    }

    public function setUri(string $uri, array $params = [])
    {
        $parsed = parse_url($uri);

        if (isset($parsed['path'])) {
          $this->pageUri = $parsed['path'];
        }

        if (isset($parsed['query'])) {
          parse_str($parsed['query'], $_params);
          $params = array_merge($_params, $params);
        }

        $this->setParams($params);
    }

    public function setParams(array $params)
    {
        $this->params = $params;
    }

    public function createUri(int $page = 1): string
    {
        $params = $this->params;

        if (empty($page)) {
          unset($params['page']);
        } else {
          $params['page'] = $page;
        }

        if (empty($params)) {
          return $this->pageUri;
        } else {
          return $this->pageUri . '?' . http_build_query($params, '', '&');
        }
    }

    public function getOffset(): int
    {
        return ($this->current_page - 1) * $this->item_count_per_page;
    }

    public function getNextPage(): int
    {
        $max_page = $this->getMaxPage();
        return ($this->current_page < $max_page) ? $this->current_page + 1 : $max_page;
    }

    public function getPreviousPage(): int
    {
        return ($this->current_page > 1) ? $this->current_page - 1 : 1;
    }

    public function isFirstPage(): bool
    {
        return ($this->current_page === 1);
    }

    public function isLastPage(): bool
    {
        return ($this->current_page === $this->getMaxPage());
    }
}
