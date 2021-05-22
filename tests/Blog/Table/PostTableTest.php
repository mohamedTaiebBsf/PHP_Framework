<?php

namespace Tests\App\Blog\Table;

use App\Blog\Entity\Post;
use App\Blog\Table\PostTable;
use Tests\DatabaseTestCase;

class PostTableTest extends DatabaseTestCase
{

    /**
     * @var PostTable
     */
    private $postTable;

    public function setUp(): void
    {
        $pdo = $this->getPdo();
        $this->migrateDatabase($pdo);
        $this->postTable = new PostTable($pdo);
    }

    public function testFind()
    {
        $this->seedDatabase($this->postTable->getPdo());
        $post = $this->postTable->find(1);
        $this->assertInstanceOf(Post::class, $post);
    }

    public function testFindNotFoundRecord()
    {
        $post = $this->postTable->find(1000);
        $this->assertNull($post);
    }


    public function testUpdate()
    {
        $this->seedDatabase($this->postTable->getPdo());
        $this->postTable->update(1, ['name' => 'salut', 'slug' => 'demo']);
        $post = $this->postTable->find(1);
        $this->assertEquals('salut', $post->name);
        $this->assertEquals('demo', $post->slug);
    }


    public function testInsert()
    {
        $this->postTable->insert([
            'name' => 'salut',
            'slug' => 'demo',
            'content' => 'test',
            'updated_at' => '2019-05-10 14:05:25',
            'created_at' => '2020-07-11 10:30:00'
        ]);
        $post = $this->postTable->find(1);
        $this->assertEquals('salut', $post->name);
        $this->assertEquals('demo', $post->slug);
        $this->assertEquals('test', $post->content);
    }


    public function testDelete()
    {
        $this->postTable->insert([
            'name' => 'salut',
            'slug' => 'demo',
            'content' => 'test',
            'updated_at' => '2019-05-10 14:05:25',
            'created_at' => '2020-07-11 10:30:00'
        ]);
        $this->postTable->insert([
            'name' => 'hola',
            'slug' => 'los-amigos',
            'content' => 'test',
            'updated_at' => '2019-05-10 14:05:25',
            'created_at' => '2020-07-11 10:30:00'
        ]);
        $count = $this->postTable->getPdo()->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        $this->assertEquals(2, (int)$count);
        $this->postTable->delete($this->postTable->getPdo()->lastInsertId());
        $count = $this->postTable->getPdo()->query('SELECT COUNT(id) FROM posts')->fetchColumn();
        $this->assertEquals(1, (int)$count);
    }
}