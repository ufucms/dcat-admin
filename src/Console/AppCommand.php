<?php

namespace Dcat\Admin\Console;

use Dcat\Admin\Support\Helper;
use Illuminate\Filesystem\Filesystem;

class AppCommand extends InstallCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'admin:app {name} 
        {--yaml= : Create menu permission yaml files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new application';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $yaml = $this->option('yaml');

        $this->addConfig();
        if($yaml){
            $this->addYaml();
        }
        $this->initAdminDirectory();

        $this->info('Done.');
    }

    protected function addConfig()
    {
        /* @var Filesystem $files */
        $files = $this->laravel['files'];

        $app = Helper::slug($namespace = $this->argument('name'));

        $files->put(
            $config = config_path($app.'.php'),
            str_replace(
                ['DummyNamespace', 'DummyApp', 'DummyRoute'],
                [$namespace, $app, strtoupper($app)],
                $files->get(__DIR__.'/stubs/config.stub')
            )
        );

        config(['admin' => include $config]);
    }

    protected function addYaml()
    {
        /* @var Filesystem $files */
        $files = $this->laravel['files'];

        $app = strtolower($this->argument('name'));

        $menu = config_path($app.'-menu.yaml');
        $files->put($menu, $this->getStub('app-menu'));

        $permission = config_path($app.'-permission.yaml');
        $files->put($permission, $this->getStub('app-permission'));
    }

    /**
     * Set admin directory.
     *
     * @return void
     */
    protected function setDirectory()
    {
        $this->directory = app_path($this->argument('name'));
    }
}
