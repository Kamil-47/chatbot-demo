<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Payments
        DB::table('payments')->where('status', 'oczekująca')->update(['status' => 'waiting']);
        DB::table('payments')->where('status', 'opłacona')->update(['status' => 'paid']);

        // Lessons
        DB::table('lessons')->where('status', 'zaplanowana')->update(['status' => 'planned']);
        DB::table('lessons')->where('status', 'odwołana')->update(['status' => 'canceled']);
        DB::table('lessons')->where('status', 'odbyta')->update(['status' => 'completed']);
    }

    public function down(): void
    {
        // Payments
        DB::table('payments')->where('status', 'waiting')->update(['status' => 'oczekująca']);
        DB::table('payments')->where('status', 'paid')->update(['status' => 'opłacona']);

        // Lessons
        DB::table('lessons')->where('status', 'planned')->update(['status' => 'zaplanowana']);
        DB::table('lessons')->where('status', 'canceled')->update(['status' => 'odwołana']);
        DB::table('lessons')->where('status', 'completed')->update(['status' => 'odbyta']);
    }
};