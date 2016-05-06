<?php namespace CronRunner\Jobs;

class LoggedExampleJob extends Base\Job {
    
    public function configure(\GO\Scheduler $scheduler) {
        $logOutput  = $this->configuration->getItem('monolog_file', 'logs/test.log');

        $logger = new \Monolog\Logger('logOutput');
        $logger->pushHandler(
            new \Monolog\Handler\StreamHandler(sprintf("%s/../%s", __DIR__, $logOutput), \Monolog\Logger::INFO)
        );

        $scheduler->call([$this, 'execute'])
            ->every()->minute()
            ->setLogger($logger)
            ->setLabel('LoggedExampleJob')
            ->setJobDoneMessage('Job Completed.');
    }

    public function execute() {
       return "We're now logging via MonoLog.\n";
    }

}