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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // view, create, update, delete, export, login, logout
            $table->string('resource_type'); // credential, group, user, etc.
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->string('resource_name')->nullable();
            $table->json('old_values')->nullable(); // Before changes
            $table->json('new_values')->nullable(); // After changes
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('additional_data')->nullable(); // Extra context
            $table->timestamp('created_at');
            
            $table->index(['user_id', 'created_at']);
            $table->index(['resource_type', 'resource_id']);
            $table->index(['action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
