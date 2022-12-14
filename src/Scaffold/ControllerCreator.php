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

        if($this->extension){
            $this->name = str_replace('\\Controllers\\', '\\Http\\Controllers\\', $name);
        }else{
            $this->name = $name;
        }

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
            $extension = strtolower(str_replace('/', '.', $this->extension));
            $language = "{$extension}::" . Helper::slug($slug);
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

        return str_replace(
            [
                'DummyModelNamespace',
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
            $extension_dir = substr(config('admin.extension.dir'), strlen(base_path().DIRECTORY_SEPARATOR));
            $extension = strtolower($this->extension);
            $path = str_replace("/{$this->extension}/", "/{$extension_dir}/{$extension}/src/", $path);
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
