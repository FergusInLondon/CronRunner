<?php namespace CronRunner;

/**
 * Runner Class
 *
 * This object is responsible for ensuring individual Job clases are
 *  instantiated and configured. It also provides a wrapper the
 *  Scheduler object.
 */
class Runner {

    /** @var \Pimple\Container */
    private $container;

    public function __construct(
        \Pimple\Container $container
    ) {
        $this->container = $container;
        $this->configureJobs();
    }
    

    /**
     * Dynamically load all available Job objects, creating instances and 
     *  passing in the service container for configuration (i.e Scheduling)
     */
    private function configureJobs() {
        $scheduler     = $this->container['Scheduler'];
        $jobsFolder    = $this->container['Config']->getItem('jobs_folder', '/Jobs');
        $jobsNamespace = $this->container['Config']->getItem('jobs_namespace', '\CronRunner\Jobs\\');

        // Retrieve list of files in the /Jobs directory
        $classFiles = array_diff(
            scandir(__DIR__.$jobsFolder),
            array('..', '.', 'Base')
        );
        
        // Iterate through all files found, verify that they are valid Job 
        //  classes, and then instantiate and configure.
        foreach ($classFiles as $fileName) {
            if (!strpos($fileName, '.php')) {
                continue;
            }
            
            $class = $jobsNamespace . substr($fileName, 0, -4);
            
            if (is_subclass_of($class, '\CronRunner\Jobs\Base\Job')) {
                $job = new $class( $this->container );
                $job->configure(
                    $this->container['Scheduler']->call([$job, 'execute'])
                );
            }
        }
    }
    

    /**
     * Run the Scheduler.
     */
    public function executeTasks() {
        $this->container['Scheduler']->run();
    }
}
