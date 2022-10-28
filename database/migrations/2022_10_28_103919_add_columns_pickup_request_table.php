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
        Schema::table('pickup_request', function (Blueprint $table) {
            //
            $table->string('customer_name')->after('quantity')->nullable();
            $table->string('user_type')->after('quantity')->nullable();
            $table->string('shipping_company')->after('customer_name')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pickup_request', function (Blueprint $table) {
            //
        });
    }
};
