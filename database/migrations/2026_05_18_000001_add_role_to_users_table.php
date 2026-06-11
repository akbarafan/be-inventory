<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// This migration is kept for compatibility but role is already in create_users_table
return new class extends Migration
{
    public function up(): void
    {
        // Role column already exists in users table migration - nothing to do
    }

    public function down(): void
    {
        // Nothing to rollback
    }
};
