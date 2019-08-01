# HTTP Logstash input filebeat emulator

This is a crude solution for sending log file content to a Logstash instance that is set up to listen using the `http` input plugin.

The idea is to run a process provided by this package in a cron task (say, every five minutes). This process will then read log files from a saved pointer to the latest message, and send all of its lines separately with some configurable added context data.

All logged data should be valid JSON for this to work.

## Warning

This is a CRUDE approach, and should only be considered for cases where:

- Ancient software is used;
- Servers have no root access (and so cannot have filebeat installed);


## Usage and Configuration   

It is recommended to run log processing through a cronjob that is [guaranteed not to overlap](http://www.unixwiz.net/tools/lockrun.html).
Within that process, you can easily create and use a processor like so:

```php
<?php
use PXLWidgets\FilebeatEmulator\Config\Config;
use PXLWidgets\FilebeatEmulator\Config\SourceConfig;
use PXLWidgets\FilebeatEmulator\Config\ProcessConfig;
use PXLWidgets\FilebeatEmulator\Config\TargetConfig;
use PXLWidgets\FilebeatEmulator\Process\LogProcessorFactory;

// Provide log paths using glob wildcard patterns to define which logs should be processed.
$logPaths   = ['/home/some/path/log-*.txt', '/other/path/*.log'];
// Determine where the processing status file should be stored.
// This file marks up to where the log was previously processed successfully.
$statusPath = '/home/tmp/status.txt'; 

// The ElasticSearch index that should be used (added under root 'index' key).
$index = 'your-project-acceptance';

// The name for the environment in which your application runs.
$environment = 'acceptance';

// A friendly name for your application (added under root 'application' key).
$application = 'My testing application';

// Any extra data that will be added to all log records sent. This will not overwrite values for keys that are set explicitly.
$extra = [
    'your-key' => 'your value',
];

// The URI where you want to send the log entries to.
$host = 'localhost:5000';

// The (Basic Auth) user and password that you want to use.
$user     = null;
$password = null;

// Headers to send along with the curl call. For example, if you're going to send JSON data, set the correct Content-Type:
$headers = [
    'Content-Type' => 'application/json',
];


$config = new Config(
    new SourceConfig($logPaths, $statusPath),
    new TargetConfig($host, $user, $password, $headers),
    new ProcessConfig($index, $environment, $application, $extra) 
);

$processor = (new LogProcessorFactory())->make($config);


$processor->process();
```

This is the simplest setup, that requires no framework whatever. 
You are encouraged to use environment variables and framework DI-solutions where available.


## Credits

- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
