<?php namespace CronRunner\Jobs\Base;

abstract class Job {
    
    /** @var \Pimple\Container */ 
    protected $container;

    /** @var \Configula\Config */
    protected $configuration;

    public function __construct(
        \Pimple\Container $container
    ) {
        $this->container     = $container;
        $this->configuration = $container['Config'];
    }
    
    /**
     * After a Job instance is created, configure() is called - passing
     *  with it a \GO\Job\Job object for configuring timing intervals.
     *
     * @param \GO\Job\Job
     */
    abstract public function configure(\GO\Job\Job $job);
    
    
    /**
     * When the interval requirements are satisfied, execute() is run which
     *  is where the task actually does its magic.
     */
    abstract public function execute();
}