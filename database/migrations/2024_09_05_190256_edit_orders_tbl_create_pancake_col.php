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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('pancake_shop_id', 20)->nullable();
            $table->string('pancake_shop_order_id', 20)->nullable();
            $table->string('created_by', 20)->nullable();
            $table->text('pancake_shop_order_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('pancake_shop_id');
            $table->dropColumn('pancake_shop_order_id');
            $table->dropColumn('created_by');
            $table->dropColumn('pancake_shop_order_link');
        });
    }
};
