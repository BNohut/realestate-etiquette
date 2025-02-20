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
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('contact_id')->nullable();
            $table->bigInteger('portfolio_id')->nullable();
            $table->bigInteger('province_id')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->string('neighborhood')->nullable();
            $table->bigInteger('record_type_id');
            $table->string('portfolio_group')->nullable();
            $table->string('portfolio_variation')->nullable();
            $table->string('contact_resource')->nullable();
            $table->string('portfolio_type')->nullable();
            $table->string('notes')->nullable();
            $table->dateTime('record_date')->nullable();
            $table->string('link')->nullable();
            $table->double('prepayment')->nullable();
            $table->double('sales_price')->nullable();
            $table->string('record_result')->nullable();
            $table->string('record_level')->nullable();
            $table->double('budget')->nullable();
            $table->double('price_offer')->nullable();
            $table->longText('feed_message')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};
