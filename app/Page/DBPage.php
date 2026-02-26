<?php

namespace App\Page;

use App\Layout\ILayout;

class DBPage extends Page
{
    protected $id;
    protected $createdAt;
    protected $updatedAt;

    public function __construct($data, $db, ILayout $layout, $template, $code = 200) {

        parent::__construct($db, $layout, $template, $code);

        if (isset($data['id'])) {
            $this->id = (int) trim($data['id']);
        }

        if (isset($data['created_at'])) {
            $this->createdAt = trim($data['created_at']);
        }

        if (isset($data['updated_at'])) {
            $this->updatedAt = trim($data['updated_at']);
        }

    }

    public function getId() {
        return $this->id;
    }

    public function getCreatedAt($format = true) {
        return $this->getDateValue($this->createdAt, $format);
    }

    public function getUpdatedAt($format = true) {
        return $this->getDateValue($this->updatedAt, $format);
    }

    private function getDateValue($value, $format = true) {
        if (is_null($value) || !mb_strlen($value)) {
            return null;
        }
        if ($format) {
            return date("d.m.Y H:i:s", strtotime($value));
        }
        return $value;
    }
}
