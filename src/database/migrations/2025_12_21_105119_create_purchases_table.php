<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id(); // 
            $table->foreignId('item_id')->constrained('items'); // 
            $table->foreignId('user_id')->constrained('users'); // 購入者 
            $table->tinyInteger('payment_method'); // 
            $table->string('post_code', 8); // 
            $table->string('address'); // 
            $table->string('building_name')->nullable(); // 
            $table->timestamps(); //
                });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
    }
}
