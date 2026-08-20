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
        Schema::create('activities', function (Blueprint $table) {
            $table->string('id')->primary(); // Support string / random numeric ID
            $table->string('title');
            $table->string('subject')->nullable();
            $table->string('project_name')->nullable();
            $table->text('description')->nullable();
            $table->string('start')->nullable();
            $table->string('end')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->boolean('all_day')->default(true);
            $table->string('status', 50)->default('planned'); // 'Confirmed', 'In progress', 'In specification', 'New', 'done', 'Closed', 'cancelled'
            $table->string('category', 100)->default('Survei');
            $table->string('location')->default('BPS HQ');
            
            // Relasi User / Pembuat & Assignee Utama
            $table->string('created_by')->nullable();
            $table->string('assignee_id')->nullable();
            $table->string('division_id')->nullable();

            // Kolom JSON untuk multi assignees dan lampiran dokumen & metadata
            $table->json('assignees')->nullable();
            $table->text('result')->nullable();
            $table->json('documents')->nullable();
            $table->json('read_by')->nullable();
            $table->json('deleted_notification_by')->nullable();

            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('assignee_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('division_id')->references('id')->on('divisions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
