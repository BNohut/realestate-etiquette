<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('contact_id');
            $table->string('title');
            $table->string('portfolio_no')->unique();
            $table->double('square_net')->nullable();
            $table->double('square_total')->nullable();
            $table->string('portfolio_type');
            $table->string('portfolio_group');
            $table->string('portfolio_variation');
            $table->string('portfolio_resource');
            $table->string('ada_no')->nullable();
            $table->string('parsel_no')->nullable();
            $table->string('street')->nullable();
            $table->string('building_no')->nullable();
            $table->string('apartment_no')->nullable();
            $table->bigInteger('province_id');
            $table->bigInteger('state_id');
            $table->string('neighborhood');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->double('list_price');
            $table->double('minimum_price')->nullable();
            $table->double('sale_price')->nullable();
            $table->longText('description')->nullable();
            $table->dateTime('contract_date')->nullable();
            $table->string('deed_status')->nullable();
            $table->string('images')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolios');
    }
};
