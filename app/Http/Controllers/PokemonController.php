<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\SearchHistory;

class PokemonController extends Controller
{
    /**
     * Manejar solicitudes de búsqueda.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        // Validar el término de búsqueda
        $request->validate([
            'search_term' => 'required|string',
        ]);

        // Llamar al proxy para obtener datos de la API de Pokémon
        // Para simplicidad, supongamos que tenemos un método separado o una clase proxy para hacer solicitudes a la API
        $pokemonData = $this->callPokemonAPI($request->search_term);

        // Guardar el historial de búsqueda si la llamada a la API fue exitosa
        if ($pokemonData) {
            SearchHistory::create([
                'search_term' => $request->search_term,
                'user_session_id' => $request->session()->getId(),
            ]);
        }

        // Devolver la respuesta
        return response()->json($pokemonData);
    }

    /**
     * Obtener historial de búsqueda.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function fetchSearchHistory(Request $request)
    {
        // Obtener los últimos 10 registros del historial de búsqueda del usuario basados en el ID de sesión
        $searchHistory = SearchHistory::where('user_session_id', $request->session()->getId())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Devolver el historial de búsqueda
        return response()->json($searchHistory);
    }

    /**
     * Borrar historial de búsqueda.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function clearSearchHistory(Request $request)
    {
        // Eliminar todos los registros del historial de búsqueda para la sesión de usuario actual
        SearchHistory::where('user_session_id', $request->session()->getId())->delete();

        // Devolver un mensaje de éxito
        return response()->json(['message' => 'Historial de búsqueda borrado correctamente']);
    }

    /**
     * Eliminar un elemento individual del historial de búsqueda.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteSearchHistoryItem($id)
    {
        // Encontrar y eliminar el elemento del historial de búsqueda por su ID
        SearchHistory::find($id)->delete();

        // Devolver un mensaje de éxito
        return response()->json(['message' => 'Elemento del historial de búsqueda eliminado correctamente']);
    }

    /**
     * Llamar a la API de Pokémon para obtener datos.
     * Para simplicidad, puedes implementar este método o usar una clase proxy separada.
     *
     * @param  string  $searchTerm
     * @return mixed
     */
    public function callPokemonAPI($searchTerm)
    {
        $pokemonApiUrl = "https://pokeapi.co/api/v2/pokemon/{$searchTerm}";

        // Hacer una solicitud a la API de Pokémon usando el cliente HTTP Guzzle
        $response = Http::get($pokemonApiUrl);

        // Verificar si la solicitud fue exitosa
        if ($response->successful()) {
            return $response->json();
        } else {
            // Devolver nulo si la solicitud falla
            return null;
        }
    }
}
