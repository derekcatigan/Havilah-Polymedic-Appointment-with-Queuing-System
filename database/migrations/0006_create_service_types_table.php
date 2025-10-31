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
        Schema::create('service_types', function (Blueprint $table) {
            $table->id();
            $table->string('item_code_id')->unique();
            $table->string('standard_barcode_id')->nullable();
            $table->string('short_description');
            $table->text('standard_description')->nullable();
            $table->string('generic_name')->nullable();
            $table->text('specifications')->nullable();
            $table->string('item_category')->nullable();
            $table->string('examination_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_types');
    }
};
