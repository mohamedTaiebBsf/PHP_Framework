<?php

namespace App\blog\Table;

use App\Blog\Entity\Post;
use Framework\Database\PaginatedQuery;
use Framework\Database\Table;
use Pagerfanta\Pagerfanta;

class PostTable extends Table
{
    protected $entity = Post::class;

    protected $table = "posts";

    public function paginationQuery()
    {
        return "SELECT p.id, p.name, c.name as category_name
        FROM {$this->table} as p 
        LEFT JOIN categories as c ON p.category_id = c.id
        ORDER BY created_at DESC";
    }
}
