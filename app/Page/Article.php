<?php

namespace App\Page;

class Article extends DBPage
{
    protected $title;
    protected $description;

    protected $content;
    protected $image;
    protected $views;
    protected $categories;

    public function __construct($data, IArticleDB $db, $layout, $template, $code = 200) {

        parent::__construct($data, $db, $layout, $template, $code);

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

        $this->layout->setVar("article", $this);
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

        $tags = $this->categories;
        $ids = [];

        foreach ($tags as $tag) {
            $categories[] = $this->db->getCategoryByTitle($tag);
        }

        return $categories;
    }

    public function getLink()
    {
        return "/article/" . $this->id;
    }

    public function getSimilarArticles()
    {
        return $this->db->getSimilarArticles($this->getId(), 3);
    }

}
