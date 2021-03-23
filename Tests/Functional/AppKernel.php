<?php

namespace JMS\JobQueueBundle\Tests\Functional;

// Set-up composer auto-loading if Client is insulated.
call_user_func(
    function () {
        if (!is_file($autoloadFile = __DIR__.'/../../vendor/autoload.php')) {
            throw new \LogicException(
                'The autoload file "vendor/autoload.php" was not found. Did you run "composer install --dev"?'
            );
        }

        require_once $autoloadFile;
    }
);

use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    private $config;

    public function __construct(string $environment, bool $debug)
    {
        parent::__construct('test', false);

    }

    public function registerBundles()
    {
        return array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \JMS\JobQueueBundle\JMSJobQueueBundle(),
            new \JMS\JobQueueBundle\Tests\Functional\TestBundle\TestBundle(),
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $confDir = $this->getProjectDir().'/Tests/Functional/config';
        $loader->load($confDir.'/{packages}/*'.self::CONFIG_EXTS, 'glob');
    }
}