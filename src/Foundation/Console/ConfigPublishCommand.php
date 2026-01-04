<?php

namespace WPINT\Core\Foundation\Console;

use Illuminate\Foundation\Console\ConfigPublishCommand as ConsoleConfigPublishCommand;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\select;

#[AsCommand(name: 'config:publish')]
class ConfigPublishCommand extends ConsoleConfigPublishCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config:publish
                    {name? : The name of the configuration file to publish}
                    {--all : Publish all configuration files}
                    {--force : Overwrite any existing configuration files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish configuration files to your application';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $config = $this->getBaseConfigurationFiles();
        unset($config['broadcasting']);
        if (is_null($this->argument('name')) && $this->option('all')) {
            foreach ($config as $key => $file) {
                $this->publish($key, $file, $this->laravel->configPath().'/'.$key.'.php');
            }

            return;
        }

        $name = (string) (is_null($this->argument('name')) ? select(
            label: 'Which configuration file would you like to publish?',
            options: (new Collection($config))->map(fn (string $path) => basename($path, '.php')),
        ) : $this->argument('name'));

        if (! is_null($name) && ! isset($config[$name])) {
            $this->components->error('Unrecognized configuration file.');

            return 1;
        }

        $this->publish($name, $config[$name], $this->laravel->configPath().'/'.$name.'.php');
    }

}
