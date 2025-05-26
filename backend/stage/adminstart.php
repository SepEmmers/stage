<!DOCTYPE html>
<html lang="nl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>damin</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="css/style.css" />
    <script src="js/script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="js/adminstart.js"></script>
  </head>
  <body
    class="flex flex-col items-center justify-center gap-y-[2rem] h-screen font-mono"
  >
    <section class="">
      <div class="px-8 border-2 rounded-lg">
        <h1 id="timeDisplay"></h1>
      </div>

      <h2 id="dagdisplay"></h2>
    </section>

    <section class="flex flex-row items-center justify-center gap-x-4 w-[90%] max-w-[400px] min-h-[250px]">
      <div class="w-[10%] h-full bg-red-500 rounded-lg">
        <a href="logout_admin.php" class="h-full w-full flex items-center">
          <img
            src="Media library/Arrow left.png"
            alt="Knop voor terug naar optie pagina te gaan"
          />
        </a>
      </div>

      <div
        class="w-[90%] min-h-full px-[20px] py-[20px] sm:px-[40px] sm:py-[20px] flex flex-col items-center justify-center gap-y-6">
        <h2 id="naamDisplay">Hey</h2>

      </div>
    </section>

    <section class="flex flex-col items-center w-[90%] max-w-[800px]">
      <div class="flex flex-col sm:flex-row justify-between w-full mb-4 gap-y-2 sm:gap-y-0">
        <div class="flex items-center">
          <label for="filterNaam" class="mr-2">Filter op naam:</label>
          <select id="filterNaam" class="mr-4">
            <option value="">Alle</option>
            </select>
        </div>
        <div class="flex items-center">
          <label for="filterDatum" class="mr-2">Filter op datum:</label>
          <input type="date" id="filterDatum">
        </div>
        <div class="flex items-center">
          <label for="sortBy" class="mr-2">Sorteer op:</label>
          <select id="sortBy">
            <option value="naam_asc">Naam (A-Z)</option>
            <option value="naam_desc">Naam (Z-A)</option>
            <option value="datum_asc">Datum (Oud naar Nieuw)</option>
            <option value="datum_desc">Datum (Nieuw naar Oud)</option>
            <option value="uren_asc">Gewerkte uren (Laag naar Hoog)</option>
            <option value="uren_desc">Gewerkte uren (Hoog naar Laag)</option>
          </select>
        </div>
      </div>
      <div id="historyDisplay">
        </div>
    </section>
  </body>
</html>