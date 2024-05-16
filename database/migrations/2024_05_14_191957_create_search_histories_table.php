<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_histories', function (Blueprint $table) {
            $table->id();
            $table->string('search_term'); // Columna para almacenar el término de búsqueda.
            $table->string('user_session_id'); // Columna para almacenar el identificador de sesión del usuario.
            $table->timestamp('searched_at')->useCurrent(); // Columna para almacenar la marca de tiempo de la búsqueda.
            $table->timestamps();
        });
    }

    /**
     * Revertir las migraciones.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('search_histories');
    }
};
