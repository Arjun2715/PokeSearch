<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\SearchHistory;

class PokemonController extends Controller
{
    /**
     * Handle search requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        // Validate the search term
        $request->validate([
            'search_term' => 'required|string',
        ]);

        // Call the proxy to fetch data from the Pokemon API
        // For simplicity, let's assume we have a separate method or a proxy class for making API requests
        $pokemonData = $this->callPokemonAPI($request->search_term);

        // Save the search history if the API call was successful
        if ($pokemonData) {
            SearchHistory::create([
                'search_term' => $request->search_term,
                'user_session_id' => $request->session()->getId(),
            ]);
        }

        // Return the response
        return response()->json($pokemonData);
    }

    /**
     * Fetch search history.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function fetchSearchHistory(Request $request)
    {
        // Fetch the user's last 10 search history records based on session ID
        $searchHistory = SearchHistory::where('user_session_id', $request->session()->getId())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Return the search history
        return response()->json($searchHistory);
    }

    /**
     * Clear search history.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function clearSearchHistory(Request $request)
    {
        // Delete all search history records for the current user session
        SearchHistory::where('user_session_id', $request->session()->getId())->delete();

        // Return a success message
        return response()->json(['message' => 'Search history cleared successfully']);
    }

    /**
     * Call the Pokemon API to fetch data.
     * For simplicity, you can implement this method or use a separate proxy class.
     *
     * @param  string  $searchTerm
     * @return mixed
     */
    public function pokemonProxy(Request $request)
    {
        // Validate the search term
        $request->validate([
            'search_term' => 'required|string',
        ]);

        // $pokemonApiUrl = "https://pokeapi.co/api/v2/pokemon/language/7/{$request->search_term}";
        $pokemonApiUrl = "https://pokeapi.co/api/v2/ability/{$request->search_term}";

        // Make a request to the Pokémon API using Guzzle HTTP client
        $response = Http::get($pokemonApiUrl);

        // Check if the request was successful
        if ($response->successful()) {
            // Save the search history
            SearchHistory::create([
                'search_term' => $request->search_term,
                'user_session_id' => $request->session()->getId(),
            ]);

            return $response->json();
        } else {
            // Return an error response if the request fails
            return response()->json(['error' => 'Failed to fetch data from Pokémon API'], $response->status());
        }
    }
}
