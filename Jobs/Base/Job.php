<?php namespace CronRunner\Jobs\Base;

abstract class Job {
    
    protected /** \Pimple\Container */ $container;
    protected /** \Configula\Config */ $configuration;

    public function __construct(
        \Pimple\Container $container
    ) {
        $this->container     = $container;
        $this->configuration = $container['Config'];
    }
    
    /**
     *
     *
     *
     *
     */
    abstract public function configure(\GO\Scheduler $scheduler);
    
    /**
     *
     *
     *
     */
    abstract public function execute();
}