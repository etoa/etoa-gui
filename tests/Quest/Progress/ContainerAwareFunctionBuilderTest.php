<?php declare(strict_types=1);

namespace EtoA\Quest\Progress;

use EtoA\Building\BuildingRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Technology\TechnologyRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserRepository;
use EtoA\WebTestCase;
use Symfony\Component\Finder\Finder;

class ContainerAwareFunctionBuilderTest extends WebTestCase
{
    /** @var ContainerAwareFunctionBuilder */
    private $builder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->builder = new ContainerAwareFunctionBuilder(
            $this->app[BuildingRepository::class],
            $this->app[TechnologyRepository::class],
            $this->app[DefenseRepository::class],
            $this->app[UserRepository::class],
            $this->app[PlanetRepository::class]
        );
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
