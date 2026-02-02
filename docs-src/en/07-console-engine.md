# Console Engine

--page-nav--

## 1. Introduction

This engine favors the creation of applications to be executed in the terminal.

## 2. Bootstrap

The first thing to do is create a terminal script and give it execution permissions:

```bash
touch example
chmod a+x example
```

To run it, simply enter the terminal and type:

```bash
./example
```

Of course, nothing will happen, since there is nothing in the script.

In the script file, you must implement the initialization of an application that
uses the `ConsoleEngine` engine as in the example below:

```php
#!/bin/php
<?php

use Iquety\Application\Application;
use Iquety\Application\IoEngine\Console\ConsoleEngine;
use Iquety\Application\IoEngine\Console\ConsoleInput;
use Module\Console\MainConsole;

ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';

$application = Application::instance();

$application->bootEngine(new ConsoleEngine());

// We register the instance of the main module
$app->bootApplication(...);

// We register the instances of one or more secondary modules
$app->bootModule(...);

$output = $application->run(new ConsoleInput($argv));

$application->sendResponse($output);
```

## 3. Module Implementation

Now that the script is implemented, we need to provide the instance of our
module to the `bootApplication` method. For didactic purposes, let's call our
module `MyConsoleModule`:

```php
class MyConsoleModule extends ConsoleModule
{
    public function bootDependencies(Container $container): void
    {
        // here we register the dependencies required for the module
        $container->addFactory(MyInterface::class, new MyImplementation());
    }

    public function bootRoutineDirectories(RoutineSourceSet &$sourceSet): void
    {
        // the directory where the routines that the script will be able to
        // execute are located; you can specify as many directories as necessary
        // in this example, only one will be specified:
        $sourceSet->add(new RoutineSource(__DIR__ . '/My/Routines'));
    }

    public function getScriptName(): string
    {
        // This name will be used to display information to the user
        return 'example';
    }

    public function getScriptPath(): string
    {
        // this path will be used to display information to the user
        return __DIR__;
    }
}
```

Now we provide the module to the application:

```php
$application->bootApplication(new MyConsoleModule());
```

## 4. Implementing the Routine

The last thing to do is to create the routines to be executed. In the configuration
of `MyConsoleModule`, it was determined that the routines should be allocated in
`__DIR__ . '/My/Routines'`. For educational purposes, we will create a file
in this directory and call it `MyRoutine.php`:

```php
<?php

declare(strict_types=1);

namespace Acme\My\Routines;

use Iquety\Application\IoEngine\Console\ConsoleRoutine;
use Iquety\Console\Arguments;

class MyRoutine extends ConsoleRoutine
{
    protected function initialize(): void
    {
        $this->setName('beautiful-routine');

        $this->setDescription('Beautiful information');
    }

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    protected function handle(Arguments $arguments): void
    {
        $this->info('A beautiful routine was performed');
    }
}
```

## 5. Running the Routine

Now, just run the terminal script and specify the name of the routine:

```bash
./example beautiful-routine
➜ A beautiful routine was performed
```

If the script is run without arguments, help information will be displayed:

```bash
./example

How to use: 
  ./example routine [options] [arguments]

Options: 
-h, --help            Display help information

Available routines: 
help                  Display help information
beautiful-routine     Beautiful information
```

For more information on how to implement a routine and the tools available for use,
read the [`iquety/console` library documentation](https://github.com/iquety/console).

--page-nav--
