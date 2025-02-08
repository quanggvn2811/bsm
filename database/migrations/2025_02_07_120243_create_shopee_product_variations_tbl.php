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
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            // Shopee data
            $table->string('variation_id');
            $table->string('variation_name');
            $table->double('last_imported_price')->default(0)->comment('Last cost');
            $table->text('images')->nullable();
            $table->text('fields')->nullable();
            // BSM product
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedInteger('product_quantity')->default(1);
            $table->softDeletes();
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
        Schema::dropIfExists('product_variations');
    }
};
