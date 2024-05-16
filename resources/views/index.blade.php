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
                <!-- Los elementos del historial de búsqueda se agregarán dinámicamente aquí -->
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
        // Obtener el historial de búsqueda cuando la página se carga
        fetchSearchHistory();
        // Agregar un event listener para limpiar todo el historial de búsqueda
        document.getElementById('clear-history-button').addEventListener('click', function() {
            clearSearchHistory();
        });
    };


    document.getElementById('search-button').addEventListener('click', function() {
        var searchTerm = document.getElementById('pokemon-search').value.toLowerCase();
        // Evitar búsquedas duplicadas
        if (searchTerm === lastSearchTerm) {
            console.log('Término de búsqueda duplicado, omitiendo búsqueda.');
            return; // Salir de la función sin ejecutar la búsqueda
        }
        // Realizar una solicitud AJAX a tu backend
        fetch('/search', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content'),
                },
                body: JSON.stringify({
                    search_term: searchTerm
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    // Handle errors
                    console.error('Error:', data.error);
                } else {
                    console.log(data);
                    var pokemonInfo = document.getElementById('pokemon-info');

                    pokemonInfo.innerHTML = '<h2> Nombre: ' + data.name + '</h2>';
                    pokemonInfo.innerHTML += '<p>Peso: ' + data.weight + '</p>';

                    pokemonInfo.innerHTML += '<ul>'; 
                        //habiliodades
                    data.abilities.forEach(function(ability) {
                        fetch(ability.ability.url)
                            .then(response => response.json())
                            .then(abilityData => {
                                const spanishEntries = abilityData.flavor_text_entries.filter(
                                    entry => entry.language.name === 'es');
                                if (spanishEntries.length > 0) {
                                    pokemonInfo.innerHTML += '<li>' + spanishEntries[0].flavor_text + '</li>';
                                }
                            }) 
                    });
                    pokemonInfo.innerHTML += '</ul>';
                    pokemonInfo.innerHTML += '<audio controls><source src="' + data.cries.latest +'" type="audio/ogg">Your browser does not support the audio element.</audio>';
             
                    pokemonInfo.innerHTML += '<h3>Sprites:</h3>';
                    pokemonInfo.innerHTML += '<img src="' + data.sprites.front_default +'" alt="Front Default">';
                    pokemonInfo.innerHTML += '<img src="' + data.sprites.back_default +'" alt="Back Default">'; 
                    pokemonInfo.innerHTML += '<img src="' + data.sprites.other.dream_world.front_default +'" alt="Dream World">'; 

                    
                }
            }) 
  
    });

    function removeDuplicates(array, key) {
        return array.filter((obj, index, self) =>
            index === self.findIndex(entry => entry[key] === obj[key])
        );
    }


    // Función para obtener y mostrar el historial de búsqueda
    function fetchSearchHistory() {
        fetch('/search-history')
            .then(response => response.json())
            .then(data => {
                // Limpiar el historial de búsqueda existente
                document.getElementById('search-history-list').innerHTML = '';
                // Iterar sobre los datos del historial de búsqueda y generar HTML para cada elemento
                data.forEach(function(item) {
                    var listItem = document.createElement('li');
                    listItem.textContent = item.search_term;
                    // Agregar un event listener de click a cada elemento del historial
                    listItem.addEventListener('click', function() {
                        // Establecer el valor de entrada de búsqueda al elemento de historial clicado
                        document.getElementById('pokemon-search').value = item.search_term;
                        // Activar una nueva búsqueda
                        document.getElementById('search-button').click();
                    });
                    // Agregar un botón de eliminar para cada elemento del historial
                    var deleteButton = document.createElement('button');
                    deleteButton.textContent = '<---Eliminar';
                    deleteButton.addEventListener('click', function(event) {
                        event
                            .stopPropagation(); // Prevenir que el clic active el evento de clic del padre
                        deleteSearchHistoryItem(item.id);
                    });
                    listItem.appendChild(deleteButton);
                    document.getElementById('search-history-list').appendChild(listItem);
                });
                lastSearchTerm = '';
            })
            .catch(error => {
                console.error('Error al obtener historial de búsqueda:', error);
            });
    }


    // Función para eliminar un elemento del historial de búsqueda
    function deleteSearchHistoryItem(itemId) {
        fetch('/delete-search-history/' + itemId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            })
            .then(response => {
                if (response.ok) {
                    // Recargar el historial de búsqueda después de la eliminación
                    fetchSearchHistory();
                } else {
                    console.error('Error al eliminar elemento del historial de búsqueda.');
                }
            })
            .catch(error => {
                console.error('Error al eliminar elemento del historial de búsqueda:', error);
            });
    }


    // Función para limpiar todo el historial de búsqueda
    function clearSearchHistory() {
        fetch('/clear-search-history', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            })
            .then(response => {
                if (response.ok) {
                    // Recargar el historial de búsqueda después de limpiar
                    fetchSearchHistory();
                } else {
                    console.error('Error al limpiar historial de búsqueda.');
                }
            })
            .catch(error => {
                console.error('Error al limpiar historial de búsqueda:', error);
            });
    }
</script>

</html>
