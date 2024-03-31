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
        Schema::table('tasks', function (Blueprint $table) {
            // 外部キー制約を追加
            $table->foreign('project_id')
                  ->references('id')
                  ->on('projects')
                  ->onDelete('cascade');
        });

        Schema::table('lists', function (Blueprint $table) {
            // 外部キー制約を追加
            $table->foreign('project_id')
                  ->references('id')
                  ->on('projects')
                  ->onDelete('cascade');
        });

        Schema::table('lists_date', function (Blueprint $table) {
            // 外部キー制約を追加
            $table->foreign('project_id')
                  ->references('id')
                  ->on('projects')
                  ->onDelete('cascade');
        });

        Schema::table('lists_date', function (Blueprint $table) {
            // 外部キー制約を追加
            $table->foreign('list_id')
                  ->references('id')
                  ->on('lists')
                  ->onDelete('cascade');
        });

        Schema::table('lists_particle', function (Blueprint $table) {
            // 外部キー制約を追加
            $table->foreign('project_id')
                  ->references('id')
                  ->on('projects')
                  ->onDelete('cascade');
        });

        Schema::table('lists_particle', function (Blueprint $table) {
            // 外部キー制約を追加
            $table->foreign('list_id')
                  ->references('id')
                  ->on('lists')
                  ->onDelete('cascade');
        });
    }

    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // 外部キー制約を削除
            $table->dropForeign(['project_id']);
        });

        Schema::table('lists', function (Blueprint $table) {
            // 外部キー制約を削除
            $table->dropForeign(['project_id']);
        });

        Schema::table('lists_date', function (Blueprint $table) {
            // 外部キー制約を削除
            $table->dropForeign(['project_id']);
        });

        Schema::table('lists_date', function (Blueprint $table) {
            // 外部キー制約を削除
            $table->dropForeign(['list_id']);
        });

        Schema::table('lists_particle', function (Blueprint $table) {
            // 外部キー制約を削除
            $table->dropForeign(['project_id']);
        });

        Schema::table('lists_particle', function (Blueprint $table) {
            // 外部キー制約を削除
            $table->dropForeign(['list_id']);
        });
    }
};
