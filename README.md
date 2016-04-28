# Logging plugin for CakePHP 3.x
Log user's action in your Database. This plugin is composed of a Component and a Log engine.
This plugin is compatible with core logs of CakePHP.

## Requirements
- PHP version 5.4.16 or higher
- CakePhp 3.0 or higher

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require daoandco/cakephp-logging
```

Loading the Plugin like that
```PHP
// In config/bootstrap.php
Plugin::load('Logging', ['bootstrap' => true, 'routes' => false]);
```

Create table log : execute shema in `config/shema/logs.sql` (you can change the table name)

```SQL
CREATE TABLE `logs` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `created` DATETIME NOT NULL,
    `level` VARCHAR(50) NOT NULL,
    `scope` VARCHAR(50) NULL DEFAULT NULL,
    `user_id` INT(10) UNSIGNED NULL DEFAULT NULL,
    `message` TEXT NULL,
    `context` TEXT NULL,
    PRIMARY KEY (`id`),
    INDEX `user_id` (`user_id`),
    INDEX `scope` (`scope`),
    INDEX `level` (`level`)
)
COLLATE='utf8_general_ci'
;
```

## Quick Start

If you want just replace default config you can change Log's config in your `app` file

```PHP
// In config/app.php
'Log' => [
	'debug' => [
		'className' => 'Logging.Database',
        'levels' => ['notice', 'info', 'debug'],
    ],
    'error' => [
        'className' => 'Logging.Database',
        'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
    ],
],
```

For writing to logs see [http://book.cakephp.org/3.0/en/core-libraries/logging.html#writing-to-logs](http://book.cakephp.org/3.0/en/core-libraries/logging.html#writing-to-logs)

```PHP
use Cake\Log\Log;

Log::write('debug', 'Something did not work');
```

Or you can use LogComponent in your controllers. The component store by default `request` and `session` in context field.

```PHP
$this->loadComponent('Logging.Log');

$this->Log->write('debug', 'myScope', 'Message');
```

## Configuration

### Options

- **className :** `'Logging.Database'`
- **model :** Model name (default: `'Logging.Logs'`)
- **table :** table name (default: `'logs'`)
- **levels :** logging levels (default: `'[]'` = all levels) [More infos](http://book.cakephp.org/3.0/en/core-libraries/logging.html#using-levels)
- **scopes:** logging scopes (default: `'[]'` = all scopes) [More infos](http://book.cakephp.org/3.0/en/core-libraries/logging.html#logging-scopes)
- **requiredScope :** if true no store logs if scope is empty (default `false`)
- **userId :** path where is stored user id in Session (default `Auth.User.id`)

### Use cases
Edit `config/app.php`

#### Write everything with the plugin
```PHP
// In config/app.php
'Log' => [
	'debug' => [
		'className' => 'Logging.Database',
        'levels' => ['notice', 'info', 'debug'],
    ],
    'error' => [
        'className' => 'Logging.Database',
        'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
    ],
],
```

#### Write cake log in file and write application log with the plugin

With this configuration the scope is required when you write in a log

```PHP
'Log' => [
    'debug' => [
        'className' => 'Cake\Log\Engine\FileLog',
        'path' => LOGS,
        'file' => 'debug',
        'levels' => ['notice', 'info', 'debug'],
        'scopes' => false,
        'url' => env('LOG_DEBUG_URL', null),
    ],
    'error' => [
        'className' => 'Cake\Log\Engine\FileLog',
        'path' => LOGS,
        'file' => 'error',
        'levels' => ['warning', 'error', 'critical', 'alert', 'emergency'],
        'scopes' => false,
        'url' => env('LOG_ERROR_URL', null),
    ],
    'app' => [
        'className'     => 'Logging.Database',
        'requiredScope' => true,
    ],
],
```

Writing to Logs :
```PHP
// With Cake\Log\Log;
Log::write('debug', 'Something did not work', ['scope'=>['myScope']]);

// Or with component
$this->Log->write('debug', 'myScope', 'Something did not work');
```

## Component
```PHP
// Load component
$this->loadComponent('Logging.Log');
```

### Configuration
- **request:** if true store `$this->request` in Context (default: `'false'`)
- **session:** if true store `$_SESSION` Context  (default: `'false'`)
- **ip:** if true store `$this->request->clientIp()` Context  (default: `'false'`)
- **referer:** if true store `$this->request->referer()` Context  (default: `'false'`)
- **vars:** store more datas (ex : `['plugin' => $this->plugin]`

### Methods
- `write($level, $scope, $message, $context, $config)`
	Log a message

- `emergency($scope, $message, $context, $config)`
	Log a emergency message

- `alert($scope, $message, $context, $config)`
	Log a  alert message

- `critical($scope, $message, $context, $config)`
	Log a critical message

- `error($scope, $message, $context, $config)`
	Log a error message

- `warning($scope, $message, $context, $config)`
	Log a warning message

- `notice($scope, $message, $context, $config)`
	Log a notice message

- `debug($scope, $message, $context, $config)`
	Log a debug message

- `info($scope, $message, $context, $config)`
	Log a info message

### Parameters
- **levels :** (string) logging levels (`'emergency'|'alert'|'critical'|'error'|'warning'|'notice'|'debug'|'info'`) [More infos](http://book.cakephp.org/3.0/en/core-libraries/logging.html#using-levels)
- **scope :** (string|array) logging scopes [More infos](http://book.cakephp.org/3.0/en/core-libraries/logging.html#logging-scopes)
- **message:** (string) log message
- **context:** (array) Additional data to be used for logging the message
- **config:** change base config (ex request, session...)

### Use
```PHP
// Basic usage
$this->Log->write('debug', 'myScope', 'Something did not work');

// With convenience methods
$this->Log->emergency('myScope', 'My message');
$this->Log->alert('myScope', 'My message');
$this->Log->critical('myScope', 'My message');
$this->Log->error('myScope', 'My message');
$this->Log->warning('myScope', 'My message');
$this->Log->notice('myScope', 'My message');
$this->Log->debug('myScope', 'My message');
$this->Log->info('myScope', 'My message');

// Add datas
$this->Log->info('myScope', 'My message', ['key1' => 'value1', 'key2' => 'value2']);

// Save request
$this->Log->info('myScope', 'My message', [], ['request' => true]);

// Save session
$this->Log->info('myScope', 'My message', [], ['session' => true]);

// Save ip
$this->Log->info('myScope', 'My message', [], ['ip' => true]);

// Save referer url
$this->Log->info('myScope', 'My message', [], ['referer' => true]);

// Don't save userId
$this->Log->info('myScope', 'My message', ['userId' => null];

// Force userId if different to $_SESSION
$this->Log->info('myScope', 'My message', ['userId' => 2];

// No scope
$this->Log->info(null, 'My message');

// Multi scope = multi lines in bdd
$this->Log->info(['scope1', 'scope2'], 'My message');

```
