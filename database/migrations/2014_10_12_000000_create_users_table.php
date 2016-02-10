<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->string('username', '32')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('branchid', '32');
            $table->string('password', '60');
            $table->tinyInteger('admin')->default(0);
            $table->rememberToken();
            $table->string('id', '32')->primary();
            $table->timestamps();

            $table->index('username', 'USERNAME');
            $table->index('name', 'NAME');
            $table->index('email', 'EMAIL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user');
    }
}
