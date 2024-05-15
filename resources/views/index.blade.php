<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Buscar Pokemon</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex justify-center items-start h-screen mt-10">
    <div class="absolute top-0 left-0">
        <div class="bg-white rounded px-8 pt-6 pb-8 mb-4 mt-4">
            <h1 class="text-2xl text-center font-bold mb-4">Historial</h1>
            <button id="clear-history-button"
                class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline"
                type="button">
                Limpiar Historial
            </button>
            <ul id="search-history-list" class="list-disc list-inside">
                <!-- Search history items will be dynamically added here -->
            </ul>
        </div>
    </div>
    <div class="flex flex-col space-x-6">
        <div class="bg-white  rounded px-8 pt-6 pb-8 mb-4">
            <h1 class="text-2xl text-center font-bold mb-4">Buscar Pokemon</h1>
            <form class="flex flex-row space-x-4 mb-4">
                <input
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="pokemon-search" type="text" placeholder="Buscar Pokemon">
                <button id="search-button"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    type="button">
                    Buscar
                </button>
            </form>
        </div>
        <div id="pokemon-info" class="mt-4"></div>
    </div>

</body>
<script>
    var lastSearchTerm = '';

    window.onload = function() {
        // Fetch search history when the page loads
        fetchSearchHistory();

        // Add event listener for clearing all search history
        document.getElementById('clear-history-button').addEventListener('click', function() {
            clearSearchHistory();
        });
    };

    document.getElementById('search-button').addEventListener('click', function() {
    var searchTerm = document.getElementById('pokemon-search').value.toLowerCase();

    // Prevent duplicate searches
    if (searchTerm === lastSearchTerm) {
        console.log('Duplicate search term, skipping search.');
        return; // Exit the function without executing the search
    }

    // Make an AJAX request to your backend
    fetch('/search', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({
            search_term: searchTerm
        }),
    })
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
            // pokemonInfo.innerHTML += '<h3>Abilities:</h3>';
            // pokemonInfo.innerHTML += '<ul>';
            // data.abilities.forEach(function(ability) {
            //     pokemonInfo.innerHTML += '<li>' + ability.ability.name + '</li>';
            // });
            // pokemonInfo.innerHTML += '</ul>';

            // After getting the Pokémon data
            var pokemonId = data.id;

            // Construct the URL for the ability endpoint
            var abilityUrl = 'https://pokeapi.co/api/v2/ability/' + pokemonId;

            // Make a fetch request to the ability endpoint
            fetch(abilityUrl)
            .then(response => response.json())
            .then(abilityData => {
                // Filter the flavor text entries to include only Spanish
                var spanishEntries = abilityData.flavor_text_entries.filter(entry => entry.language.name === 'es');
                var uniqueSpanishEntries = removeDuplicates(spanishEntries, 'flavor_text');

                // Extract the abilities in Spanish from the filtered entries
                var spanishAbilities = uniqueSpanishEntries.map(entry => entry.flavor_text);

                // Now append the Spanish abilities to the HTML
                pokemonInfo.innerHTML += '<h3>Habilidades:</h3>';
                pokemonInfo.innerHTML += '<ul>';
                spanishAbilities.forEach(function(spanishAbility) {
                    pokemonInfo.innerHTML += '<li>' + spanishAbility + '</li>';
                });
                pokemonInfo.innerHTML += '</ul>';

                // Update the search history after a new search
                fetchSearchHistory();
                // Update the last search term
                lastSearchTerm = searchTerm;
            })
            .catch(error => {
                console.error('Error fetching abilities:', error);
            });
        }
    })
    .catch(error => {
        // Handle network error
        console.error('Error:', error);
    });
});

// Function to remove duplicate entries based on a specific property
function removeDuplicates(array, key) {
    return array.filter((obj, index, self) =>
        index === self.findIndex(entry => entry[key] === obj[key])
    );
}

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
                    // Add click event listener to each history item
                    listItem.addEventListener('click', function() {
                        // Set the search input value to the clicked history item
                        document.getElementById('pokemon-search').value = item.search_term;
                        // Trigger a new search
                        document.getElementById('search-button').click();
                    });
                    // Add a delete button for each history item
                    var deleteButton = document.createElement('button');
                    deleteButton.textContent = '<---Eliminar';
                    deleteButton.addEventListener('click', function(event) {
                        event
                            .stopPropagation(); // Prevent the click from triggering the parent's click event
                        deleteSearchHistoryItem(item.id);
                    });
                    listItem.appendChild(deleteButton);
                    document.getElementById('search-history-list').appendChild(listItem);
                });
                lastSearchTerm = '';
            })
            .catch(error => {
                console.error('Error fetching search history:', error);
            });
    }

    // Function to delete a search history item
    function deleteSearchHistoryItem(itemId) {
        fetch('/delete-search-history/' + itemId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            })
            .then(response => {
                if (response.ok) {
                    // Reload the search history after deletion
                    fetchSearchHistory();
                } else {
                    console.error('Failed to delete search history item.');
                }
            })
            .catch(error => {
                console.error('Error deleting search history item:', error);
            });
    }

    // Function to clear all search history
    function clearSearchHistory() {
        fetch('/clear-search-history', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            })
            .then(response => {
                if (response.ok) {
                    // Reload the search history after clearing
                    fetchSearchHistory();
                } else {
                    console.error('Failed to clear search history.');
                }
            })
            .catch(error => {
                console.error('Error clearing search history:', error);
            });
    }
</script>

</html>
