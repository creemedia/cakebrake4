<?php
namespace Creemedia\CakeBrake4\Error;

use Airbrake\ErrorHandler as AirbrakeErrorHandler;
use Airbrake\Instance as AirbrakeInstance;
use Airbrake\Notifier as AirbrakeNotifier;
use Cake\Core\Configure;
use Cake\Error\ErrorHandler;

/**
 * Airbrake Handler, this class allows for an error or exception to be sent to airbrake,
 * while also allowing the normal CakePHP error flow 
 */
class AirbrakeHandler extends ErrorHandler
{
    protected $airbrakeHandler;

    /**
     * Constructor
     *
     * @param array $options The options for error handling.
     */
    public function __construct($options = [])
    {
        parent::__construct($options);

        // Create new Notifier instance.
        $notifier = new AirbrakeNotifier([
            'projectId' => Configure::read('AirbrakeOptions.project_id'),
            'projectKey' => Configure::read('AirbrakeOptions.project_api_key')
        ]);

        $notifier->addFilter(function ($notice) {
            $notice['context']['environment'] = Configure::read('AirbrakeOptions.environment');
            return $notice;
        });

        if (!empty($this->_options['skipLog'])) {
            $notifier->addFilter(function ($notice) {
                foreach ((array)$this->_options['skipLog'] as $class) {
                    if ($notice['errors'][0]['type'] === $class) {
                        return false;
                    }
                }
                return $notice;
            });
        }

        // Set global notifier instance.
        AirbrakeInstance::set($notifier);

        // Setup the Airbrake Error Handler.
        $this->airbrakeHandler = new AirbrakeErrorHandler($notifier);

        register_shutdown_function([$this->airbrakeHandler, 'onShutdown']);
    }

    /**
     * {@inheritDoc}
     */
    public function handleError($code, $description, $file = null, $line = null, $context = null): bool
    {
        $this->airbrakeHandler->onError($code, $description, $file, $line);
        return parent::handleError($code, $description, $file, $line, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function wrapAndHandleException($exception): void
    {
        $this->airbrakeHandler->onException($exception);
        parent::wrapAndHandleException($exception);
    }
}
