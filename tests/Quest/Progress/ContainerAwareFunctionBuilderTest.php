<?php

namespace EtoA\Quest\Progress;

use EtoA\WebTestCase;
use Symfony\Component\Finder\Finder;

class ContainerAwareFunctionBuilderTest extends WebTestCase
{
    /** @var ContainerAwareFunctionBuilder */
    private $builder;

    protected function setUp()
    {
        parent::setUp();

        $this->builder = new ContainerAwareFunctionBuilder($this->app);
    }

    /**
     * @dataProvider initFunctionNameProvider
     */
    public function testBuild($name, $className)
    {
        $this->assertInstanceOf($className, $this->builder->build($name, []));
    }

    public function initFunctionNameProvider()
    {
        $finder = new Finder();
        $finder->files()->in(__DIR__ . '/../../../src/Quest/Progress/InitFunctions');

        $namespace = 'EtoA\\Quest\\Progress\\InitFunctions\\';
        $classes = [];
        foreach ($finder as $info) {
            $className = $namespace . $info->getBasename('.php');
            $classes[] = [$className::NAME, $className];
        }

        return $classes;
    }
}
