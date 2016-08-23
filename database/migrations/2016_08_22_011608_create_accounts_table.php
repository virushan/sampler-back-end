<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;


class CreateAccountsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_email', 50)->unique();
            $table->double('balance', 15, 2);
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_email');
            $table->foreign('user_email')->references('email')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function($table){
            $table->dropForeign(['user_email']);
        });
        Schema::drop('accounts');
    }
}
