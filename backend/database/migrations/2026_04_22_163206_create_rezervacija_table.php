<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('rezervacija', function (Blueprint $table) {
        $table->id('rezerv_num');
        $table->string('tips', 50);
        $table->string('pakalpojuma_nosaukums', 100);
        $table->decimal('cena', 10, 2);
        $table->foreignId('celojuma_id')->constrained('celojums', 'celojuma_id')->onDelete('cascade');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rezervacija');
    }
};
