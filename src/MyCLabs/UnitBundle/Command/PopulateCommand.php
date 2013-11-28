<?php

namespace MyCLabs\UnitBundle\Command;

use Doctrine\ORM\EntityManager;
use MyCLabs\UnitBundle\Command\Populate\PopulateDiscreteUnit;
use MyCLabs\UnitBundle\Command\Populate\PopulatePhysicalQuantities;
use MyCLabs\UnitBundle\Command\Populate\PopulateStandardUnit;
use MyCLabs\UnitBundle\Command\Populate\PopulateUnitSystem;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Populates the units database from XML data.
 */
class PopulateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('unit:populate')
            ->setDescription('Populate the units database from XML data');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->getContainer()->get('doctrine.orm.entity_manager');

        $populateUnitSystem = new PopulateUnitSystem($entityManager);
        $populateUnitSystem->run();
        $output->writeln('Populated unit systems');

        $quantitieUnits = new PopulatePhysicalQuantities($entityManager);
        $quantitieUnits->run();
        $output->writeln('Populated physical quantities');

        $entityManager->flush();
        return;

        $standardUnits = new PopulateStandardUnit($entityManager);
        $standardUnits->run();
        $output->writeln('Populated standard units');

        $quantitieUnits = new PopulatePhysicalQuantities($entityManager);
        $quantitieUnits->update();
        $output->writeln('Physical quantities updated with reference units');

        $discreteUnits = new PopulateDiscreteUnit($entityManager);
        $discreteUnits->run();
        $output->writeln('Populated discrete units');
    }
}
