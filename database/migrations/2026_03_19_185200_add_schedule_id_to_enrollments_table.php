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
        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreignId('schedule_id')->nullable()->constrained()->onDelete('cascade');
            $table->dropForeign(['class_model_id']);
            $table->foreignId('class_model_id')->nullable()->change()->constrained()->onDelete('cascade');
            $table->dropUnique(['class_model_id', 'student_id']);
            $table->unique(['schedule_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['schedule_id']);
            $table->dropColumn('schedule_id');
            $table->dropUnique(['schedule_id', 'student_id']);
        });
    }
};
