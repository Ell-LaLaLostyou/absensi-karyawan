<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal');
            $table->dateTime('masuk_at')->nullable();
            $table->decimal('masuk_lat', 10, 7)->nullable();
            $table->decimal('masuk_lng', 10, 7)->nullable();
            $table->unsignedInteger('masuk_jarak')->nullable();
            $table->string('masuk_foto')->nullable();
            $table->dateTime('pulang_at')->nullable();
            $table->decimal('pulang_lat', 10, 7)->nullable();
            $table->decimal('pulang_lng', 10, 7)->nullable();
            $table->unsignedInteger('pulang_jarak')->nullable();
            $table->string('pulang_foto')->nullable();
            $table->string('status')->default('tepat_waktu');
            $table->timestamps();

            $table->unique(['user_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};