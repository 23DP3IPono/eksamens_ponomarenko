<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('izdevums', function (Blueprint $table) {
        $table->id('izdevums_id');
        $table->decimal('summa', 10, 2);
        $table->date('datums');
        $table->string('kategorija', 50);
        $table->foreignId('celojuma_id')->constrained('celojums', 'celojuma_id')->onDelete('cascade');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izdevums');
    }
};
