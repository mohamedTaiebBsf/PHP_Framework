<?php

namespace Tests\Framework;

use Framework\Validator;
use PHPUnit\Framework\TestCase;
use Tests\DatabaseTestCase;

class ValidatorTest extends DatabaseTestCase
{
    public function testRequiredIfFail()
    {
        $errors = $this->makeValidator(['name' => 'john'])
            ->required('name', 'content')
            ->getErrors();

        $this->assertCount(1, $errors);
    }

    public function testNotEmpty()
    {
        $errors = $this->makeValidator(['name' => 'john', 'content' => ''])
            ->notEmpty('content')
            ->getErrors();

        $this->assertCount(1, $errors);
    }


    public function testRequiredIfSuccess()
    {
        $errors = $this->makeValidator(['name' => 'john', 'content' => 'content'])
            ->required('name', 'content')
            ->getErrors();

        $this->assertCount(0, $errors);
    }

    public function testSlugSuccess()
    {
        $errors = $this->makeValidator([
            'slug' => 'aze-aze-32',
            'slug2' => 'aze'
        ])
            ->slug('slug')
            ->slug('slug2')
            ->getErrors();

        $this->assertCount(0, $errors);
    }

    public function testSlugError()
    {
        $errors = $this->makeValidator([
            'slug' => 'aze-azE-32',
            'slug2' => 'aze-aze_32',
            'slug3' => 'aze--aze-32',
            'slug4' => 'aze-aze-'
        ])
            ->slug('slug')
            ->slug('slug2')
            ->slug('slug3')
            ->slug('slug4')
            ->getErrors();

        $this->assertEquals(['slug', 'slug2', 'slug3', 'slug4'], array_keys($errors));
    }

    public function testLength()
    {
        $params = ['slug' => '123456789'];
        $this->assertCount(0, $this->makeValidator($params)->length('slug', 3)->getErrors());
        $errors = $this->makeValidator($params)->length('slug', 12)->getErrors();
        $this->assertCount(1, $errors);
        $this->assertEquals('Le champ slug doit contenir plus de 12 caractères.', $errors['slug']);
        $this->assertCount(1, $this->makeValidator($params)->length('slug', 3, 4)->getErrors());
        $this->assertCount(0, $this->makeValidator($params)->length('slug', 3, 20)->getErrors());
        $this->assertCount(0, $this->makeValidator($params)->length('slug', null, 20)->getErrors());
        $this->assertCount(1, $this->makeValidator($params)->length('slug', 3, 8)->getErrors());
    }

    public function testDateTime()
    {
        $this->assertCount(0, $this->makeValidator(['date' => '2021-05-12 17:46:32'])->datetime('date')->getErrors());
        $this->assertCount(0, $this->makeValidator(['date' => '2021-05-12 00:00:00'])->datetime('date')->getErrors());
        $this->assertCount(1, $this->makeValidator(['date' => '2021-21-12'])->datetime('date')->getErrors());
        $this->assertCount(1, $this->makeValidator(['date' => '2021-02-29'])->datetime('date')->getErrors());
    }

    public function testExists()
    {
        $pdo = $this->getPdo();
        $pdo->exec("CREATE TABLE test (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255)
        )");

        $pdo->exec("INSERT INTO test (name) VALUES ('a1')");
        $pdo->exec("INSERT INTO test (name) VALUES ('a2')");

        $this->assertTrue($this->makeValidator(['category' => 1])->exists('category', 'test', $pdo)->isValid());
        $this->assertFalse($this->makeValidator(['category' => 123])->exists('category', 'test', $pdo)->isValid());

    }

    private function makeValidator(array $params): Validator
    {
        return new Validator($params);
    }
}