<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminSettingsTable extends Migration
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
        $engine = config('database.connections.mysql.engine') === null ? 'InnoDB' : config('database.connections.mysql.engine');
        Schema::create($this->config('database.settings_table') ?: 'admin_settings', function (Blueprint $table) use($engine) {
            $table->string('slug', 100)->primary()->comment('设置项标识');
            $table->longText('value')->comment('值');
            $table->timestamp('created_at')->nullable()->index()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->index()->comment('更新时间');

            $table->engine = "{$engine} comment '管理-设置表'";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->config('database.settings_table') ?: 'admin_settings');
    }
}
