<?php

namespace App\Page\Param;

class Pagination
{
    protected $current;
    protected $total;
    protected $limit;
    protected $param;
    protected $filters = [];
    protected $link;

    public function __construct($data)
    {
        if (isset($data['total'])) {
            $this->total = (int) $data['total'];
        }

        if (isset($data['limit'])) {
            $this->limit = (int) $data['limit'];
        }

        if (isset($data['param'])) {
            $this->param = $data['param'];
        }

        if (isset($data['filters'])) {
            $this->filters = $data['filters'];
        }

        if (isset($data['link'])) {
            $this->link = $data['link'];
        }

        $this->current = filter_input(INPUT_GET, $this->param, FILTER_VALIDATE_INT) ?: 1;

    }

    public function getCurrentPageNo() {
        return $this->current;
    }

    public function toHtml() {
        ob_start();
        include "../html/php/partials/pagination/simple.php";
        return ob_get_clean();
    }

    public function getTotalPages(): int
    {
        return (int) ceil($this->total / $this->limit);
    }

    public function getPages(): array
    {
        $totalPages = $this->getTotalPages();
        $pages = [];
        $params = [];

        foreach ($this->filters as $filter) {
            $val = filter_input(INPUT_GET, $filter, FILTER_SANITIZE_SPECIAL_CHARS);
            if ($val !== null && $val !== false) {
                $params[$filter] = $val;
            }
        }

        $sidePages = 4;

        for ($i = 1; $i <= $totalPages; $i++) {

            if (
                $i == 1 ||
                $i == $totalPages ||
                ($i >= $this->current - $sidePages && $i <= $this->current + $sidePages)
            ) {

                $params[$this->param] = $i;

                if ($i == 1) {
                    unset($params[$this->param]);
                }

                $link = (!count($params)) ? $this->link : $this->link . '?' . http_build_query($params);

                $pages[] = [
                    'index' => $i,
                    'link' => $link,
                    'current' => ($i === $this->current),
                    'separator' => false
                ];
            } elseif (
                ($i == 2 && $this->current - $sidePages > 2) ||
                ($i == $totalPages - 1 && $this->current + $sidePages < $totalPages - 1)
            ) {
                $pages[] = [
                    'index' => '...',
                    'link' => null,
                    'current' => false,
                    'separator' => true
                ];
            }
        }

        return $pages;
    }

    public function getOffset(): int
    {
        return ($this->current - 1) * $this->limit;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}