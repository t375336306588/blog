<?php

namespace App\Page\Param;

class Order
{
    protected $param;
    protected $filters = [];
    protected $link;
    protected $current;
    protected $values = [];

    public function __construct($data)
    {
        if (isset($data['param'])) {
            $this->param = $data['param'];
        }

        if (isset($data['filters'])) {
            $this->filters = $data['filters'];
        }

        if (isset($data['link'])) {
            $this->link = $data['link'];
        }

        if (isset($data['values'])) {
            $this->values = $data['values'];
        }

        $val = filter_input(INPUT_GET, $this->param, FILTER_SANITIZE_SPECIAL_CHARS);

        $this->current = (isset($this->values[$val])) ? $val : array_key_first($this->values);
    }

    public function getCurrentKey(): string
    {
        return $this->current;
    }

    public function setCurrentKey($current)
    {
        $this->current = $current;
    }

    public function toSQL(): string
    {
        return $this->values[$this->current]["sql"];
    }

    public function getLinks(): array
    {
        $links = [];
        $baseParams = [];

        foreach ($this->filters as $filter) {
            $val = filter_input(INPUT_GET, $filter, FILTER_SANITIZE_SPECIAL_CHARS);
            if ($val !== null && $val !== false) {
                $baseParams[$filter] = $val;
            }
        }

        foreach ($this->values as $key => $data) {
            $params = $baseParams;
            $params[$this->param] = $key;

            $links[] = [
                'key'    => $key,
                'label'  => $data["label"],
                'link'   => $this->link . '?' . http_build_query($params),
                'active' => ($key === $this->current)
            ];
        }

        return $links;
    }
}