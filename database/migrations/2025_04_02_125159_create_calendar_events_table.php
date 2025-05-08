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
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null'); // Allow null if project is deleted
            $table->foreignId('resource_id')->nullable()->constrained()->onDelete('set null'); // Allow null if resource is deleted
            $table->string('title');
            $table->dateTime('start_time'); // Use dateTime for specific times
            $table->dateTime('end_time');   // Use dateTime for specific times
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
