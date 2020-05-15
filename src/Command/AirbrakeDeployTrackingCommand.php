<?php
declare(strict_types=1);

namespace Creemedia\CakeBrake4\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\Http\Client;

/**
 * AirbrakeDeployTracking command.
 */
class AirbrakeDeployTrackingCommand extends Command
{
    /**
     * Hook method for defining this command's option parser.
     *
     * @see https://book.cakephp.org/3.0/en/console-and-shells/commands.html#defining-arguments-and-options
     *
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser The built parser.
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);

        return $parser;
    }

    /**
     * Execute function
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return void The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $airbrake = Configure::read('AirbrakeOptions');
        $id = $airbrake['project_id'];
        $key = $airbrake['project_api_key'];

        unset($airbrake['project_id'], $airbrake['project_api_key']);

        $http = new Client();
        $http->post(
            sprintf('https://airbrake.io/api/v4/projects/%s/deploys?key=%s', $id, $key),
            json_encode($airbrake),
            ['type' => 'json']
        );
    }
}
