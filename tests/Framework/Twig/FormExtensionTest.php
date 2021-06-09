<?php

namespace Tests\Framework\Twig;

use Framework\Twig\FormExtension;
use PHPUnit\Framework\TestCase;

class FormExtensionTest extends TestCase
{
    /**
     * @var FormExtension
     */
    private $formExtension;

    public function setUp(): void
    {
        $this->formExtension = new FormExtension();
    }

    public function testField()
    {
        $html = $this->formExtension->field([], 'name', 'demo', 'Titre');

        $this->assertSimilar("
            <div class=\"form-group\">
                <label for=\"name\">Titre</label>
                <input type=\"text\" class=\"form-control\" name=\"name\" id=\"name\" value=\"demo\">
            </div>
        ", $html);
    }

    public function testTextarea()
    {
        $html = $this->formExtension->field([], 'name', 'demo', 'Titre', ['type' => 'textarea']);
        $this->assertSimilar("
            <div class=\"form-group\">
                <label for=\"name\">Titre</label>
                <textarea class=\"form-control\" name=\"name\" id=\"name\">demo</textarea>
            </div>
        ", $html);
    }

    public function testSelect()
    {
        $html = $this->formExtension->field(
            [],
            'name',
            2,
            'Titre',
            ['options' => [1 => 'demo', '2' => 'demo2']]
        );

        $this->assertSimilar("
            <div class=\"form-group\">
                <label for=\"name\">Titre</label>
                <select class=\"form-control\" name=\"name\" id=\"name\">
                    <option>Choisir une catégorie...</option>
                    <option value=\"1\">demo</option>
                    <option value=\"2\" selected>demo2</option>
                </select>
            </div>
        ", $html);
    }

    public function testFieldWithClass()
    {
        $html = $this->formExtension->field([], 'name', 'demo', 'Titre', ['class' => 'demo']);
        $this->assertSimilar("
            <div class=\"form-group\">
                <label for=\"name\">Titre</label>
                <input type=\"text\" class=\"form-control demo\" name=\"name\" id=\"name\" value=\"demo\">
            </div>
        ", $html);
    }

    public function testFieldWithErrors()
    {
        $context = ['errors' => ['name' => 'erreur']];
        $html = $this->formExtension->field($context, 'name', 'demo', 'Titre');
        $this->assertSimilar("
            <div class=\"form-group\">
                <label for=\"name\">Titre</label>
                <input type=\"text\" class=\"form-control is-invalid\" name=\"name\" id=\"name\" value=\"demo\">
                <div class=\"invalid-feedback\">erreur</div>
            </div>
        ", $html);
    }

    private function assertSimilar(string $expected, string $actual)
    {
        $this->assertEquals($this->trim($expected), $this->trim($actual));
    }

    private function trim(string $value)
    {
        $lines = explode("\n", $value);
        $lines = array_map('trim', $lines);
        return implode('', $lines);
    }
}