<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="CSS/style.css">
    <script src="JS/script.js"></script>
</head>
<body class="flex flex-col items-center justify-center gap-y-[6rem] h-screen font-mono">

    <section class="px-8 border-2 rounded-lg">
        <h1 id="timeDisplay">
        </h1>
    </section>

    <section class="flex flex-row items-center justify-center gap-x-4 w-[90%] max-w-[400px] min-h-[250px]">
        <div class=" w-[10%] h-full bg-red-500 rounded-lg">
            <a href="Index.html" class="h-full w-full flex items-center">
                <img src="Media library/Arrow left.png" alt="Knop voor terug naar optie pagina te gaan">
            </a>
        </div>

        <div class="w-[90%] min-h-full px-[20px] py-[20px] sm:px-[40px] sm:py-[20px] border-solid border 1.2px rounded-lg flex flex-col items-center justify-center gap-y-4">
            
            <h2 class="">Login</h2>
            <form action="" class="flex flex-col items-center w-full gap-y-4">
                <div class="flex flex-col items-start w-full gap-y-1 max-w-[280px]">
                    <label for="password">Wachtwoord:</label>
                    <input type="password" name="password" id="password" class="bg-neutral-100 rounded-sm w-full min-h-[30px] ">
                </div>
                <div class="flex items-center justify-center gap-4 max-w-[280px] w-full min-h-[30px] bg-blue-400 rounded-sm mt-2">
                    <input type="submit" value="Login" >
                    <img src="Media library/right-arrow.svg" alt="login in" class="h-[25px]">
                </div>
            </form>
    
        </div>
    </section>
    
</body>
</html>