<?php

namespace WPINT\Core\Foundation;

use Illuminate\Foundation\Application as FrameworkApplication;

use Exception;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\PackageManifest;
use Illuminate\Foundation\ProviderRepository;
use Illuminate\Http\Request;
use Illuminate\Log\LogServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use WPINT\Core\Foundation\Providers\FileDirectServiceProvider;
use WPINT\Core\Foundation\Providers\FileServiceProvider;
use WPINT\Core\Foundation\Providers\MigrationServiceProvider;
use WPINT\Core\Foundation\Routing\RouterServiceProvider;
use Wpint\WPAPI\WPAPIServiceProvider;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;

class Application extends FrameworkApplication
{
    use Macroable;

    /**
     * The wpint framework version.
     *
     * @var string
     */
    const VERSION = '2.0.0';

    /**
     * Create a new Illuminate application instance.
     *
     * @param  string|null  $basePath
     */
    public function __construct($basePath = null)
    {
        if ($basePath) {
            $this->setBasePath($basePath);
        }
                    
        do_action('wpint_init', $this);

        $this->registerBaseBindings();
        $this->registerBaseServiceProviders();
        $this->registerCoreContainerAliases();

    }

    /**
     * Begin configuring a new wpint application instance.
     *
     * @param  string|null  $basePath
     * @return \WPINT\Core\Foundation\Configuration\ApplicationBuilder
     */
    public static function configure(?string $basePath = null)
    {
        $basePath = match (true) {
            is_string($basePath) => $basePath,
            default => static::inferBasePath(),
        };
     
        do_action('wpint_before_load_configuration', self::$instance);
        
        return (new \WPINT\Core\Foundation\Configuration\ApplicationBuilder(new static($basePath)))            
            ->withKernels()
            ->withEvents()
            ->withCommands()
            ->withProviders();
    }

    /**
     * Register all of the configured providers.
     *
     * @return void
     */
    public function registerConfiguredProviders()
    {
        $providers = (new Collection($this->make('config')->get('app.providers')))
            ->partition(fn ($provider) => str_starts_with($provider, 'Illuminate\\'));

        $providers->splice(1, 0, [$this->make(PackageManifest::class)->providers()]);

        (new ProviderRepository($this, new Filesystem, $this->getCachedServicesPath()))
            ->load($providers->collapse()->toArray());

        $this->fireAppCallbacks($this->registeredCallbacks);

    }

    /**
     * Register all of the base service providers.
     *
     * @return void
     */
    protected function registerBaseServiceProviders()
    {
        $this->register(new LogServiceProvider($this));
        $this->register(new EventServiceProvider($this));
        $this->register(new RouterServiceProvider($this));
        $this->register(new WPAPIServiceProvider($this));
        $this->register(new FileDirectServiceProvider($this));
        $this->register(new FileServiceProvider($this));
        $this->register(new MigrationServiceProvider($this));
    }

    /**
     * Register any services needed for Laravel Cloud.
     *
     * @return void
     */
    protected function registerLaravelCloudServices()
    {
        throw new Exception("This method is not supported in wpint framework.");
    }

    /**
     * Handle the incoming HTTP request and send the response to the browser.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function handleRequest(Request $request)
    {
        $kernel = $this->make(Kernel::class);

        $response = $kernel->handle($request);

        if($response)
        {

            $response->send();       
            $kernel->terminate($request, $response);
        };  

 
    }

    /**
     * Handle the incoming Artisan command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @return int
     */
    public function handleCommand(InputInterface $input)
    {
        $kernel = $this->make(ConsoleKernelContract::class);

        $status = $kernel->handle(
            $input,
            new ConsoleOutput()
        );
     
        $kernel->terminate($input, $status);

        return $status;
    }

}
