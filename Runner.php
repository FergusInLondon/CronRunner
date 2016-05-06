<?php namespace CronRunner;

class Runner {

    /** \Pimple\Container */
    private $container;

    /** array */
    private $jobs;


    public function __construct(
        \Pimple\Container $container
    ) {
        $this->container = $container;
        $this->configureJobs();
    }
    

    private function configureJobs() {
        $classFiles = array_diff(
            scandir(__DIR__.'/Jobs'),
            array('..', '.', 'Base')
        );
        
        foreach ($classFiles as $fileName) {
            if (!strpos($fileName, '.php')) {
                continue;
            }
            
            $class = sprintf('\CronRunner\Jobs\%s', substr($fileName, 0, -4));
            
            if (is_subclass_of($class, '\CronRunner\Jobs\Base\Job')) {
                $this->jobs[] = new $class( $this->container );
            }
        }
    }
    

    public function executeTasks() {
        foreach ($this->jobs as $job) {
            $job->configure(
                $this->container['Scheduler']
            );
        }
        
        $this->container['Scheduler']->run();
    }
}