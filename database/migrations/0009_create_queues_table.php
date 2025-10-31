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
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            // Optional link to appointment
            $table->foreignId('appointment_id')
                ->nullable()
                ->constrained('appointments', 'id')
                ->cascadeOnDelete();

            // Doctor reference
            $table->foreignId('doctor_user_id')
                ->constrained('users', 'id')
                ->cascadeOnDelete();

            // Patient reference
            $table->foreignId('patient_user_id')
                ->constrained('users', 'id')
                ->cascadeOnDelete();

            // Queue tracking
            $table->date('queue_date');
            $table->unsignedInteger('queue_number');
            $table->string('queue_status')->default('waiting');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
