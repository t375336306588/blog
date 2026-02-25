<?php

namespace App\Resource;

use App\DB\IResourceCategories;

class Category extends Resource
{
    protected $title;
    protected $description;
    protected $parentId;
    protected $db;

    public function __construct($data, IResourceCategories $db) {
        parent::__construct($data);
        if (isset($data['title'])) {
            $this->title = trim($data['title']);
        }
        if (isset($data['description'])) {
            $this->description = trim($data['description']);
        }
        if (isset($data['parent_id'])) {
            $this->parentId = $data['parent_id'];
        }
        $this->db = $db;
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
        return $this->db->getParentCategory($this->getId());
    }

    public function getParentId() {
        return $this->parentId;
    }

    public function getLatestArticles($limit) {
        return $this->db->getCategoryLatestArticles($this->getId(), $limit);
    }

}
