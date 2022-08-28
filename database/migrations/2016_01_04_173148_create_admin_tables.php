<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminTables extends Migration
{
    public function getConnection()
    {
        return $this->config('database.connection') ?: config('database.default');
    }

    public function config($key)
    {
        return config('admin.'.$key);
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->config('database.users_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username', 120)->unique()->comment('用户名');
            $table->string('password', 80)->comment('用户密码');
            $table->string('name')->nullable()->comment('姓名');
            $table->string('avatar')->nullable()->comment('用户头像');
            $table->string('remember_token', 100)->nullable()->comment('记住我');
            $table->timestamp('created_at')->nullable()->index()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->index()->comment('更新时间');

            $table->engine = "comment '管理-用户表'";
        });

        Schema::create($this->config('database.roles_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50)->comment('角色名称');
            $table->string('slug', 50)->unique()->comment('角色标识');
            $table->timestamp('created_at')->nullable()->index()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->index()->comment('更新时间');

            $table->engine = "comment '管理-角色表'";
        });

        Schema::create($this->config('database.permissions_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50)->comment('权限名称');
            $table->string('slug', 50)->unique()->comment('权限标识');
            $table->string('http_method')->nullable()->comment('请求方法');
            $table->text('http_path')->nullable()->comment('请求路径');
            $table->integer('order')->default(100)->comment('排序');
            $table->bigInteger('parent_id')->default(0)->comment('上级ID');
            $table->timestamp('created_at')->nullable()->index()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->index()->comment('更新时间');

            $table->engine = "comment '管理-权限表'";
        });

        Schema::create($this->config('database.menu_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('parent_id')->default(0)->comment('上级ID');
            $table->integer('order')->default(100)->comment('排序');
            $table->string('title', 50)->comment('菜单名称');
            $table->string('icon', 50)->nullable()->comment('菜单图标');
            $table->string('uri', 50)->nullable()->comment('菜单路径');
            $table->tinyInteger('show')->default(1)->comment('是否显示');
            $table->string('extension', 50)->nullable()->default('')->comment('扩展参数');
            $table->timestamp('created_at')->nullable()->index()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->index()->comment('更新时间');

            $table->engine = "comment '管理-菜单表'";
        });

        Schema::create($this->config('database.role_users_table'), function (Blueprint $table) {
            $table->bigInteger('role_id')->comment('角色ID');
            $table->bigInteger('user_id')->comment('管理用户ID');
            $table->unique(['role_id', 'user_id']);
            $table->timestamp('created_at')->nullable()->index()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->index()->comment('更新时间');

            $table->engine = "comment '管理-用户角色表'";
        });

        Schema::create($this->config('database.role_permissions_table'), function (Blueprint $table) {
            $table->bigInteger('role_id')->comment('角色ID');
            $table->bigInteger('permission_id')->comment('权限ID');
            $table->unique(['role_id', 'permission_id']);
            $table->timestamp('created_at')->nullable()->index()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->index()->comment('更新时间');

            $table->engine = "comment '管理-角色权限表'";
        });

        Schema::create($this->config('database.role_menu_table'), function (Blueprint $table) {
            $table->bigInteger('role_id')->comment('角色ID');
            $table->bigInteger('menu_id')->comment('菜单ID');
            $table->unique(['role_id', 'menu_id']);
            $table->timestamp('created_at')->nullable()->index()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->index()->comment('更新时间');

            $table->engine = "comment '管理-角色-菜单表'";
        });

        Schema::create($this->config('database.permission_menu_table'), function (Blueprint $table) {
            $table->bigInteger('permission_id')->comment('权限ID');
            $table->bigInteger('menu_id')->comment('菜单ID');
            $table->unique(['permission_id', 'menu_id']);
            $table->timestamp('created_at')->nullable()->index()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->index()->comment('更新时间');

            $table->engine = "comment '管理-菜单权限表'";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->config('database.users_table'));
        Schema::dropIfExists($this->config('database.roles_table'));
        Schema::dropIfExists($this->config('database.permissions_table'));
        Schema::dropIfExists($this->config('database.menu_table'));
        Schema::dropIfExists($this->config('database.role_users_table'));
        Schema::dropIfExists($this->config('database.role_permissions_table'));
        Schema::dropIfExists($this->config('database.role_menu_table'));
        Schema::dropIfExists($this->config('database.permission_menu_table'));
    }
}
