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
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('order_date')->default(date('Y-m-d'));
            $table->string('order_number');
            $table->string('box_size')->nullable();
            $table->tinyInteger('priority')->default(0);
            $table->tinyInteger('status_id')->default(0);
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')
                ->references('id')
                ->on('customers');
            $table->double('total')->default(0);
            $table->unsignedFloat('ship_by_customer')->default(0);
            $table->unsignedFloat('ship_by_shop')->default(0);
            $table->unsignedInteger('shop_id');
            $table->double('cost')->default(0);
            $table->text('evidence')->nullable();
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('orders');
    }
};
