<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use App\Handlers\Events\ChangePasswordEventHandler;
use App\Handlers\Events\AuthLoginEventHandler;
use App\Handlers\Events\AuthLoginErrorEventHandler;
use App\Events\UserChangePassword;
use App\Events\UserLoggedIn;
use App\Events\UserLoggedFailed;
use App\Listeners\DepslpEventListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\SomeEvent' => [
            'App\Listeners\EventListener',
        ],
        UserLoggedIn::class => [
            AuthLoginEventHandler::class,
        ],
        UserChangePassword::class => [
            ChangePasswordEventHandler::class,
        ],
        UserLoggedFailed::class => [
            AuthLoginErrorEventHandler::class,
        ]
    ];

    protected $subscribe = [
        DepslpEventListener::class,
        \App\Listeners\EmploymentActivityEventListener::class,
        \App\Listeners\ProcessesEventListener::class,
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
