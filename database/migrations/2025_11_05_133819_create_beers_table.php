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
        Schema::create('beers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tagline');
            $table->text('description');
            $table->date('first_brewed_date');
            $table->decimal('abv', 4, 1)->comment('Alcohol By Volume (Teor alcoolico)');
            $table->integer('ibu')->comment('International Bitterness Unit (Indice de armargor)');
            $table->integer('ebc')->comment('Escala de cor 0 = clara / 80 = escura');
            $table->decimal('ph', 3, 1)->comment('Indice de Acidez');
            $table->integer('volume')->comment('Volume in ml');
            $table->text('ingredients');
            $table->text('brewer_tips');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beers');
    }
};
