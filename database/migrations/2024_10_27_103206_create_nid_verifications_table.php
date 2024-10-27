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
        Schema::create('nid_verifications', function (Blueprint $table) {
            $table->id();
            $table->longText('nid_number');
            $table->string('email');
            $table->longText('encrypted_image')->nullable();
            $table->string('censored_image_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nid_verifications');
    }
};
