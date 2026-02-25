<?php

namespace App\DB;

use App\Resource\Article;

abstract class PDO {

    protected $connection;

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

            $data = $this->getRow("articles", $id);

            return new Article($data);
        }

        return null;

    }

    private function getRow($table, $id)
    {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);

        $sql = "SELECT * FROM `$table` WHERE id = :id LIMIT 1";

        $s = $this->connection->prepare($sql);

        $s->execute(['id' => $id]);

        return $s->fetch();
    }

    public function getArticle($id)
    {
        $sql = "SELECT 
                a.*,
                f.path AS image,
                IFNULL(v.number, 0) AS views,
                GROUP_CONCAT(c.name) AS categories
            FROM articles a
            LEFT JOIN resources_attachments ra ON ra.resource_id = a.id AND ra.resource_type = 'article'
            LEFT JOIN files f ON f.id = ra.file_id
            LEFT JOIN resources_views v ON v.resource_id = a.id AND v.resource_type = 'article'
            LEFT JOIN resources_categories rc ON rc.resource_id = a.id AND rc.resource_type = 'article'
            LEFT JOIN categories c ON c.id = rc.category_id
            WHERE a.id = :id
            GROUP BY a.id";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([':id' => $id]);

        $article = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($article && $article['categories']) {
            // Превращаем строку категорий "PHP,SQL" в массив ["PHP", "SQL"]
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