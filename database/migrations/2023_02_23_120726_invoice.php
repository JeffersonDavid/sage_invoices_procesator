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
        Schema::create('invoices', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->string('invoice_number');
            $table->string('invoice_name');
            $table->string('invoice_api_name');
            $table->string('invoice_local_path');
            $table->string('invoice_download_url');
            $table->date('invoice_date');
            $table->integer('send_status');
            $table->date('shipment_date');
            $table->timestamp('updated_date')->useCurrent();
            $table->timestamp('creation_date')->useCurrent();
            $table->longText('token_call');
            $table->longText('refresh_token_call');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
