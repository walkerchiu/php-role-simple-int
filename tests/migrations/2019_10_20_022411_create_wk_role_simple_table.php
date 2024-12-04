<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateWkRoleSimpleTable extends Migration
{
    public function up()
    {
        Schema::create(config('wk-core.table.role-simple.roles'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->nullableMorphs('host');
            $table->string('serial')->nullable();
            $table->string('identifier');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_enabled')->default(0);

            $table->timestampsTz();
            $table->softDeletes();

            $table->index('serial');
            $table->index('identifier');
            $table->index('is_enabled');
        });

        Schema::create(config('wk-core.table.role-simple.permissions'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('serial')->nullable();
            $table->string('identifier');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->boolean('is_enabled')->default(0);

            $table->timestampsTz();
            $table->softDeletes();

            $table->index('serial');
            $table->index('identifier');
            $table->index('is_enabled');
        });

        Schema::create(config('wk-core.table.role-simple.roles_permissions'), function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');

            $table->foreign('role_id')->references('id')
                  ->on(config('wk-core.table.role-simple.roles'))
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreign('permission_id')->references('id')
                  ->on(config('wk-core.table.role-simple.permissions'))
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });

        Schema::create(config('wk-core.table.role-simple.users_roles'), function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');

            $table->foreign('user_id')->references('id')
                  ->on(config('wk-core.table.user'))
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreign('role_id')->references('id')
                  ->on(config('wk-core.table.role-simple.roles'))
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists(config('wk-core.table.role-simple.users_roles'));
        Schema::dropIfExists(config('wk-core.table.role-simple.roles_permissions'));
        Schema::dropIfExists(config('wk-core.table.role-simple.permissions'));
        Schema::dropIfExists(config('wk-core.table.role-simple.roles'));
    }
}
