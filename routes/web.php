<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PokemonController;

// Ruta para mostrar la página principal.
Route::get('/', function () {
    return view('index');
});

// Ruta para manejar solicitudes de búsqueda
Route::post('/search', [PokemonController::class, 'search'])->name('search');

// Ruta para recuperar el historial de búsqueda
Route::get('/search-history', [PokemonController::class, 'fetchSearchHistory'])->name('search.history');

// Ruta para borrar el historial de búsqueda
Route::post('/clear-search-history', [PokemonController::class, 'clearSearchHistory'])->name('search.history.clear');

// Ruta para eliminar un elemento individual del historial de búsqueda
Route::delete('/delete-search-history/{id}', [PokemonController::class, 'deleteSearchHistoryItem'])->name('search.history.delete');
