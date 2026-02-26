<?php

namespace App\DB;

use App\Page\IArticleDB;
use App\Page\ICategoryDB;
use App\Page\Creator\ICreator;
use App\Page\Creator\ICreatorDB;

abstract class PDO implements ICategoryDB, IArticleDB, ICreatorDB {


    protected $connection;
    protected $categories;
    protected $articleCreator;
    protected $categoryCreator;

    public function setArticleCreator(ICreator $creator) {
        $this->articleCreator = $creator;
    }

    public function setCategoryCreator(ICreator $creator) {
        $this->categoryCreator = $creator;
    }

    public function getCategoryData(int $id) {

        $q = "SELECT * FROM categories WHERE id = :id";

        $s = $this->connection->prepare($q);

        $s->execute([
            ":id" => $id
        ]);

        return $s->fetch();
    }

    public function getHomeCategories() {
        $q = "SELECT DISTINCT c.id FROM categories c
            INNER JOIN resources_categories rc ON c.id = rc.category_id
            WHERE rc.resource_type = 'article'";

        $s = $this->connection->prepare($q);

        $s->execute();

        return array_map(
            fn($id) => $this->getCategory($id),
            $s->fetchAll(\PDO::FETCH_COLUMN)
        );
    }

    public function getCategoryArticles(int $id, string $orderBy, string $orderType, int $offset, int $limit) {

        $q = "SELECT a.id
            FROM articles a                
            INNER JOIN resources_categories rc ON a.id = rc.resource_id AND rc.resource_type = 'article'
            LEFT JOIN resources_views rv ON a.id = rv.resource_id AND rv.resource_type = 'article'
            WHERE rc.category_id = :c_id
            ORDER BY $orderBy $orderType
            LIMIT :offset, :limit";

        $s = $this->connection->prepare($q);

        $s->bindValue(':c_id', (int) $id, \PDO::PARAM_INT);
        $s->bindValue(':offset', (int) $offset, \PDO::PARAM_INT);
        $s->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);

        $s->execute();

        return array_map(
            fn($id) => $this->articleCreator->getById($id),
            $s->fetchAll(\PDO::FETCH_COLUMN)
        );
    }


    public function getSimilarArticles(int $id, int $limit = 6) {

        $article = $this->articleCreator->getById($id);

        $similar = [];

        foreach ($article->getCategories() as $category) {
            foreach ($category->getRandomArticles(7) as $cArticle) {
                if ($cArticle->getId() !== $id) {
                    $similar[] = $cArticle;
                    $limit--;
                    if ($limit < 1) {
                        break 2;
                    }
                }
            }
        }

        shuffle($similar);

        return $similar;
    }

    public function getRandomCategoryArticles(int $id, int $limit)
    {
        $q = "SELECT a.id
            FROM articles a                
            INNER JOIN resources_categories rc ON a.id = rc.resource_id AND rc.resource_type = 'article'
            WHERE rc.category_id = :c_id
            ORDER BY RAND()
            LIMIT :limit";

        $s = $this->connection->prepare($q);

        $s->bindValue(':c_id', (int) $id, \PDO::PARAM_INT);
        $s->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);

        $s->execute();

        return array_map(
            fn($id) => $this->articleCreator->getById($id),
            $s->fetchAll(\PDO::FETCH_COLUMN)
        );
    }

    public function getLatestCategoryArticles(int $id, int $limit) {

        $q = "SELECT a.id
            FROM articles a                
            INNER JOIN resources_categories rc ON a.id = rc.resource_id AND rc.resource_type = 'article'
            WHERE rc.category_id = :c_id
            ORDER BY a.created_at DESC
            LIMIT :limit";

        $s = $this->connection->prepare($q);

        $s->bindValue(':c_id', (int) $id, \PDO::PARAM_INT);
        $s->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);

        $s->execute();

        return array_map(
            fn($id) => $this->articleCreator->getById($id),
            $s->fetchAll(\PDO::FETCH_COLUMN)
        );
    }

    public function countCategoryArticles(int $id): int
    {
        $q = "SELECT COUNT(*) 
          FROM articles a                
          INNER JOIN resources_categories rc ON a.id = rc.resource_id 
          WHERE rc.resource_type = 'article' 
            AND rc.category_id = :c_id";

        $s = $this->connection->prepare($q);
        $s->bindValue(':c_id', $id, \PDO::PARAM_INT);
        $s->execute();

        return (int) $s->fetchColumn();
    }

    protected function getCategories()
    {
        if (is_null($this->categories)) {
            $this->categories = array_map(
                fn($id) => $this->categoryCreator->getById($id),
                $this->getIDs("categories")
            );
        }
        return $this->categories;
    }

    public function getChildrenCategories(int $id) {
        $children = [];

        foreach ($this->getCategories() as $category) {
            if ($category->getParentId() == $id) {
                $children[] = $category;
            }
        }

        return $children;
    }

    public function getCategory(int $id) {
        foreach ($this->getCategories() as $category) {
            if ($category->getId() == $id) {
                return $category;
            }
        }
        return null;
    }

    public function getCategoryByTitle(string $title) {
        foreach ($this->getCategories() as $category) {
            if ($category->getTitle() == $title) {
                return $category;
            }
        }
        return null;
    }



    public function createCategories($list) {

        foreach ($list as $path) {

            $parts = array_map('trim', explode('>', $path));

            $parentId = null;

            foreach ($parts as $name) {
                $parentId = $this->getOrCreateCategory($name, $parentId);
            }

        }

    }

    public function getOrCreateCategory($name, $parentId)
    {
        $q = "SELECT id FROM categories WHERE title = ? AND " .
            ($parentId === null ? "parent_id IS NULL" : "parent_id = ?");

        $s = $this->connection->prepare($q);
        $params = ($parentId === null) ? [$name] : [$name, $parentId];
        $s->execute($params);
        $row = $s->fetch();

        if ($row) {
            return (int) $row['id'];
        }

        $s = $this->connection->prepare("INSERT INTO categories (title, parent_id) VALUES (?, ?)");
        $s->execute([$name, $parentId]);

        return (int) $this->connection->lastInsertId();
    }

    public function createArticle($data) {

        $q = "INSERT INTO articles (title, description, content) VALUES (:title, :description, :content)";

        $s = $this->connection->prepare($q);

        if (isset($data['title']) && isset($data['description']) && isset($data['content'])) {
            $s->execute([
                ':title' => $data['title'],
                ':description' => $data['description'],
                ':content' => $data['content']
            ]);

            $id = (int) $this->connection->lastInsertId();

            return $this->articleCreator->getById($id);
        }

        return null;

    }

    public function getRow($table, int $id)
    {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);

        $q = "SELECT * FROM `$table` WHERE id = :id LIMIT 1";

        $s = $this->connection->prepare($q);

        $s->execute(['id' => $id]);

        return $s->fetch();
    }

    public function updateRow($column, $value, $table, int $id)
    {
        $column = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);

        $q = "UPDATE `$table` SET `$column` = :value WHERE id = :id LIMIT 1";

        $s = $this->connection->prepare($q);

        $s->execute([
            ':id' => $id,
            ':value' => $value,
        ]);

    }

    protected function getRows($table)
    {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);

        $sql = "SELECT * FROM `$table`";

        $s = $this->connection->prepare($sql);

        $s->execute();

        return $s->fetchAll();
    }

    protected function getIDs($table)
    {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);

        $sql = "SELECT id FROM `$table`";

        $s = $this->connection->prepare($sql);

        $s->execute();

        return $s->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function getArticleData(int $id)
    {

        $q = "SELECT 
                a.*,
                f.path AS image,
                IFNULL(v.number, 0) AS views,
                GROUP_CONCAT(c.title) AS categories
            FROM articles a
            LEFT JOIN resources_attachments ra ON ra.resource_id = a.id AND ra.resource_type = 'article'
            LEFT JOIN files f ON f.id = ra.file_id
            LEFT JOIN resources_views v ON v.resource_id = a.id AND v.resource_type = 'article'
            LEFT JOIN resources_categories rc ON rc.resource_id = a.id AND rc.resource_type = 'article'
            LEFT JOIN categories c ON c.id = rc.category_id
            WHERE a.id = :id
            GROUP BY a.id";

        $s = $this->connection->prepare($q);
        $s->execute([':id' => $id]);

        $article = $s->fetch();

        if ($article && $article['categories']) {
            $article['categories'] = explode(',', $article['categories']);
        }

        return $article;
    }

    public function setViews($number, $rId, $rType)
    {
        $q = "INSERT INTO resources_views (resource_type, resource_id, number) 
            VALUES (:type, :id, :number_insert) 
            ON DUPLICATE KEY UPDATE 
                number = :number_update";

        $s = $this->connection->prepare($q);

        $s->execute([
            ':type'   => $rType,
            ':id' => $rId,
            ':number_insert' => $number,
            ':number_update' => $number,
        ]);

    }

    public function addFile($path)
    {
        $q = "INSERT IGNORE INTO files (path) VALUES (:path)";

        $s = $this->connection->prepare($q);

        $s->execute([':path' => $path]);

        $id = $this->connection->lastInsertId();

        if ($id == 0) {
            $s = $this->connection->prepare("SELECT id FROM files WHERE path = :path");
            $s->execute([':path' => $path]);
            $id = $s->fetchColumn();
        }

        return $id;

    }

    public function setAttachment($fileId, $rId, $rType)
    {
        $q = "INSERT INTO resources_attachments (resource_type, resource_id, file_id) 
            VALUES (:type, :id, :file_insert) 
            ON DUPLICATE KEY UPDATE 
                file_id = :file_update";

        $s = $this->connection->prepare($q);

        $s->execute([
            ':type'   => $rType,
            ':id' => $rId,
            ':file_insert' => $fileId,
            ':file_update' => $fileId,
        ]);

    }

    public function setCategories($cIds, $rId, $rType)
    {
        $this->connection->beginTransaction();

        $q = "DELETE FROM resources_categories 
                WHERE resource_id = :r_id AND resource_type = :r_type";
        $s = $this->connection->prepare($q);
        $s->execute([':r_id' => $rId, ':r_type' => $rType]);

        if (count($cIds)) {

            $q = "INSERT IGNORE INTO resources_categories (resource_type, resource_id, category_id) 
                    VALUES (:r_type, :r_id, :c_id)";

            $s = $this->connection->prepare($q);

            foreach ($cIds as $cId) {
                $s->execute([
                    ':r_type'   => $rType,
                    ':r_id' => $rId,
                    ':c_id' => $cId
                ]);
            }
        }

        return $this->connection->commit();

    }
}