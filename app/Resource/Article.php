<?php

namespace App\Resource;

class Article extends Resource
{
    protected $title;
    protected $description;

    protected $content;
    protected $image;
    protected $views;
    protected $categories;

    public function __construct($data) {
        parent::__construct($data);
        if (isset($data['title'])) {
            $this->title = trim($data['title']);
        }
        if (isset($data['description'])) {
            $this->description = trim($data['description']);
        }
        if (isset($data['content'])) {
            $this->content = trim($data['content']);
        }
        if (isset($data['image'])) {
            $this->image = trim($data['image']);
        }
        if (isset($data['views'])) {
            $this->views = (int) trim($data['views']);
        }
        if (isset($data['categories'])) {
            $this->categories = $data['categories'];
        }
    }

    public function getTitle() {
        return $this->title;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getContent() {
        return $this->content;
    }

    public function getImage() {
        if (is_null($this->image)) {
            return "/files/placeholder.png";
        }
        return $this->image;
    }

    public function getViews() {
        return $this->views;
    }

    public function getCategories() {
        return $this->categories;
    }

}
