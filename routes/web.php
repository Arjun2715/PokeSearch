<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PokemonController;

// Route to display the main page
Route::get('/', function () {
    return view('index');
});

// Route to handle search requests
Route::post('/search', [PokemonController::class, 'search'])->name('search');

// Route to fetch search history
Route::get('/search-history', [PokemonController::class, 'fetchSearchHistory'])->name('search.history');

// Route to clear search history
Route::post('/clear-search-history', [PokemonController::class, 'clearSearchHistory'])->name('search.history.clear');

Route::get('/pokemon-proxy', [PokemonController::class, 'pokemonProxy'])->name('pokemon.proxy');
