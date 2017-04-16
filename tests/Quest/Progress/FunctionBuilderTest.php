<?php

namespace EtoA\Quest\Progress;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

class FunctionBuilderTest extends TestCase
{
    /** @var FunctionBuilder */
    private $builder;

    protected function setUp()
    {
        parent::setUp();

        $this->builder = new FunctionBuilder();
    }

    /**
     * @dataProvider functionNameProvider
     */
    public function testBuild($name, $className)
    {
        $this->assertInstanceOf($className, $this->builder->build($name, []));
    }

    public function functionNameProvider()
    {
        $finder = new Finder();
        $finder->files()->in(__DIR__ . '/../../../src/Quest/Progress/Functions');

        $namespace = 'EtoA\\Quest\\Progress\\Functions\\';
        $classes = [];
        foreach ($finder as $info) {
            $className = $namespace . $info->getBasename('.php');
            $classes[] = [$className::NAME, $className];
        }

        return $classes;
    }
}
