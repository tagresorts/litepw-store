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
        Schema::table('credentials', function (Blueprint $table) {
            // Additional username/password pairs (up to 6 total)
            $table->string('username_2')->nullable();
            $table->text('encrypted_password_2')->nullable();
            $table->string('username_3')->nullable();
            $table->text('encrypted_password_3')->nullable();
            $table->string('username_4')->nullable();
            $table->text('encrypted_password_4')->nullable();
            $table->string('username_5')->nullable();
            $table->text('encrypted_password_5')->nullable();
            $table->string('username_6')->nullable();
            $table->text('encrypted_password_6')->nullable();
            
            // Additional URLs/IPs (up to 10 total)
            $table->string('url_2')->nullable();
            $table->string('url_3')->nullable();
            $table->string('url_4')->nullable();
            $table->string('url_5')->nullable();
            $table->string('url_6')->nullable();
            $table->string('url_7')->nullable();
            $table->string('url_8')->nullable();
            $table->string('url_9')->nullable();
            $table->string('url_10')->nullable();
            
            // Add indexes for better querying
            $table->index(['username_2', 'created_by']);
            $table->index(['username_3', 'created_by']);
            $table->index(['username_4', 'created_by']);
            $table->index(['username_5', 'created_by']);
            $table->index(['username_6', 'created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credentials', function (Blueprint $table) {
            $table->dropIndex(['username_2', 'created_by']);
            $table->dropIndex(['username_3', 'created_by']);
            $table->dropIndex(['username_4', 'created_by']);
            $table->dropIndex(['username_5', 'created_by']);
            $table->dropIndex(['username_6', 'created_by']);
            
            $table->dropColumn([
                'username_2', 'encrypted_password_2',
                'username_3', 'encrypted_password_3',
                'username_4', 'encrypted_password_4',
                'username_5', 'encrypted_password_5',
                'username_6', 'encrypted_password_6',
                'url_2', 'url_3', 'url_4', 'url_5', 'url_6',
                'url_7', 'url_8', 'url_9', 'url_10'
            ]);
        });
    }
};