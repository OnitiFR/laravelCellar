<?php 

namespace Oniti\Cellar;

use Illuminate\Support\ServiceProvider;
use App;
use Illuminate\Foundation\AliasLoader;

class CellarServiceProvider extends ServiceProvider{
 
 
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
 
    public function boot(){
        $loader = AliasLoader::getInstance();
        $loader->alias('AWS', \Aws\Laravel\AwsFacade::class);
        $loader->alias('CellarS3', \Oniti\Cellar\CellarS3Facade::class);
        $this->publishes([
            __DIR__.'/config' => base_path('config')
        ]);
    }
 
    public function register() {
        App::bind('CellarS3', function()
        {
            return new \Oniti\Cellar\CellarS3;
        });
        $this->app->register(
            \Aws\Laravel\AwsServiceProvider::class
        );
    }
 
}