<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'experience_points')) {
                $table->unsignedInteger('experience_points')->default(0);
            }
            if (!Schema::hasColumn('users', 'level')) {
                $table->unsignedInteger('level')->default(1);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'experience_points')) {
                $table->dropColumn('experience_points');
            }
            if (Schema::hasColumn('users', 'level')) {
                $table->dropColumn('level');
            }
        });
    }
};
