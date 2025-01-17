<?php

namespace Dcat\Admin\Scaffold;

use Dcat\Admin\Exception\AdminException;
use Dcat\Admin\Support\Helper;

class ControllerCreator
{
    use GridCreator, FormCreator, ShowCreator;

    /**
     * Controller full name.
     *
     * @var string
     */
    protected $name;

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * extension name.
     *
     * @var string
     */
    protected $extension;

    /**
     * ControllerCreator constructor.
     *
     * @param  string  $name
     * @param  null  $files
     */
    public function __construct($name, $files = null, $extension = '')
    {
        $this->extension = $extension;

        $this->name = $name;

        $this->files = $files ?: app('files');

    }

    /**
     * Create a controller.
     *
     * @param  string  $model
     * @return string
     *
     * @throws \Exception
     */
    public function create($model)
    {
        $path = $this->getPath($this->name);
        $dir = dirname($path);

        if (! is_dir($dir)) {
            $this->files->makeDirectory($dir, 0755, true);
        }

        if ($this->files->exists($path)) {
            throw new AdminException("Controller [$this->name] already exists!");
        }

        $stub = $this->files->get($this->getStub());

        $slug = str_replace('Controller', '', class_basename($this->name));

        $model = $model ?: 'App\Admin\Repositories\\'.$slug;

        $language = '';
        if($this->extension){
            $language = "{$this->extension}::" . Helper::slug($slug);
        }

        $this->files->put($path, $this->replace($stub, $this->name, $model, $slug, $language));
        $this->files->chmod($path, 0777);

        return $path;
    }

    /**
     * @param  string  $stub
     * @param  string  $name
     * @param  string  $model
     * @return string
     */
    protected function replace($stub, $name, $model, $slug, $language)
    {
        $stub = $this->replaceClass($stub, $name);

        $asModel = " as Model";
        if(stripos($model, 'Repositories') > -1){
            $models = str_replace('Repositories', 'Models', str_replace('Admin\Repositories', 'Models', $model));
            $models = str_replace([
                'Admin\Repositories', 
                'Http\Repositories', 
                'Repositories'
                ], [
                'Models', 
                'Models', 
                'Models'], $model);
            $asModel = ";\r\nuse {$models} as Model";
        }

        return str_replace(
            [
                'DummyModelNamespace',
                '{asModel}',
                'DummyModel',
                'DummyTitle',
                '{controller}',
                '{language}',
                '{grid}',
                '{form}',
                '{show}',
            ],
            [
                $model,
                $asModel,
                class_basename($model),
                class_basename($model),
                $slug,
                $language,
                $this->generateGrid(),
                $this->generateForm(),
                $this->generateShow(),
            ],
            $stub
        );
    }

    /**
     * Get controller namespace from giving name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        return str_replace(['DummyClass', 'DummyNamespace'], [$class, $this->getNamespace($name)], $stub);
    }

    /**
     * Get file path from giving controller name.
     *
     * @param  string  $name
     * @return string
     */
    public function getPath($name)
    {
        $path = Helper::guessClassFileName($name);
        if($this->extension){
            $extension_dir = Helper::getExtensionDir();
            $space = Helper::space($this->extension);
            $paths = Helper::path($this->extension);
            $path  = str_replace("/{$space}/", "/{$extension_dir}/{$paths}/src/", $path);
        }
        return $path;
    }

    /**
     * Get stub file path.
     *
     * @return string
     */
    public function getStub()
    {
        return __DIR__.'/stubs/controller.stub';
    }
}
