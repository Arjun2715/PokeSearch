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
    <div class="bg-white  rounded px-8 pt-6 pb-8 mb-4">
        <h1 class="text-2xl text-center font-bold mb-4">Search Pokemon</h1>
        <form class="flex flex-row space-x-4 mb-4"> 
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="pokemon-search" type="text" placeholder="Search Pokemon">
            
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="button">
                    Search
                </button> 
        </form>
    </div>
</body>
</html>
