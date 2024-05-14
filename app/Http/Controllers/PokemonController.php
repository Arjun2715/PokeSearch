<?php

namespace App\Http\Controllers;

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
    private function callPokemonAPI($searchTerm)
    {
        // Implement logic to call the Pokemon API and fetch data
        // For example, you can use Guzzle HTTP client or any other HTTP client library

        // For demonstration purposes, let's return a sample response
        return [
            'pokemon_name' => $searchTerm,
            'abilities' => ['Ability 1', 'Ability 2', 'Ability 3'],
        ];
    }
}
