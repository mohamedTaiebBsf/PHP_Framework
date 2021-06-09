<?php

namespace App\Blog\Entity;

class Post
{
    public $id;
    public $name;
    public $slug;
    public $content;
    public $createdAt;
    public $updatedAt;
    public $categoryName;

    public function setCreatedAt($datetime)
    {
        if (is_string($datetime)) {
            $this->createdAt = new \DateTime($datetime);
        }
    }

    public function setUpdatedAt($datetime)
    {
        if (is_string($datetime)) {
            $this->updatedAt = new \DateTime($datetime);
        }
    }
}
