<?php

namespace JMS\JobQueueBundle\Tests\Functional;

use Doctrine\ORM\EntityManager;
use JMS\JobQueueBundle\Entity\Job;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class CronTest extends BaseTestCase
{
    public function testSchedulesCommands()
    {
        $output = $this->doRun(array('--min-job-interval' => 1, '--max-runtime' => 12));
        $this->assertEquals(2, substr_count($output, 'Scheduling command scheduled-every-few-seconds'), $output);
    }

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    private function doRun(array $args = array())
    {
        array_unshift($args, 'jms-job-queue:schedule');
        $output = new MemoryOutput();
        $this->app->run(new ArrayInput($args), $output);

        return $output->getOutput();
    }

}