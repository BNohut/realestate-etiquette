<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable()->unique();
            $table->string('gender');
            $table->string('province_id')->nullable();
            $table->string('state_id')->nullable();
            $table->string('neighborhood')->nullable();
            $table->longText('address')->nullable();
            $table->string('profession')->nullable();
            $table->longText('details')->nullable();
            $table->string('avatar')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
};
