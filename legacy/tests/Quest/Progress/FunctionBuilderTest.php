<?php declare(strict_types=1);

namespace EtoA\Quest\Progress;

use LittleCubicleGames\Quests\Progress\Functions\EventHandlerFunctionInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

class FunctionBuilderTest extends TestCase
{
    private FunctionBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new FunctionBuilder();
    }

    /**
     * @dataProvider functionNameProvider
     * @param class-string<EventHandlerFunctionInterface> $className
     */
    public function testBuild(string $name, string $className): void
    {
        $this->assertInstanceOf($className, $this->builder->build($name, []));
    }

    public function functionNameProvider(): array
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
