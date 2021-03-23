<?php

namespace JMS\JobQueueBundle\Tests\Functional;

use Doctrine\ORM\EntityManager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseTestCase extends WebTestCase
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var Application */
    protected $app;

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();

        $this->em = static::$kernel->getContainer()->get('doctrine')->getManagerForClass('JMSJobQueueBundle:Job');

        $this->importDatabaseSchema();

        $this->app = new Application($kernel);
        $this->app->setAutoExit(false);
        $this->app->setCatchExceptions(false);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        // doing this is recommended to avoid memory leaks
        $this->em->close();
        $this->em = null;
    }

    protected final function importDatabaseSchema()
    {
        foreach (self::$kernel->getContainer()->get('doctrine')->getManagers() as $em) {
            $this->importSchemaForEm($em);
        }
    }

    private function importSchemaForEm(EntityManager $em)
    {
        $metadata = $em->getMetadataFactory()->getAllMetadata();
        if (!empty($metadata)) {
            $schemaTool = new SchemaTool($em);
            $schemaTool->dropDatabase();
            $schemaTool->createSchema($metadata);
        }
    }
}