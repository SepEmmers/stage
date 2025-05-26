<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Werkuren wijzigen</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="css/tailwind.css">
    <link rel="stylesheet" href="css/font.css">
    <script src="js/script.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <style type="text/tailwindcss">

        @theme {

            /* basis kleuren */
            --color-default-grey: oklch(88.53% 0 0);

            --color-spc-green: oklch(72.13% 0.1721 126.07);

            /* background page */
            --color-background: oklch(95.81% 0 0);

            /* text */
            --color-text-white: #fff;

            /* button toevoegen/aanpassen/verwijderen */
            --color-toevoegenAanpassen: oklch(84.26% 0.2256 129.65);

            --color-verwijderen: oklch(58.3% 0.238666 28.4765);

            /* button terug/uitloggen */
            --color-terug-rood: oklch(63.78% 0.2373 25.44);

            /* admin */
            --color-admin: oklch(67.98% 0.171156 256.1873);

            --color-rooster: oklch(65.67% 0 0);

            --color-achtergrond2: oklch(90.67% 0 0);
        }

    </style>
    
</head>
<body class="flex flex-col items-center min-h-dvh noto-sans-normal">

    <section class="flex flex-row items-center justify-center dag bg-admin">
        <h2 class="text-white ">Werkuren wijzigen</h2>
    </section>

    <article class="flex flex-col items-center w-full flex-grow relative">

        <!--div om de achtergrond niet helemaal tot boven te laten gaan-->
        <div class="w-full min-h-[100px]">
        </div>

        <section class="flex flex-col items-center justify-center w-full md:w-[80%] max-w-[1200px] min-h-[130px] shadow-md noto-sans-bold absolute top-[20px] bg-white">
            <a href="admin.html" class="w-full relative">
                <img src="media library/back arrow.webp" alt="Terug naar vorige pagina" class="w-[45px] h-[45px] absolute top-[-20px] left-[-20px] p-2 bg-terug-rood rounded-full ">
            </a>
            <h1>Werkuren wijzigen</h1>
        </section>

        <section class="flex flex-col items-end gap-16 w-full flex-grow bg-background ">

            <!--div om de verborgen height in te pakken-->
            <div class="min-h-[50px] w-full"></div>

            
            <div class="w-full md:w-[80%] md:max-w-[1000px] mx-auto mb-12">
            <?php
    // Controleer of de 'id' en 'pk' parameters aanwezig zijn in de URL
    if (isset($_GET['id']) && isset($_GET['pk']) && isset($_GET['school_pk']) && isset($_GET['job_pk'])) {
        $personeel_id = $_GET['id'];
        $werkuren_id = $_GET['pk'];
        $school_id = $_GET['school_pk'];
        $job_id = $_GET['job_pk'];

        // Database connectie gegevens (pas dit aan naar jouw configuratie in conn.php)
        include "conn.php";


        try {
            $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Voorbereiden van de stored procedure call
            $stmt = $conn->prepare("CALL usp_GetWerkuurByIdAndPersoneelId(:werkuur_id, :personeel_id, :school_id, :job_id)");
            $stmt->bindParam(':werkuur_id', $werkuren_id, PDO::PARAM_INT);
            $stmt->bindParam(':personeel_id', $personeel_id, PDO::PARAM_INT);
            $stmt->bindParam(':school_id', $school_id, PDO::PARAM_INT);
            $stmt->bindParam(':job_id', $job_id, PDO::PARAM_INT);
            $stmt->execute();

            // Ophalen van de resultaten als een associatieve array
            $werkuur = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($werkuur) {
                // Formatteer de start- en eindtijd naar alleen de uren
                $starttijd_uur = date('H:i', strtotime($werkuur['starttijd']));
                $eindtijd_uur = $werkuur['eindtijd'] ? date('H:i', strtotime($werkuur['eindtijd'])) : '';
                ?>
                <form action="werkuren_aanpassing.php" method="POST" class="flex flex-row justify-center flex-wrap gap-x-20 gap-y-4 w-full">
                    <input type="hidden" name="pk_werkuren" value="<?php echo htmlspecialchars($werkuur['pk_werkuren']); ?>">
                    <input type="hidden" name="personeel_fk" value="<?php echo htmlspecialchars($werkuur['personeel_fk']); ?>">
                    <input type="hidden" name="school_fk" value="<?php echo htmlspecialchars($werkuur['school_fk']); ?>">
                    <input type="hidden" name="job_fk" value="<?php echo htmlspecialchars($werkuur['job_fk']); ?>">

                    <div class="flex flex-col gap-2 w-[45%] max-w-[400px]">
                    <label for="naam" class="noto-sans-bold">naam:</label>
                    <input type="text" id="naam" name="naam" value="<?php echo htmlspecialchars($werkuur['VolledigeNaam']); ?>" disabled class="bg-white min-h-[50px] px-4 rounded-md text-xl">
                    </div>
                    <div class="flex flex-col gap-2 w-[45%] max-w-[400px]">
                        <label for="datum" class="noto-sans-bold">Datum:</label>
                        <input type="date" id="datum" name="datum" value="<?php echo htmlspecialchars($werkuur['datum']); ?>" readonly class="bg-white min-h-[50px] px-4 rounded-md text-xl">
                    </div>
                    <div class="flex flex-col gap-2 w-[45%] max-w-[400px]">
                        <label for="begintijd" class="noto-sans-bold">begintijd:</label>
                        <input type="time" id="begintijd" name="begintijd" value="<?php echo htmlspecialchars($starttijd_uur); ?>" class="bg-white min-h-[50px] px-4 rounded-md text-xl">
                    </div>
                    <div class="flex flex-col gap-2 w-[45%] max-w-[400px]">
                        <label for="eindtijd" class="noto-sans-bold">Eindtijd:</label>
                        <input type="time" id="eindtijd" name="eindtijd" value="<?php echo htmlspecialchars($eindtijd_uur); ?>" class="bg-white min-h-[50px] px-4 rounded-md text-xl">
                    </div>



                    <button type="submit" value="Opslaan" class="button rounded-md bg-toevoegenAanpassen cursor-pointer">
                        <img src="media library/plus.png" alt="Toepassen" class="img-button">
                        <h2>Toepassen</h2>
                    </button>
                    
                </form>
                <?php
            } else {
                echo "<p>Er is geen werkuur gevonden met de opgegeven ID's.</p>";
            }

            // Sluit de cursor (optioneel bij stored procedures in sommige PDO drivers)
            $stmt->closeCursor();

        } catch(PDOException $e) {
            echo "Fout bij het ophalen van de gegevens: " . htmlspecialchars($e->getMessage());
        } finally {
            // Sluit de database verbinding, ook als er een fout is opgetreden
            if ($conn !== null) {
                $conn = null;
            }
        }
    } else {
        echo "<p>Er zijn geen werkuren geselecteerd om aan te passen.</p>";
    }
    ?>

            </div>
            
        </section>

    </article>
   
</body>
</html>