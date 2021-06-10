<?php

use Faker\Factory;
use Phinx\Seed\AbstractSeed;

class PostSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run()
    {
        // Seeding des catégories
        $data = [];
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 5; $i++) {
            $name = $faker->catchPhrase;
            $slug = implode('-', explode(' ', strtolower(str_replace('\'', '', $this->skipAccents($name)))));
            $data[] = [
                'name' => $name,
                'slug' => $slug,
            ];
        }

        $this->table('categories')->insert($data)->save();

        // Seeding des articles
        $data = [];
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 100; $i++) {
            $date = $faker->unixTime('now');
            $name = $faker->catchPhrase;
            $slug = implode('-', explode(' ', strtolower(str_replace('\'', '', $this->skipAccents($name)))));
            $data[] = [
                'name' => $name,
                'slug' => $slug,
                'category_id' => rand(1, 5),
                'content' => $faker->text(3000),
                'updated_at' => date('Y-m-d H:i:s', $date),
                'created_at' => date('Y-m-d H:i:s', $date),
                'published' => 1
            ];
        }

        $this->table('posts')->insert($data)->save();
    }

    private function skipAccents($str, $charset = 'utf-8')
    {

        $str = htmlentities($str, ENT_NOQUOTES, $charset);

        $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
        $str = preg_replace('#&[^;]+;#', '', $str);

        return $str;
    }
}
