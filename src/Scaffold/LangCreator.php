<?php

namespace Dcat\Admin\Scaffold;

use Dcat\Admin\Support\Helper;
use Illuminate\Support\Facades\App;

class LangCreator
{
    protected $fields = [];

    /**
     * extension name.
     *
     * @var string
     */
    protected $extension;

    public function __construct(array $fields, $extension = '')
    {
        $this->fields = $fields;

        $this->extension = $extension;
    }

    /**
     * 生成语言包.
     *
     * @param  string  $controller
     * @param  string  $title
     * @return string
     */
    public function create(string $controller, ?string $title)
    {
        $controller = str_replace('Controller', '', class_basename($controller));

        $filename = $this->getLangPath($controller);
        if (is_file($filename)) {
            return;
        }

        $title = $title ?: $controller;

        $content = [
            'labels' => [
                $controller => $title,
                Helper::slug($controller) => $title,
                'create' => admin_trans('admin.create'),
                'edit'   => admin_trans('admin.edit'),
            ],
            'fields'  => [],
            'options' => [],
        ];
        foreach ($this->fields as $field) {
            if (empty($field['name'])) {
                continue;
            }

            $content['fields'][$field['name']] = $field['translation'] ?: $field['name'];
        }

        $content['fields']['created_at'] = admin_trans('admin.created_at');
        $content['fields']['updated_at'] = admin_trans('admin.updated_at');

        $files = app('files');
        if ($files->put($filename, Helper::exportArrayPhp($content))) {
            $files->chmod($filename, 0777);

            return $filename;
        }
    }

    /**
     * 获取语言包路径.
     *
     * @param  string  $controller
     * @return string
     */
    protected function getLangPath(string $controller)
    {
        $path = rtrim(app()->langPath(), '/').'/'.App::getLocale();
        if($this->extension){
            $extension_dir = substr(config('admin.extension.dir'), strlen(base_path().DIRECTORY_SEPARATOR));
            $extension = strtolower($this->extension);
            $path = base_path("{$extension_dir}/{$extension}/resources/lang/").App::getLocale();
        }

        return $path.'/'.Helper::slug($controller).'.php';
    }
}
