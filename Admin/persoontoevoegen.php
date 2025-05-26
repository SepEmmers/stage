<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persoon toevoegn</title>
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

<?php
// Database connectie gegevens (pas dit aan naar jouw configuratie in conn.php)
include "conn.php";

$conn = null; // Initialiseer de connectie variabele buiten de try-catch
$melding = "";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $voornaam = isset($_POST['voornaam']) ? $_POST['voornaam'] : '';
        $familienaam = isset($_POST['familienaam']) ? $_POST['familienaam'] : '';
        $pin = isset($_POST['pin']) ? $_POST['pin'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $gebruikersnaam = isset($_POST['gebruikersnaam']) ? $_POST['gebruikersnaam'] : '';
        $nfcid = isset($_POST['nfcid']) ? $_POST['nfcid'] : '';

        // Controleer of alle velden zijn ingevuld
        if (!empty($voornaam) && !empty($familienaam) && !empty($pin) && !empty($email) && !empty($gebruikersnaam) && !empty($nfcid)) {
            // Voorbereiden van de SQL query om de stored procedure aan te roepen
            $sql = "CALL VoegPersoneelToe(:voornaam, :familienaam, :pin, :email, :gebruikersnaam, :nfcid)";

            $stmt = $conn->prepare($sql);

            // Binden van de parameters
            $stmt->bindParam(':voornaam', $voornaam);
            $stmt->bindParam(':familienaam', $familienaam);
            $stmt->bindParam(':pin', $pin);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':gebruikersnaam', $gebruikersnaam);
            $stmt->bindParam(':nfcid', $nfcid);

            // Uitvoeren van de query
            if ($stmt->execute()) {
                $melding = "<p style='color: green;'>Nieuwe werknemer succesvol toegevoegd!</p>";
                header("Location: personen.html");
                exit();
            } else {
                $melding = "<p style='color: red;'>Er is een fout opgetreden bij het toevoegen van de werknemer.</p>";
                error_log("Fout bij het toevoegen van een nieuwe werknemer: " . print_r($stmt->errorInfo(), true));
            }
        } else {
            $melding = "<p style='color: orange;'>Vul alle velden in.</p>";
        }
    }
} catch(PDOException $e) {
    $melding = "<p style='color: red;'>Fout bij de databaseverbinding: " . htmlspecialchars($e->getMessage()) . "</p>";
    error_log("Database verbindingsfout in werknemer_toevoegen.php: " . $e->getMessage());
} finally {
    // Sluit de database verbinding
    if ($conn !== null) {
        $conn = null;
    }
}

    echo $melding;
    ?>

    <section class="flex flex-row items-center justify-center dag bg-admin">
        <h2 class="text-white ">Persoon toevoegen</h2>
    </section>

    <article class="flex flex-col items-center w-full flex-grow relative">

        <!--div om de achtergrond niet helemaal tot boven te laten gaan-->
        <div class="w-full min-h-[100px]">
        </div>

        <section class="flex flex-col items-center justify-center w-full md:w-[80%] max-w-[1200px] min-h-[130px] shadow-md noto-sans-bold absolute top-[20px] bg-white">
            
            <a href="personen.html" class="w-full relative">
                <img src="media library/back arrow.webp" alt="Terug naar vorige pagina" class="w-[45px] h-[45px] absolute top-[-20px] left-[-20px] p-2 bg-terug-rood rounded-full ">
            </a>
            <h1>Persoon naam</h1>
            
        </section>


        <section class="flex flex-col items-end gap-16 w-full flex-grow bg-background ">

            <!--div om de verborgen height in te pakken-->
            <div class="min-h-[50px] w-full"></div>

            <div class="w-full md:w-[80%] md:max-w-[1000px] mx-auto mb-12">

                <form id="myForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="flex flex-row justify-center flex-wrap gap-x-20 gap-y-4 w-full">
                        <div class="flex flex-col gap-2 w-[45%] max-w-[400px]">
                            <label for="voornaam" class="noto-sans-bold">Voornaam:</label>
                            <input type="text" id="voornaam" name="voornaam" class="bg-white min-h-[50px] px-4 rounded-md text-xl">
                        </div>
                        <div class="flex flex-col gap-2 w-[45%] max-w-[400px]">
                            <label for="familienaam" class="noto-sans-bold">Familienaam:</label>
                            <input type="text" id="familienaam" name="familienaam" class="bg-white min-h-[50px] px-4 rounded-md text-xl">
                        </div>
                        <div class="flex flex-col gap-2 w-[45%] max-w-[400px]">
                            <label for="pin" class="noto-sans-bold">Pincode:</label>
                            <input type="password" id="pin" name="pin" class="bg-white min-h-[50px] px-4 rounded-md text-xl">
                        </div>
                        <div class="flex flex-col gap-2 w-[45%] max-w-[400px]">
                            <label for="gebruikersnaam" class="noto-sans-bold">Gebruikersnaam:</label>
                            <input type="text" id="gebruikersnaam" name="gebruikersnaam" class="bg-white min-h-[50px] px-4 rounded-md text-xl">
                        </div>
                        <div class="flex flex-col gap-2 w-[45%] max-w-[400px]">
                            <label for="nfcid" class="noto-sans-bold">NFC-id:</label>
                            <input type="password" id="nfcid" name="nfcid" class="bg-white min-h-[50px] px-4 rounded-md text-xl">
                            </div>
                        <div class="flex flex-col gap-2 w-[45%] max-w-[400px]">
                            <label for="email" class="noto-sans-bold">Email:</label>
                            <input type="email" id="email" name="email" class="bg-white min-h-[50px] px-4 rounded-md text-xl">
                        </div>





                        <button type="submit" class="button rounded-md bg-toevoegenAanpassen cursor-pointer mt-8">
                            <img src="media library/plus.png" alt="Toevoegen" class="img-button">
                            <h2>Toevoegen</h2>
                        </button>
                        
                </form>

            </div>
            
        </section>

    </article>

    <script>

const form = document.getElementById('myForm');

form.addEventListener('submit', function(event) {
    event.preventDefault();
    // Voeg eventuele andere logica hier toe, bv. validatie
    // Als je wilt, kun je hier ook een ander gedrag implementeren
    // als de gebruiker op Enter drukt, bv. de volgende focus, of een andere actie
});

    </script>
   
</body>
</html>