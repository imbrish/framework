<?php

namespace Illuminate\Tests\View\Blade;

class BladeParseArgumentsTest extends AbstractBladeTestCase
{
    public function testSimpleArguments()
    {
        $expression = '$var, "foo", 123';
        $expected = ['$var', '"foo"', '123'];

        $this->assertEquals($expected, $this->compiler->parseArguments($expression));
    }

    public function testStripParentheses()
    {
        $expression = '(12.345, true, \'baz\')';
        $expected = ['12.345', 'true', '\'baz\''];

        $this->assertEquals($expected, $this->compiler->parseArguments($expression));
    }

    public function testEscapedQuotes()
    {
        $expression = '"bam\"baz\"bof", \'foo \\\'bar\\\'\'';
        $expected = ['"bam\"baz\"bof"', '\'foo \\\'bar\\\'\''];

        $this->assertEquals($expected, $this->compiler->parseArguments($expression));
    }

    public function testArray()
    {
        $expression = '["abc", 12.34, [1, 2, false]]';
        $expected = ['["abc", 12.34, [1, 2, false]]'];

        $this->assertEquals($expected, $this->compiler->parseArguments($expression));
    }

    public function testAssociativeArray()
    {
        $expression = '["foo" => "bar", "baz" => ["abc" => 123], "bam" => true]';
        $expected = ['["foo" => "bar", "baz" => ["abc" => 123], "bam" => true]'];

        $this->assertEquals($expected, $this->compiler->parseArguments($expression));
    }

    public function testStaticMethodCall()
    {
        $expression = 'Foo::bar("abc", 12.34, [1, 2, false])';
        $expected = ['Foo::bar("abc", 12.34, [1, 2, false])'];

        $this->assertEquals($expected, $this->compiler->parseArguments($expression));
    }

    public function testNonStaticMethodCall()
    {
        $expression = '$foo->bar("abc", 12.34, [1, 2, false])';
        $expected = ['$foo->bar("abc", 12.34, [1, 2, false])'];

        $this->assertEquals($expected, $this->compiler->parseArguments($expression));
    }

    public function testManyMixedArguments()
    {
        $expression = <<<EOS

"Foo bar, bar baz, 123",

    'Kabooom, foo "lol"',

["foo" => "bar", "baz" => ["abc" => 123], "bam" => true]  ,

\$foo->bar("abc", 12.34, [1, 2, false]),

43785321.57841, true,

   [],   array()

EOS;

        $expected = [
            '"Foo bar, bar baz, 123"',
            '\'Kabooom, foo "lol"\'',
            '["foo" => "bar", "baz" => ["abc" => 123], "bam" => true]',
            '$foo->bar("abc", 12.34, [1, 2, false])',
            '43785321.57841',
            'true',
            '[]',
            'array()',
        ];

        $this->assertEquals($expected, $this->compiler->parseArguments($expression));
    }
}
