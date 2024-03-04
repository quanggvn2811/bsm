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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('supplier_id');
            /*$table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->onDelete('cascade');*/
            $table->string('images')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('sku', 20)->nullable(); // Sku by stock
            $table->string('supplier_sku', 20)->nullable(); // sku by ncc
            $table->unsignedFloat('cost')->default(0);
            $table->unsignedFloat('price')->default(0);
            $table->unsignedBigInteger('category_id');
            $table->unsignedInteger('quantity')->default(0);
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
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
        Schema::dropIfExists('products');
    }
};
