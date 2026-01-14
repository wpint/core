<?php

namespace WPINT\Core\Console;

use Illuminate\Console\Application as ConsoleApplication;
use Illuminate\Contracts\Container\Container as ContractsContainer;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;

class Application extends ConsoleApplication
{

    public function __construct(ContractsContainer $wpint, EventsDispatcher $events, $version)
    {
       
        parent::__construct($wpint, $events, $version);
        
        $this->setName('WPINT Framework');
        $this->setVersion($version);
    }
    
}
