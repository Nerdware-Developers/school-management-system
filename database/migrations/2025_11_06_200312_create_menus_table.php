<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('menus', function (Blueprint $table) {
        if (!Schema::hasColumn('menus', 'title')) {
            $table->string('title')->after('id');
        }
        if (!Schema::hasColumn('menus', 'icon')) {
            $table->string('icon')->nullable();
        }
        if (!Schema::hasColumn('menus', 'route')) {
            $table->string('route')->nullable();
        }
        if (!Schema::hasColumn('menus', 'is_active')) {
            $table->boolean('is_active')->default(true);
        }
        if (!Schema::hasColumn('menus', 'parent_id')) {
            $table->unsignedBigInteger('parent_id')->nullable();
        }
        if (!Schema::hasColumn('menus', 'order')) {
            $table->integer('order')->default(0);
        }
    });
}



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
