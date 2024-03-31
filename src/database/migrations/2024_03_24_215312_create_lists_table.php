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
        Schema::create('lists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('list_name');
            $table->string('prefix')->nullable();
            $table->string('suffix')->nullable();
            $table->text('list_description')->nullable(); 
            $table->string('status')->default('pending'); //制限すべきかも？$table->enum('status', ['pending', 'in_progress', 'completed', 'on_hold', 'cancelled'])->default('pending');
            $table->integer('progress')->default(0);
            $table->string('category')->nullable(); 
            $table->integer('priority')->default(0);
            $table->boolean('include_time')->default(false);
            $table->uuid('project_id');
            $table->timestamps();
        });

        Schema::create('lists_date', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('no');
            $table->dateTime('date');
            $table->integer('start');
            $table->integer('end');
            $table->string('status')->default('pending'); //制限すべきかも？$table->enum('status', ['pending', 'in_progress', 'completed', 'on_hold', 'cancelled'])->default('pending');
            $table->integer('progress')->default(0);
            $table->string('category')->nullable(); 
            $table->integer('priority')->default(0);
            $table->boolean('include_time')->default(false);
            $table->uuid('project_id');
            $table->uuid('list_id');
            $table->timestamps();
        });

        Schema::create('lists_particle', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('no');
            $table->text('description')->nullable(); 
            $table->string('status')->default('pending'); //制限すべきかも？$table->enum('status', ['pending', 'in_progress', 'completed', 'on_hold', 'cancelled'])->default('pending');
            $table->integer('progress')->default(0);
            $table->string('category')->nullable(); 
            $table->integer('priority')->default(0);
            $table->boolean('include_time')->default(false);
            $table->uuid('project_id');
            $table->uuid('list_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lists');
        Schema::dropIfExists('lists_date');
        Schema::dropIfExists('lists_particle');
    }
};
