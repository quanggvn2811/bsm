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
        Schema::table('products', function (Blueprint $table) {
            $table->tinyInteger('type')
                ->default(1)
                ->comment('1 => single; 2 => multiple sub product; 3 => other')
            ;

            $table->string('sub_product_id')
                ->nullable()
                ->comment('array sub product ids')
            ;

            // Change data type string to text
            $table->text('images')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('sub_product_id');
        });
    }
};
