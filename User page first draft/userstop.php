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
<body class="flex flex-col items-center justify-center gap-y-[2rem] h-screen font-mono">

    <section class="">
        <div class="px-8 border-2 rounded-lg">
            <h1 id="timeDisplay">
            </h1>
        </div>
        
        <h2 id="dagdisplay">
        </h2>
    </section>

    <section class="flex flex-row items-center justify-center gap-x-4 w-[90%] max-w-[400px] min-h-[250px]">
        
        <div class=" w-[10%] h-full bg-red-500 rounded-lg">
            <a href="Index.html" class="h-full w-full flex items-center">
                <img src="Media library/Arrow left.png" alt="Knop voor terug naar optie pagina te gaan">
            </a>
        </div>

        <div class="w-[90%] min-h-full px-[20px] py-[20px] sm:px-[40px] sm:py-[20px] flex flex-col items-center justify-center gap-y-12">
            
            <h2>Hey angiecha</h2>

            <button>
            <h2 class="bg-red-400 px-8 py-1 rounded-md">stop</h2>
            </button>
        </div>

    </section>

    <section>
        <button class="flex flex-row items-center justify-center gap-x-4 max-w-[400px]  px-[20px] py-[10px] sm:px-[20px] sm:py-[10px] bg-neutral-200 rounded-lg">
            <h2>Geschiedenis</h2>
            <img src="media library/clock.png" alt="" class="w-[40px] h-[40px]">
        </button>
    </section>
    
</body>
</html>