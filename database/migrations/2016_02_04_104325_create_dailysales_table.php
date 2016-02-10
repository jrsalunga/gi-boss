<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDailysalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dailysales', function (Blueprint $table) {
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
            $table->date('date');
            $table->char('branchid', '32')->nullable();
            $table->char('managerid', '32')->nullable();
            $table->decimal('sales', 15, 2)->nullable();
            $table->decimal('cos', 15, 2)->nullable();
            $table->decimal('tips', 15, 2)->nullable();
            $table->smallInteger('custcount')->default(0)->nullable();
            $table->smallInteger('empcount')->default(0)->nullable();
            $table->char('id', '32')->primary();

            $table->index('branchid', 'BRANCHID');
            $table->index('managerid', 'MANAGERID');
            $table->unique(['date','branchid'], 'DATEBRANCH');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dailysales');
    }
}
