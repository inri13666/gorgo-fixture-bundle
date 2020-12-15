<?php

declare(strict_types=1);

namespace Gorgo\Bundle\FixtureBundle\Loader;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Oro\Bundle\TestFrameworkBundle\Test\DataFixtures\AliceFixtureFactory;
use Oro\Bundle\TestFrameworkBundle\Test\DataFixtures\AliceFixtureIdentifierResolver;
use Oro\Bundle\TestFrameworkBundle\Test\DataFixtures\DataFixturesExecutor;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpKernel\KernelInterface;

class FixturesLoader implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    private static array $loadedFixtures = [];

    private KernelInterface $kernel;

    private ManagerRegistry $doctrine;

    private ReferenceRepository $referenceRepository;

    public function __construct(KernelInterface $kernel, ManagerRegistry $doctrine)
    {
        $this->kernel = $kernel;
        $this->doctrine = $doctrine;
    }

    public function load(array $fixtures, bool $force = false): void
    {
        $fixtureIdentifierResolver = new AliceFixtureIdentifierResolver($this->kernel);

        $filteredFixtures = $this->filterFixtures($fixtures, $force);
        if (!$filteredFixtures) {
            return;
        }

        $loader = new DataFixturesLoader(new AliceFixtureFactory(), $fixtureIdentifierResolver, $this->container);
        foreach ($filteredFixtures as $fixture) {
            $loader->addFixture($fixture);
        }

        /** @var EntityManagerInterface $em */
        $em = $this->doctrine->getManager();
        $executor = new DataFixturesExecutor($em);
        $this->referenceRepository = $executor->getReferenceRepository();
        $executor->execute($loader->getFixtures(), true);
    }

    public function getReferenceRepository(): ReferenceRepository
    {
        return $this->referenceRepository;
    }

    public static function getLoadedFixtures(): array
    {
        return self::$loadedFixtures;
    }

    private function filterFixtures(array $fixtures, $force = false)
    {
        $fixtureIdentifierResolver = new AliceFixtureIdentifierResolver($this->kernel);
        $filteredFixtures = [];

        foreach ($fixtures as $fixture) {
            $filteredFixtures[$fixtureIdentifierResolver->resolveId($fixture)] = $fixture;
        }

        if (!$force) {
            $removeLoadedFixturesCallback = function ($fixtureId) {
                return !in_array($fixtureId, self::$loadedFixtures, true);
            };

            $filteredFixtures = array_filter($filteredFixtures, $removeLoadedFixturesCallback, ARRAY_FILTER_USE_KEY);
        }

        self::$loadedFixtures = array_merge(self::$loadedFixtures, array_keys($filteredFixtures));

        return $filteredFixtures;
    }
}
