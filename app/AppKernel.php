<?php

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use FOS\RestBundle\FOSRestBundle;
use Mnapoli\Translated\Integration\Symfony2\TranslatedBundle;
use Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle;
use MyCLabs\UnitBundle\UnitBundle;
use Sensio\Bundle\DistributionBundle\SensioDistributionBundle;
use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle;
use Sonata\IntlBundle\SonataIntlBundle;
use Symfony\Bundle\AsseticBundle\AsseticBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new FrameworkBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new MonologBundle(),
            new AsseticBundle(),
            new DoctrineBundle(),
            new SensioFrameworkExtraBundle(),
            new MopaBootstrapBundle(),
            new FOSRestBundle(),
            new SonataIntlBundle(),
            new TranslatedBundle(),
            new UnitBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'])) {
            $bundles[] = new WebProfilerBundle();
            $bundles[] = new SensioDistributionBundle();
            $bundles[] = new SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }

    protected function getContainerBaseClass()
    {
        return 'DI\Bridge\Symfony\SymfonyContainerBridge';
    }

    protected function initializeContainer()
    {
        parent::initializeContainer();

        $builder = new \DI\ContainerBuilder();
        $builder->addDefinitions(__DIR__ . '/config/config.php');
        $builder->addDefinitions(__DIR__ . '/../src/MyCLabs/UnitBundle/Resources/config/di.php');
        $builder->wrapContainer($this->getContainer());

        $this->getContainer()->setFallbackContainer($builder->build());
    }
}
