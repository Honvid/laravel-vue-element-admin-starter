<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->default(0)->comment('层级关系');
            $table->string('title')->comment('菜单标题');
            $table->string('icon')->nullable()->comment('图标');
            $table->string('uri')->unique()->comment('菜单路径');
            $table->string('permissions')->nullable()->comment('菜单权限');
            $table->integer('priority')->default(0)->comment('排序');
            $table->boolean('link')->default(false)->comment('是否外链');
            $table->boolean('show')->default(true)->comment('是否展示');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
}
