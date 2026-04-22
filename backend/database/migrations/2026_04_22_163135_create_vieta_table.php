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
    Schema::create('vieta', function (Blueprint $table) {
        $table->id('vieta_id');
        $table->string('nosaukums', 100);
        $table->string('adrese', 150)->nullable();
        $table->string('koordinatas', 100)->nullable();
        $table->string('tips', 50)->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vieta');
    }
};
