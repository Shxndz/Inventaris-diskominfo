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
    Schema::table('users', function (Blueprint $table) {
        $table->string('nip', 18)->unique()->after('name');
        $table->string('no_hp', 20)->after('email');
        $table->string('jabatan')->after('no_hp');
        $table->foreignId('sekbid_id')->nullable()->after('jabatan')->constrained('sekbids')->nullOnDelete();
        $table->enum('role', ['admin', 'user'])->default('user')->after('sekbid_id');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropConstrainedForeignId('sekbid_id');
        $table->dropColumn(['nip', 'no_hp', 'jabatan', 'role']);
    });
}
};
