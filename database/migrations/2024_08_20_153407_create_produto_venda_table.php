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
        Schema::create('produto_venda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venda_id')->constrained();
            $table->foreignId('produtos_id')->constrained('produtos'); // Note o plural
            $table->integer('quantidade');
            $table->decimal('valor_compra', 10, 2);
            $table->decimal('valor_venda', 10, 2);
            $table->decimal('valor_total', 10, 2);
            $table->decimal('imposto', 10, 2);
            $table->decimal('frete', 10, 2);
            $table->decimal('lucro', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto_venda');
    }
};
