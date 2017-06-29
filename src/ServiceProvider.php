<?php

namespace AP\Docs;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider as Base;

/**
 * Class ServiceProvider
 * @package AP\Docs
 */
class ServiceProvider extends Base
{
    /**
     * @var bool
     */
    protected $defer = false;
    /**
     * @var
     */
    protected $config;

    /**
     *
     */
    public function boot()
	{
	    $this->registerRoutes($this->config['http']);

		$this->loadViewsFrom(__DIR__.'/../resources/views', 'docs');
		$this->loadTranslationsFrom(__DIR__.'/../resources/langs', 'docs');
		
		$this->publishes([__DIR__.'/../config/config.php' => config_path('docs.php')], 'config');
		$this->publishes([__DIR__.'/../resources/assets' => public_path('vendor/docs')], 'public');
	}

    /**
     *
     */
    public function register()
	{
		$this->mergeConfigFrom(__DIR__.'/../config/config.php', 'docs');

        $this->config = config('docs') ?: include __DIR__.'/../config/config.php';

        $app = $this->app;

        $this->initStorage($app);

        $app->bind('AP\Docs\Contract', 'AP\Docs\Repository');
        $app->singleton('docs', 'AP\Docs\Contract');
	}

    /**
     * @param $app
     */
    protected function initStorage($app)
    {
        $path = rtrim($this->config['disk']['root'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $files = $app['files'];

        if(!$files->isDirectory($path)){
            $files->makeDirectory($path, 0777, true);
        }

        if(!isset($app['config']['filesystems.disks.docs'])){
            $app['config']['filesystems.disks.docs'] = $this->config['disk'];
        }
    }

    /**
     * @param $config
     */
    protected function registerRoutes($config)
    {
        $this->app['router']->group($config, function($router){
            $router->get('/', ['as' => 'docs.show.vendors', 'uses' => 'Controller@showVendors']);
            $router->get('{vendor}', ['as' => 'docs.show.versions', 'uses' => 'Controller@showVersionsOrPages']);
            $router->get('{vendor}/{versionOrPage}', ['as' => 'docs.show.pages', 'uses' => 'Controller@showPagesOrShowPage']);
            $router->get('{vendor}/{version}/{page}', ['as' => 'docs.show.page', 'uses' => 'Controller@showPage']);
        });
    }
}