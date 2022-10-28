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
        Schema::create('pickup_request', function (Blueprint $table) {
            $table->id();
            $table->integer('file_id');
            $table->string('drop_name')->nullable();
            $table->string('drop_pincode')->nullable();
            $table->string('drop_city')->nullable();
            $table->string('drop_state')->nullable();
            $table->text('drop_address')->nullable();
            $table->string('drop_country')->nullable();
            $table->string('drop_phone')->nullable();
            $table->string('drop_email')->nullable();
            $table->double('cod_value')->nullable();
            $table->double('price')->nullable();
            $table->string('sku')->nullable();
            $table->text('description')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('invoice_date')->nullable();
            $table->string('length')->nullable();
            $table->string('breadth')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('order_type')->nullable();
            $table->double('invoice_value')->nullable();
            $table->string('order_id')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('otp_required')->nullable();
            $table->tinyInteger('shipping_type')->nullable();
            $table->tinyInteger('processed')->nullable();
            $table->text('rec_response')->nullable();
            $table->string('awb')->nullable();
            $table->text('manifest_response')->nullable();
            $table->string('shipping_url')->nullable();
            $table->tinyInteger('cp_id')->nullable();
            $table->tinyInteger('manifest_status')->nullable();
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
        //
        Schema::dropIfExists('pickup_request');

    }
};
