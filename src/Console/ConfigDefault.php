<?php

namespace Kitamula\Kitchen\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Foundation\CachesConfiguration;

class ConfigDefault extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config:default';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '汎用初期設定を行う';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->mergeConfigFor(__DIR__.'/../config/app.php', 'app');
        return 0;
    }

    protected function mergeConfigFor($path, $key)
    {
        if (! ($this->app instanceof CachesConfiguration && $this->app->configurationIsCached())) {
            $this->app['config']->set($key, array_merge(
                $this->app['config']->get($key, []), require $path
            ));
        }
    }

}
