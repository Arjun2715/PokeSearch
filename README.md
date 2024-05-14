Descripción
Crear una webapp simple que permita al usuario introducir un término de búsqueda y
mostrar el resultado obtenido a través de una API.
El historial de búsqueda debe guardarse en una base de datos y el usuario tiene que ver en
todo momento sus últimas 10 búsquedas que hayan tenido éxito (hay que diferenciar entre
sesiones, búsquedas desde navegadores distintos deben tener historiales distintos).
El usuario puede hacer click en una de las últimas búsquedas para volver a hacer la
búsqueda.
Consideraciones
- La aplicación tiene que estar desarrollada en Laravel.
- Se usará la API de Pokémon: https://pokeapi.co/
- El objetivo será buscar un Pokémon y en caso de encontrarlo mostrar el nombre de sus
habilidades en español.
- Los resultados deben mostrarse sin refrescar la vista, así como actualizar el historial.
- Desde Javascript se llamará a un proxy desarrollado en el back y este proxy será el que
gestione la llamada a la API y retorne la respuesta.
- En esta prueba técnica se valorará el desarrollo y la estructura del backend, se espera un
frontend que simplemente permita la funcionalidad requerida. (No importa el diseño).

Indicaciones
- Facilitar un fichero README.txt con las tecnologías usadas (en caso de usar algún extra),
versiones y como hacer el setup del entorno para probar el código.
- Indicar también cualquier consideración extra que se haya hecho o decisión durante el
desarrollo.