# CronRunner (PHP)
## A skeleton application for writing back-end time-based jobs

Most web applications depend upon complex tasks running automatically behind the scenes, most often initiated by Cron. 

Whilst most frameworks provide mechanisms for writing these as part of your main code-base, this can at times prove to be problematic and costly. Sometimes a more preferable approach is to have a dedicated node running this tasks in isolation.

That's where CronRunner comes in - a minimalist combination of 3 packages, with enough scaffolding to enable you to quickly write 

require on complex CronRunner allows you to decouple time-based jobs out of the


### Features

- Built in dependency injection with Pimple, and the ability to configure dependencies in Yaml. (via Yml2Pimple)

- Fluent interface for scheduling tasks, in a similar fashion to Laravel. (via php-cron-scheduler)

- Flexible configuration options, powered by your choice of .yml, .ini, .json or .php files. (via Configula)

- Simplistic scaffolding, allowing rapid development with minimal fuss or confusion - simply add any required configuration data to the Yml files, and write your task.

## Usage

Clone this repository, and do some basic housekeeping - such as updating the composer.json file with your information.

    $ git clone hxxp...
    $ nano composer.json

Then edit the crontab file, adding an entry for the `run` file.

    $ crontab -e
     * * * * * /path/to/repo/run 1>> /de	v/null 2>&1

You're ready to go. Simple.

### Changing the namespace

The chances are that you wont want to continue using the `CronRunner` namespace. Just to a simple find+replace in your codebase for `CronRunner`, replacing it with your desired namespace.

**Ensure that composer.json is also updated to reflect this new namespace!**

### Writing a Job

Writing a Job is simple, and involves creating a new class located in `Jobs`, which inherits from `BaseJob`. There are only two methods that a subclass needs to implement: `configure` and `execute`.

#### `configure($scheduler)`

This function is responsible for configuring your task, and specifying options such as logging and timing. CronRunner delegates the configuration of this information to the individual task - allowing a greater amount of flexibility.

It takes one parameter, a Scheduler object. For information on how to configure this object, see the documentation associated with php-cron-scheduler.

As this method does configuration, it's one of the more likely places that you're going to need to use the built in configuration tool - Configula.

Scheduling can be done via a fluent interface allowing more readable expressions like `every()->hour()`, or alternatively Cron Expressions such as `->at('* * * * *')`. One possible way for writing flexible tasks is to store the Cron Expression in a configuration .yml file.

**Example:**

In this example we retrieve two values from our configuration data (see below) - a log file and an admin email address - and we schedule the task to run hourly.

    public function configure(\GO\Scheduler $scheduler) {
        $logOutput  = $this->configuration->getValue('logfile', true);
        $adminEmail = $this->configuration->getValue('adminEmail');

        $scheduler->call([$this, 'execute'])
            ->every()->hour()
            ->output($logOutput)
            ->email($adminEmail);
    }

#### `execute()`

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

### Why is this structured as an app, and not a library?

This is released primarily as a mechanism for quickly writing time based tasks without having implementing them directly in the core code-base of an application, allowing you the advantage of having a host dedicated to task running without the need for a full deployment.

If you wanted to implement this in an existing project, it's quite likely that you're already using a framework which has functionality for task scheduling.

This is intended for scenarios where you need to regularly check and process a queue, query external systems, run statistical queries on a database or . As such, it should be viewed as something akin to a minimal framework - one which can rapidly be built upon using other libraries.

## Licensing

This is licensed under the GNU General Public License,

Special mentions go to the Configula, php-cron-scheduler and Pimple projects - which this skeleton utilises.