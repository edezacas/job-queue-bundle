<?php

namespace JMS\JobQueueBundle\Tests\Functional;

use Doctrine\ORM\EntityManager;
use JMS\JobQueueBundle\Entity\Job;
use Symfony\Component\Process\Process;

class ConcurrencyTest extends BaseTestCase
{
    /** @var Process[] */
    private $processes = array();

    private $configFile;
    private $databaseFile;

    public function testHighConcurrency()
    {
        $this->startWorker('one');
        $this->startWorker('two');

        $filename = tempnam(sys_get_temp_dir(), 'log');

        /** @var Job[] $jobs */
        $jobs = array();
        for ($i = 0; $i < 5; $i++) {
            $jobs[] = $job = new Job('jms-job-queue:logging-cmd', array('Job-'.$i, $filename, '--runtime=1'));
            $this->em->persist($job);
        }
        $this->em->flush();

        $this->waitUntilJobsProcessed(20);

        $logOutput = file_get_contents($filename);
        unlink($filename);

        for ($i = 0; $i < 5; $i++) {
            $this->assertSame(2, substr_count($logOutput, 'Job-'.$i));
        }

        $workers = array();
        foreach ($jobs as $job) {
            $this->em->refresh($job);
            $workers[] = $job->getWorkerName();
        }

        $workers = array_unique($workers);
        sort($workers);

        $this->assertEquals(array('one', 'two'), $workers);
    }

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        foreach ($this->processes as $process) {
            if (!$process->isRunning()) {
                throw new\ RuntimeException(
                    sprintf(
                        'The process "%s" exited prematurely:'."\n\n%s\n\n%s",
                        $process->getCommandLine(),
                        $process->getOutput(),
                        $process->getErrorOutput()
                    )
                );
            }

            $process->stop(5);
        }
    }

    private function waitUntilJobsProcessed($maxRuntime)
    {
        $start = time();
        do {
            usleep(2E5);

            /** @var EntityManager $em */
            $em = self::$kernel->getContainer()->get('doctrine')->getManagerForClass('JMSJobQueueBundle:Job');

            $jobCount = $em->createQuery("SELECT COUNT(j) FROM ".Job::class." j WHERE j.state IN (:nonFinalStates)")
                ->setParameter('nonFinalStates', array(Job::STATE_RUNNING, Job::STATE_NEW, Job::STATE_PENDING))
                ->getSingleScalarResult();
        } while ($jobCount > 0 && time() - $start < $maxRuntime);

        if ($jobCount > 0) {
            $jobs = $em->createQuery("SELECT j FROM ".Job::class." j WHERE j.state IN (:nonFinalStates)")
                ->setParameter('nonFinalStates', array(Job::STATE_RUNNING, Job::STATE_NEW, Job::STATE_PENDING))
                ->getResult();

            throw new \RuntimeException('Not all jobs were processed: '."\n\n".implode("\n\n", $jobs));
        }
    }

    private function startWorker($name)
    {
        $proc = Process::fromShellCommandline(
            'exec '.PHP_BINARY.' '.escapeshellarg(__DIR__.'/console').' jms-job-queue:run --worker-name='.$name
        );
        $proc->start();

        sleep(2);
        if (!$proc->isRunning()) {
            throw new \RuntimeException(
                sprintf(
                    "Process '%s' failed to start:\n\n%s\n\n%s",
                    $proc->getCommandLine(),
                    $proc->getOutput(),
                    $proc->getErrorOutput()
                )
            );
        }

        $this->processes[] = $proc;
    }
}