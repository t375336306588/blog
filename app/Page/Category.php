<?php

namespace App\Page;

use App\Page\Param\Pagination;
use App\Page\Param\Order;

class Category extends DBPage
{
    protected $title;
    protected $description;
    protected $parentId;
    protected $pagination;
    protected $orderBy;
    protected $orderType;

    public function __construct($data, ICategoryDB $db, $layout, $template, $code = 200) {

        parent::__construct($data, $db, $layout, $template, $code);

        if (isset($data['title'])) {
            $this->title = trim($data['title']);
        }

        if (isset($data['description'])) {
            $this->description = trim($data['description']);
        }

        if (isset($data['parent_id'])) {
            $this->parentId = $data['parent_id'];
        }

        $this->pagination = new Pagination([
            "total" => $this->countArticles(),
            "limit" => 12,
            "param" => "p",
            "filters" => ["ob", "ot"],
            "link" => $this->getLink(),
        ]);

        $this->orderBy = new Order([
            "param" => "ob",
            "filters" => ["p", "ot"],
            "link" => $this->getLink(),
            "values" => [
                'p' => [
                    "label" => 'Published',
                    "sql" => 'a.created_at',
                ],
                'v' => [
                    "label" => 'Views',
                    "sql" => 'IFNULL(rv.number, 0)',
                ],
            ]
        ]);

        $this->orderType = new Order([
            "param" => "ot",
            "filters" => ["p", "ob"],
            "link" => $this->getLink(),
            "values" => [
                'd' => [
                    "label" => 'Descending',
                    "sql" => 'DESC',
                ],
                'a' => [
                    "label" => 'Ascending',
                    "sql" => 'ASC',
                ],
            ]
        ]);

        $this->layout->setVar("category", $this);

    }

    public function getTitle() {
        return $this->title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getChildren() {
        return $this->db->getChildrenCategories($this->getId());
    }

    public function getParent() {
        if (is_null($this->parentId)) {
            return null;
        }
        return $this->db->getCategory($this->getParentId());
    }

    public function getParentId() {
        return $this->parentId;
    }

    public function getArticles() {
        return $this->db->getCategoryArticles(
            $this->getId(),
            $this->getOrderBy()->toSQL(),
            $this->getOrderType()->toSQL(),
            $this->getPagination()->getOffset(),
            $this->getPagination()->getLimit()
        );
    }

    public function getLatestArticles(int $limit) {
        return $this->db->getLatestCategoryArticles($this->getId(), $limit);
    }

    public function getRandomArticles(int $limit) {
        return $this->db->getRandomCategoryArticles($this->getId(), $limit);
    }

    public function countArticles() {
        return $this->db->countCategoryArticles($this->getId());
    }

    public function getLink()
    {
        return "/category/" . $this->id;
    }

    public function getPagination() {
        return $this->pagination;
    }

    public function getOrderBy() {
        return $this->orderBy;
    }

    public function getOrderType() {
        return $this->orderType;
    }

}
