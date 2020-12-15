<?php

declare(strict_types=1);

namespace Gorgo\Bundle\FixtureBundle\Command;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Gorgo\Bundle\FixtureBundle\Loader\FixturesLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LoadFixturesCommand extends Command
{
    private FixturesLoader $fixturesLoader;

    private ManagerRegistry $registry;

    public function __construct(FixturesLoader $fixturesLoader, ManagerRegistry $registry)
    {
        parent::__construct('gorgo:fixtures:load');
        $this->fixturesLoader = $fixturesLoader;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();
        $this->addOption('dry-run', null, InputOption::VALUE_NONE);
        $this->addOption('fixture', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $dryRun = $input->getOption('dry-run');
        if ($dryRun) {
            /** @var EntityManagerInterface $manager */
            foreach ($this->registry->getManagers() as $manager) {
                $manager->beginTransaction();
            }
        }

        $fixtures = $input->getOption('fixture');

        if (empty($fixtures)) {
            $output->writeln('At least one fixture required');

            return 1;
        }

        $this->fixturesLoader->load($fixtures);

        if ($dryRun) {
            $table = new Table($output);
            $table->setHeaders([
                'Reference',
                'Type',
            ]);

            foreach ($this->fixturesLoader->getReferenceRepository()->getReferences() as $reference => $value) {
                $table->addRow([
                    $reference,
                    is_object($value) ? ClassUtils::getClass($value) : gettype($value),
                ]);
            }

            $table->render();

            /** @var EntityManagerInterface $manager */
            foreach ($this->registry->getManagers() as $manager) {
                $manager->rollback();
            }
        }
    }
}
