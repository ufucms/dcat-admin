<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminExtensionsTable extends Migration
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
        Schema::create($this->config('database.extensions_table') ?: 'admin_extensions', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name', 100)->unique()->comment('插件名');
            $table->string('version', 20)->default('')->comment('版本号');
            $table->tinyInteger('is_enabled')->default(0)->comment('是否启用');
            $table->text('options')->nullable()->comment('配置项');
            $table->timestamp('created_at')->nullable()->index()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->index()->comment('更新时间');

            $table->engine = "InnoDB comment '管理-扩展插件表'";
        });

        Schema::create($this->config('database.extension_histories_table') ?: 'admin_extension_histories', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('name', 100)->comment('插件名');
            $table->tinyInteger('type')->default(1)->comment('类型');
            $table->string('version', 20)->default(0)->comment('版本号');
            $table->text('detail')->nullable()->comment('详情');
            $table->timestamp('created_at')->nullable()->index()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->index()->comment('更新时间');
            $table->index('name');

            $table->engine = "InnoDB comment '管理-扩展插件历史版本表'";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->config('database.extensions_table') ?: 'admin_extensions');
        Schema::dropIfExists($this->config('database.extension_histories_table') ?: 'admin_extension_histories');
    }
}
