<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('dienas_punkts', function (Blueprint $table) {
        $table->id('punkts_id');
        $table->date('datums');
        $table->string('apraksts', 200)->nullable();
        $table->foreignId('celojuma_id')->constrained('celojums', 'celojuma_id')->onDelete('cascade');
        $table->foreignId('vieta_id')->constrained('vieta', 'vieta_id');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dienas_punkts');
    }
};
