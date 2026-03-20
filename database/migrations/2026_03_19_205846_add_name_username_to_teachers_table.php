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
        Schema::table('teachers', function (Blueprint $table) {
            // First make user_id nullable
            $table->dropForeign(['user_id']);
            $table->integer('user_id')->nullable()->change();

            // Add new columns
            $table->string('name')->nullable()->after('id');
            $table->string('username')->nullable()->after('name');
            $table->integer('min_age')->nullable()->after('bio');
            $table->integer('max_age')->nullable()->after('min_age');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['name', 'username', 'min_age', 'max_age']);
            // Re-add foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
