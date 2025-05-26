<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persoon wijzigen</title>
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
        <h2 class="text-white ">Persoon wijzigen</h2>
    </section>

    <article class="flex flex-col items-center w-full flex-grow relative">

        <div class="w-full min-h-[100px]">
        </div>

        <section class="flex flex-col items-center justify-center w-full md:w-[80%] max-w-[1200px] min-h-[130px] shadow-md noto-sans-bold absolute top-[20px] bg-white">
            <a href="personen.html" class="w-full relative">
                <img src="media library/back arrow.webp" alt="Terug naar vorige pagina" class="w-[45px] h-[45px] absolute top-[-20px] left-[-20px] p-2 bg-terug-rood rounded-full ">
            </a>

            <h1>Persoon naam</h1>
        </section>


        <section class="flex flex-col items-end gap-16 w-full flex-grow bg-background ">

            <div class="min-h-[50px] w-full"></div>


            <div class="w-full md:w-[80%] md:max-w-[1000px] mx-auto mb-12">
<?php
    // Controleer of de 'id' parameter aanwezig is in de URL
    if (isset($_GET['id'])) {
        $personeel_id = $_GET['id'];

        // Database connectie gegevens (pas dit aan naar jouw configuratie in conn.php)
        include "conn.php";

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Voorbereiden van de stored procedure call
            $stmt = $conn->prepare("CALL usp_GetPersoneelId(:personeel_id)");
            $stmt->bindParam(':personeel_id', $personeel_id, PDO::PARAM_INT);
            $stmt->execute();

            // Ophalen van de resultaten als een associatieve array
            $personeel = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($personeel) {
                ?>


                    <form action="personeel_aanpassing.php" method="POST" class="flex flex-row justify-center flex-wrap gap-x-20 gap-y-4 w-full">
                        <input type="hidden" name="pk_personeel" value="<?php echo htmlspecialchars($personeel['pk_personeel']); ?>">
                        <div class="flex flex-col gap-2 w-[45%] max-w-[400px]">
                            <label for="voornaam" class="noto-sans-bold">Voornaam:</label>
                            <input type="text" id="voornaam" name="voornaam" value="<?php echo htmlspecialchars($personeel['voornaam']); ?>" class="bg-white min-h-[50px] px-4 rounded-md text-xl">
                        </div>
                        <div class="flex flex-col gap-2 w-[45%] max-w-[400px]">
                            <label for="familienaam" class="noto-sans-bold">Familienaam:</label>
                            <input type="text" id="familienaam" name="familienaam" value="<?php echo htmlspecialchars($personeel['familienaam']); ?>" class="bg-white min-h-[50px] px-4 rounded-md text-xl">
                        </div>
                        <div class="flex flex-col gap-2 w-[45%] max-w-[400px]">
                            <label for="pin" class="noto-sans-bold">Pincode:</label>
                            <input type="password" id="pin" name="pin" value="<?php echo htmlspecialchars($personeel['pin']); ?>" class="bg-white min-h-[50px] px-4 rounded-md text-xl">
                            <button type="" onclick="togglePasswordVisibility(event)">Show Password</button>
                        </div>
                        <div class="flex flex-col gap-2 w-[45%] max-w-[400px]">
                            <label for="gebruikersnaam" class="noto-sans-bold">Gebruikersnaam:</label>
                            <input type="text" id="gebruikersnaam" name="gebruikersnaam" value="<?php echo htmlspecialchars($personeel['gebruikersnaam']); ?>" class="bg-white min-h-[50px] px-4 rounded-md text-xl">
                        </div>
                        <div class="flex flex-col gap-2 w-[45%] max-w-[400px]">
                            <label for="email" class="noto-sans-bold">Email:</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($personeel['email']); ?>" class="bg-white min-h-[50px] px-4 rounded-md text-xl">
                        </div>

                <div class="flex flex-col gap-2 w-[45%] max-w-[400px]">
                    <label for="actief">Actief:</label>
                    <select id="actief" name="actief" class="bg-white min-h-[50px] px-4 rounded-md text-xl">
                        <option value="0" <?php if ($personeel['actief'] == 0) echo 'selected'; ?>>Ja</option>
                        <option value="1" <?php if ($personeel['actief'] == 1) echo 'selected'; ?>>Nee</option>
                    </select>
                        </div>

                                <button type="submit" class="button rounded-md bg-toevoegenAanpassen cursor-pointer">
                                    <img src="media library/plus.png" alt="Toepassen" class="img-button">
                                    <h2>Toepassen</h2>
                                </button>
                    </form>

    <script>

function togglePasswordVisibility(event) {
    event.preventDefault();
  const passwordInput = document.getElementById('pin');
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
  } else {
    passwordInput.type = 'password';
  }
}

            </script>

    <?php
            } else {
                echo "<p>Er is geen personeelslid gevonden met de opgegeven ID.</p>";
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

    }
    ?>

   </div>


                </section>

        </article>
</body>
</html>