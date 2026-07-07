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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('age');
            $table->string('class_number')->nullable();
            $table->string('profile')->nullable();
            $table->text('current_topic')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->date('next_exam_date')->nullable();
            $table->json('schedule')->nullable();
            $table->decimal('price_per_lesson', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};