<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Search Pokemon</title> 
        <script src="https://cdn.tailwindcss.com"></script> 
    </head>
    <body class="flex justify-center items-start h-screen mt-10"> 
        <div class="absolute top-0 left-0">
            <div class="bg-white rounded px-8 pt-6 pb-8 mb-4 mt-4">
                <h1 class="text-2xl text-center font-bold mb-4">Search History</h1>
                <ul id="search-history-list" class="list-disc list-inside">
                    <!-- Search history items will be dynamically added here -->
                </ul>
            </div>
        </div>
        <div class="flex flex-col space-x-6">
             <div class="bg-white  rounded px-8 pt-6 pb-8 mb-4">
                <h1 class="text-2xl text-center font-bold mb-4">Search Pokemon</h1>
                <form class="flex flex-row space-x-4 mb-4"> 
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="pokemon-search" type="text" placeholder="Search Pokemon">
                    <button id="search-button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="button">
                        Search
                    </button>
                </form>
            </div>
            <div id="pokemon-info" class="mt-4"></div>
        </div>
       
    </body>
<script>
    document.getElementById('search-button').addEventListener('click', function() {
        var searchTerm = document.getElementById('pokemon-search').value.toLowerCase();
        
        // Make an AJAX request to your backend
        fetch('/pokemon-proxy?search_term=' + encodeURIComponent(searchTerm))
            .then(response => response.json())
            .then(data => {
                // Update the DOM with the fetched data
                if (data.error) {
                    // Handle error
                    console.error('Error:', data.error);
                } else {
                    // Display the fetched data
                    console.log(data);
                    // Example: Display the pokemon name, abilities, and images
                    var pokemonInfo = document.getElementById('pokemon-info');
                    pokemonInfo.innerHTML = '<h2>' + data.name + '</h2>';
                    pokemonInfo.innerHTML += '<img src="' + data.sprites.front_default + '" alt="' + data.name + '">';
                    pokemonInfo.innerHTML += '<h3>Abilities:</h3>';
                    pokemonInfo.innerHTML += '<ul>';
                    data.abilities.forEach(function(ability) {
                        pokemonInfo.innerHTML += '<li>' + ability.ability.name + '</li>';
                    });
                    pokemonInfo.innerHTML += '</ul>';
                    // Update the search history after a new search
                    fetchSearchHistory();
                }
            })
            .catch(error => {
                // Handle network error
                console.error('Error:', error);
            });
    });

    // Function to fetch and display search history
    function fetchSearchHistory() {
        fetch('/search-history')
            .then(response => response.json())
            .then(data => {
                // Clear existing search history
                document.getElementById('search-history-list').innerHTML = '';

                // Iterate over the search history data and generate HTML for each item
                data.forEach(function(item) {
                    var listItem = document.createElement('li');
                    listItem.textContent = item.search_term;
                    document.getElementById('search-history-list').appendChild(listItem);
                });
            })
            .catch(error => {
                console.error('Error fetching search history:', error);
            });
    }
</script>
</html>
