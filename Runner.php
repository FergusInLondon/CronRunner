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
        \Pimple\Container $container,
        $debug = false
    ) {
        $this->container = $container;
        
        if ($debug){
            return $this->runAllJobs();
        }
        
        $this->configureJobs();
    }


    /**
     *
     *
     */
    private function listJobDirectoryEntries() {
        $jobsFolder    = $this->container['Config']->getItem('jobs_folder', '/Jobs');
        
        // Retrieve list of files in the /Jobs directory
        return array_diff(
            scandir(__DIR__.$jobsFolder),
            array('..', '.', 'Base')
        );
    }
    
    
    /**
     *
     *
     */
    private function getJobObjectForFile($file) {
        $jobsNamespace = $this->container['Config']->getItem('jobs_namespace', '\CronRunner\Jobs\\');
        $class = $jobsNamespace . substr($fileName, 0, -4);
        
        // Check it's a PHP file and extends Base/Job.
        if (strpost(fileName, '.php') && is_subclass_of($class, '\CronRunner\Jobs\Base\Job')) {
            return new $class( $this->container );
        }
        
        return false;
    }
    

    /**
     *
     *
     */
    private function runAllJobs() {
        
        $jobFiles = $this->listJobDirectoryEntries();
        
        foreach ($jobFiles as $jobFilename) {
            printf("\nFound new Job: '%s'\n", $jobFilename);
            
            $job = $this->getJobObjectForFile($jobFilename);
            
            if (!$job) {
                printf("\t[%s] Job not valid!\n", $jobFilename);
                continue;
            }

            printf("\t[%s] Valid Job. Executing...\n", $jobFilename);

            $jobObject = new $job;
            $return    = $job->execute();

            printf("\t[%s] Executed - returning '%s'.\n", $jobFilename, $return);
        }
        
        printf("\nFinished running jobs.\n");
    }


    /**
     * Dynamically load all available Job objects, creating instances and 
     *  passing in the service container for configuration (i.e Scheduling)
     */
    private function configureJobs() {
        $scheduler     = $this->container['Scheduler'];
        $jobsNamespace = $this->container['Config']->getItem('jobs_namespace', '\CronRunner\Jobs\\');

        $classFiles = $this->listJobDirectoryEntries();
        
        // Iterate through all files found, verify that they are valid Job 
        //  classes, and then instantiate and configure.
        foreach ($classFiles as $fileName) {
            $job = $this->getJobObjectForFile($fileName);

            if($job){
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
