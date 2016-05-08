# CronRunner (PHP)
## A skeleton application for writing back-end time-based jobs

Most web applications rely upon complex tasks running automatically behind the scenes, most often initiated by Cron. Whilst some frameworks - such as Laravel - provide a mechanism for writing these as part of your core codebase - sometimes it can be beneficial to break automated tasks out of your primary repository.

That's where CronRunner comes in - a minimalist combination of 3 packages, with enough scaffolding to enable you to quickly write time-based jobs without coupling them to your core codebase. Allowing smaller deployments to environments where the sole task is running supporting services.


### Features

- Built in dependency injection with Pimple, and the ability to configure dependencies in Yaml. (via [Yml2Pimple](https://github.com/gonzalo123/yml2pimple))

- Fluent interface for scheduling tasks, in a similar fashion to Laravel. (via [php-cron-scheduler](https://github.com/peppeocchi/php-cron-scheduler/))

- Flexible configuration options, powered by your choice of .yml, .ini, .json or .php files. (via [Configula](https://github.com/caseyamcl/Configula))

- Simplistic scaffolding, allowing rapid development with minimal fuss or confusion - simply add any required configuration data to the Yml files, and write your task class files.


## Usage

Clone this repository, and do some basic housekeeping - such as updating the composer.json file with your information.

    $ git clone https://github.com/FergusInLondon/CronRunner.git
    $ nano composer.json

Then edit the crontab file, adding an entry for the `run` file.

    $ crontab -e
     * * * * * /path/to/repo/run 1>> /dev/null 2>&1

You're ready to go. Simple.

### Changing the namespace

The chances are that you wont want to continue using the `CronRunner` namespace. Just to a simple find+replace in your codebase for `CronRunner`, replacing it with your desired namespace.

**Ensure that composer.json is also updated to reflect this new namespace!**

### Writing a Job

Writing a new Job is simple, and only requires writing a class and placing it in the 'Jobs' folder - by default located at `/Jobs`, or set via the `jobs_folder` configuration key - located in `/config/`.

A Job must inherit from `CronRunner\Jobs\Base\Job` - and implement two methods: `configure()` and `execute()`.

#### `abstract public function configure(\GO\Job\Job $job)`

This function is responsible for the configuration of the job: specifying it's timing, and doing other tasks that may be required upon initialisation.

CronRunner delegates this responsibility by passing in a [Job object]() from the underlying php-cron-manager library. For information on how to configure this object, see the documentation associated with php-cron-scheduler.

As this method does configuration, it's one of the more likely places that you're going to need to use the built in configuration tool - Configula.

Scheduling can be done via a fluent interface allowing more readable expressions like `every()->hour()`, or alternatively Cron Expressions such as `->at('* * * * *')`. One possible way for writing flexible tasks is to store the Cron Expression in a configuration .yml file.

**Example:**

In this example we retrieve two values from our configuration data (see below) - a log file and an admin email address - and we schedule the task to run hourly.

    public function configure(\GO\Job\Job $job) {
        $logOutput  = $this->configuration->getItem('output_file', 'logs/output.log');
        $adminEmail = $this->configuration->getItem('output_email', 'admin@localhost');

        $job->every()->hour(06)
            ->output(sprintf("%s/../%s", __DIR__, $logOutput), true)
            ->email($adminEmail);
    }

#### `abstract public function execute()`

This method is where you implement the actual logic to your command, and it's called at the interval specified in the configuration.

**Example:**

The simplest example would be one that simply echos a message to STDOUT:

    public function execute() {
       printf("We're running without any intervention required.\n");
    }

Of course, you can do pretty much what you want in here.

### Adding Configuration Options

If your task(s) rely upon additional configuration options, such as database credentials, then rather than hard-coding them you can include them dynamically.

Simply add a file (in either .yml, .ini, .json or .php format) to the config directory. Upon execution all files in this directory are parsed, and the data contained inside is available from `$this->configuration`. (via Configula)

For more information, see The Configula Documentation.

### Adding Dependencies

If your task(s) has external dependencies (i.e other Composer libraries) then add them to the `dependencies.yml` file, located in the config directory.

Upon execution the Dependency Injection Container (Pimple) is configured from this Yaml file. This is then injected in to your custom Job classes, and is accessible via `$this->container`.

For more information, see The Pimple Documentation.

### Usage Tips

- Use a combination of Cron Expressions and the configuration .yml files to enable task intervals to be stored outside of code.

- Explore the php-cron-scheduler documentation for advanced functionality such as: . . .

- Make use of Monolog, and their [extensive range of plugins](https://github.com/Seldaek/monolog/wiki/Third-Party-Packages) - alternatively, write your own using the [PSR-3 interface](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md).

## Examples

This is a simple example that is scheduled to execute once per minute, where it checks for the presence of a file - creating it if it doesn't exist.

    class JobExample extends Base/Job {
        public function configure(\GO\Job\Job $job) {
            $job->every()->minute();        
        }
        
        public function execute() {
            $file = $this->container["Config"]->getItem('file_check');
            
            if ($file && !file_exists($file)) {
                touch($file);
            }
        }
    }

There are also two examples contained in this repository, that demonstrate the logging and output features of php-cron-scheduler. ([ExampleJob](https://github.com/FergusInLondon/CronRunner/blob/master/Jobs/ExampleJob.php) and [LoggedExampleJob](https://github.com/FergusInLondon/CronRunner/blob/master/Jobs/LoggedExampleJob.php))

## Why is this structured as an app, and not a library?

This is released primarily as a mechanism for quickly writing time based tasks without having to implement them directly in the core code-base of an application, allowing you the advantage of having a host dedicated to task running without the need for a full deployment.

With this in mind, it's best viewing this as more of a framework than a library. If you wanted to use this functionality in an existing project, it's quite likely that you're already using a framework which has functionality for task scheduling.

I *may* refactor this in to a library, and continue this repository as a wrapper around the library; allowing existing projects to use the functionality contained.


## Licensing

This is licensed under the GNU General Public License,

Special mentions go to the [Configula](), [php-cron-scheduler]() and [Pimple]() projects - which this skeleton utilises.