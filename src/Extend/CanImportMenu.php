<?php

namespace Dcat\Admin\Extend;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @property \Symfony\Component\Console\Output\OutputInterface $output
 */
trait CanImportMenu
{
    protected $menu = [];

    protected $menuValidationRules = [
        'parent' => 'nullable',
        'title'  => 'required',
        'uri'    => 'nullable',
        'icon'   => 'nullable',
    ];

    /**
     * 获取菜单节点.
     *
     * @return array
     */
    protected function menu()
    {
        return $this->menu;
    }

    /**
     * 添加菜单.
     *
     * @param  array  $menu
     *
     * @throws \Exception
     */
    protected function addMenu(array $menu = [])
    {
        $menu = $menu ?: $this->menu();

        if (! Arr::isAssoc($menu)) {
            foreach ($menu as $v) {
                $this->addMenu($v);
            }

            return;
        }

        if (! $this->validateMenu($menu)) {
            return;
        }

        if ($menuModel = $this->getMenuModel()) {
            $lastOrder = $menuModel::max('order');
            $data = [
                'parent_id' => $this->getParentMenuId($menu['parent'] ?? 0),
                'order'     => $lastOrder + 1,
                'title'     => $menu['title'],
                'icon'      => (string) ($menu['icon'] ?? ''),
                'uri'       => (string) ($menu['uri'] ?? ''),
                'extension' => $this->getName(),
            ];
            $extensionInfo = $menuModel::where('extension', '!=', '')->first();
            if(!isset($extensionInfo) || !$extensionInfo){
                $prefix = env('DB_PREFIX', '');
                $table  = config('admin.database.menu_table');
                DB::update("ALTER TABLE {$prefix}{$table} AUTO_INCREMENT = 16777217;");
            }
            $menuModel::create($data);
        }
    }

    /**
     * 刷新菜单.
     *
     * @throws \Exception
     */
    protected function refreshMenu()
    {
        $this->flushMenu();

        $this->addMenu();
    }

    /**
     * 根据名称获取菜单ID.
     *
     * @param  int|string  $parent
     * @return int
     */
    protected function getParentMenuId($parent)
    {
        if (is_numeric($parent)) {
            return $parent;
        }

        $menuModel = $this->getMenuModel();

        return $menuModel::query()
            ->where('title', $parent)
            ->where('extension', $this->getName())
            ->value('id') ?: 0;
    }

    /**
     * 删除菜单.
     */
    protected function flushMenu()
    {
        $menuModel = $this->getMenuModel();

        if (! $menuModel) {
            return;
        }

        $menuModel::query()
            ->where('extension', $this->getName())
            ->delete();
    }

    /**
     * 验证菜单字段格式是否正确.
     *
     * @param  array  $menu
     * @return bool
     *
     * @throws \Exception
     */
    public function validateMenu(array $menu)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($menu, $this->menuValidationRules);

        if ($validator->passes()) {
            return true;
        }

        return false;
    }

    protected function getMenuModel()
    {
        return config('admin.database.menu_model');
    }
}
