<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $col) {
            $col->string('symbol')->primary();
            $col->string('name');
            $col->string('sector')->nullable();
            $col->decimal('last_price', 10, 2)->nullable();
            $col->boolean('is_dividend_paying')->default(false);
            $col->decimal('last_dividend_amount', 10, 2)->nullable();
            $col->date('last_dividend_date')->nullable();
            $col->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
