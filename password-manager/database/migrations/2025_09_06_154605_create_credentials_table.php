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
        Schema::create('credentials', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('username')->nullable();
            $table->text('encrypted_password'); // AES-256 encrypted
            $table->string('url')->nullable();
            $table->text('notes')->nullable();
            $table->json('tags')->nullable(); // JSON array of tags
            $table->json('custom_fields')->nullable(); // Additional custom fields
            $table->foreignId('group_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamp('password_changed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->integer('access_count')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();
            
            $table->index(['group_id', 'title']);
            $table->index('expires_at');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credentials');
    }
};
