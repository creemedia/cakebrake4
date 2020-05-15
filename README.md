# README #

## CakePHP 4 Airbrake plugin ##

## Inspiration ##

This packages is inspired by not maintained CakePHP 3 package [hrisShick/AirbrakeCake](https://github.com/chrisShick/AirbrakeCake)

## Requirements

This plugins has been developed for cakephp >=4.0 and PHP >=7.2

### How do I get set up? ###

Add in Composer file:

```composer require creemedia/cakebrake4```


create <PROJECT_NAME>/config/airbrake_options.php

READ:
- https://airbrake.io/docs/api
- https://airbrake.io/docs/api/#create-deploy-v4

add options like:

```
return [
  'AirbrakeOptions' => [
    'project_id' => '<PROJECT_ID>',
    'project_api_key' => '<PROJECT_KEY>',
    // deploy config
    'environment' => '',
    'username' => '',
    'repository' => '', // change to your own, example [https://github.com/airbrake/airbrake;]
    'revision' => '', // example exec('git rev-parse HEAD'),
  ]
];
```

Application.php

Load plugin in bootstrap() method before call parent::bootstrap()

```
    public function bootstrap(): void
    {
        $this->addPlugin('Creemedia/CakeBrake4');

        // Call parent to load bootstrap from files.
        parent::bootstrap();
		// ...
	}
```

bootstrap.php

```
	Configure::load('airbrake_options', 'default');

	$isCli = php_sapi_name() === 'cli';
	if ($isCli) {
		(new ConsoleErrorHandler(Configure::read('Error')))->register();
	} else {
		if (!Configure::read('debug')) {
			(new \Creemedia\CakeBrake4\Error\AirbrakeHandler(Configure::read('Error')))->register();
		} else {
			(new ErrorHandler(Configure::read('Error')))->register();
		}
	}
```

DeployTracking command.

```
	bin/cake airbrake_deploy_tracking
```


### Contribution guidelines ###

* Writing tests
* Code review
* Other guidelines