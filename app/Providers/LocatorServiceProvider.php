<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class LocatorServiceProvider extends ServiceProvider
{
  /**
   * Register bindings in the container.
   *
   * @return void
   */
  public function register()
  {
    $this->app->singleton('fileStorage', function ($app) {
      return new \App\Helpers\Locator('files');
    });
  }
}