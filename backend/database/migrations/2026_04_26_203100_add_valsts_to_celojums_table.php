<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('celojums', function (Blueprint $table) {
        $table->string('valsts', 100)->nullable()->after('galamerkis');
    });
}

public function down(): void
{
    Schema::table('celojums', function (Blueprint $table) {
        $table->dropColumn('valsts');
    });
}
};
