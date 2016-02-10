<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBossbranchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bossbranch', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->char('bossid', '32');
            $table->char('branchid', '32');
            $table->char('id', '32')->primary();

            $table->index('bossid', 'BOSSID');
            $table->index('branchid', 'BRANCHID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bossbranch');
    }
}
