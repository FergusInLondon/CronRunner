<?php namespace CronRunner\Jobs;

class ExampleJob extends Base\Job {
    public function configure(\GO\Job\Job $job) {
        $logOutput  = $this->configuration->getItem('output_file', 'logs/output.log');
        $adminEmail = $this->configuration->getItem('output_email', 'admin@localhost');
        
        $job->every()->hour(06)
            ->output(sprintf("%s/../%s", __DIR__, $logOutput), true)
            ->email($adminEmail);
    }

    public function execute() {
       return "We're running without any intervention required.";
    }
}
