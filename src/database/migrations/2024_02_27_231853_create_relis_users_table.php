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
        Schema::create('relis_users', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID形式のID
            // 他のカラムをここに追加する場合は以下に記述
            $table->string('name');
            $table->string('user_iocn')->nullable();
            $table->timestamps();
        });

        Schema::create('relis_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // 他のカラムをここに追加する場合は以下に記述
            $table->string('name');
            $table->string('account_icon')->nullable();
            $table->uuid('relis_user_id'); // 外部キーの型をUUIDに変更
            $table->foreign('relis_user_id') // foreignIdからforeignに変更
                  ->references('id')
                  ->on('relis_users')
                  ->onDelete('cascade');
            $table->boolean('is_default_account')->default(false);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relis_users');
    }
};
