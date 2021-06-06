<?php declare(strict_types=1);

namespace EtoA\Quest\Progress;

use EtoA\WebTestCase;
use Symfony\Component\Finder\Finder;

class ContainerAwareFunctionBuilderTest extends WebTestCase
{
    /** @var ContainerAwareFunctionBuilder */
    private $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new ContainerAwareFunctionBuilder($this->app);
    }

    /**
     * @dataProvider initFunctionNameProvider
     */
    public function testBuild(string $name, string $className): void
    {
        $this->assertInstanceOf($className, $this->builder->build($name, [
            'building_id' => 1,
            'defense_id' => 1,
            'technology_id' => 1,
            'specialist_id' => 1,
        ]));
    }

    public function initFunctionNameProvider(): array
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
