<?php

namespace Dcat\Admin\Models;

use Illuminate\Database\Seeder;
use Symfony\Component\Yaml\Yaml;

class AdminTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $createdAt = date('Y-m-d H:i:s');

        // create a user.
        Administrator::truncate();
        Administrator::create([
            'username'   => 'admin',
            'password'   => bcrypt('admin'),
            'name'       => 'Administrator',
        ]);

        // create a role.
        Role::truncate();
        Role::create([
            'name'       => 'Administrator',
            'slug'       => Role::ADMINISTRATOR,
        ]);

        // add role to user.
        Administrator::first()->roles()->save(Role::first());

        //create a permission
        $permissionYaml = Yaml::parseFile(config_path('admin-permission.yaml'));

        $permission = [];
        foreach ($permissionYaml as $k => $v) {
            $id1 = ($k + 1);
            $permission[] = [
                'id'          => $id1,
                'parent_id'   => 0,
                'name'        => $v['name'],
                'slug'        => $v['slug'],
                'http_method' => $v['http_method'],
                'http_path'   => $v['http_path'],
                'order'       => $v['order'],
                'created_at'  => $createdAt,
                'updated_at'  => $createdAt,
            ];

            if(isset($v['children']) && $v['children']){
                foreach ($v['children'] as $ke => $va) {
                    $id2 = $id1 * pow(2, 8) | ($ke + 1);
                    $permission[] = [
                        'id'          => $id2,
                        'parent_id'   => $id1,
                        'name'        => $va['name'],
                        'slug'        => $va['slug'],
                        'http_method' => $va['http_method'],
                        'http_path'   => $va['http_path'],
                        'order'       => $va['order'],
                        'created_at'  => $createdAt,
                        'updated_at'  => $createdAt,
                    ];

                    if(isset($va['children']) && $va['children']){
                        foreach ($va['children'] as $key => $val) {
                            $id3 = $id2 * pow(2, 16) | ($key + 1);
                            $permission[] = [
                                'id'          => $id3,
                                'parent_id'   => $id2,
                                'name'        => $val['name'],
                                'slug'        => $val['slug'],
                                'http_method' => $val['http_method'],
                                'http_path'   => $val['http_path'],
                                'order'       => $val['order'],
                                'created_at'  => $createdAt,
                                'updated_at'  => $createdAt,
                            ];
                        }
                    }
                }
            }
        }
        Permission::truncate();
        Permission::insert($permission);

        // Role::first()->permissions()->save(Permission::first());

        // add default menus.
        $menuYaml = Yaml::parseFile(config_path('admin-menu.yaml'));

        $menu = [];
        foreach ($menuYaml as $k => $v) {
            $id1 = ($k + 1);
            $menu[] = [
                'id'         => $id1,
                'parent_id'  => 0,
                'title'      => $v['title'],
                'icon'       => $v['icon'],
                'uri'        => $v['uri'],
                'order'      => $v['order'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            if(isset($v['children']) && $v['children']){
                foreach ($v['children'] as $ke => $va) {
                    $id2 = $id1 * pow(2, 8) | ($ke + 1);
                    $menu[] = [
                        'id'         => $id2,
                        'parent_id'  => $id1,
                        'title'      => $va['title'],
                        'icon'       => $va['icon'],
                        'uri'        => $va['uri'],
                        'order'      => $va['order'],
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];

                    if(isset($va['children']) && $va['children']){
                        foreach ($va['children'] as $key => $val) {
                            $id3 = $id2 * pow(2, 16) | ($key + 1);
                            $menu[] = [
                                'id'         => $id3,
                                'parent_id'  => $id2,
                                'title'      => $val['title'],
                                'icon'       => $val['icon'],
                                'uri'        => $val['uri'],
                                'order'      => $val['order'],
                                'created_at' => $createdAt,
                                'updated_at' => $createdAt,
                            ];
                        }
                    }
                }
            }
        }
        Menu::truncate();
        Menu::insert($menu);

        (new Menu())->flushCache();
    }
}
