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
        Schema::create('friendships', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUIDをプライマリーキーとして設定
            $table->uuid('account_id'); // UUID型のカラム
            $table->uuid('friend_id'); // UUID型のカラム
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('relis_accounts')->onDelete('cascade');
            $table->foreign('friend_id')->references('id')->on('relis_accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friendships');
    }
};
