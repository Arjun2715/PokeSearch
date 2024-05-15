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
     * Delete individual search history item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteSearchHistoryItem($id)
    {
        // Find and delete the search history item by its ID
        SearchHistory::find($id)->delete();

        // Return a success message
        return response()->json(['message' => 'Search history item deleted successfully']);
    }

    /**
     * Call the Pokemon API to fetch data.
     * For simplicity, you can implement this method or use a separate proxy class.
     *
     * @param  string  $searchTerm
     * @return mixed
     */
    public function callPokemonAPI($searchTerm)
    {
        $pokemonApiUrl = "https://pokeapi.co/api/v2/pokemon/{$searchTerm}";

        // Make a request to the Pokémon API using Guzzle HTTP client
        $response = Http::get($pokemonApiUrl);

        // Check if the request was successful
        if ($response->successful()) {
            return $response->json();
        } else {
            // Return null if the request fails
            return null;
        }
    }
}
