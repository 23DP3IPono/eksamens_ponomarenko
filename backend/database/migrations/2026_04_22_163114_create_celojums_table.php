<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('celojums', function (Blueprint $table) {
        $table->id('celojuma_id');
        $table->string('nosaukums', 100);
        $table->string('galamerkis', 100);
        $table->date('sakuma_datums');
        $table->date('beigu_datums');
        $table->decimal('budzets', 10, 2)->default(0);
        $table->foreignId('lietotajs_id')->constrained('users')->onDelete('cascade');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('celojums');
    }
};
