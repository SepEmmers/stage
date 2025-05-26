<?php
// Database connectie gegevens (pas dit aan naar jouw configuratie)
include "conn.php";

echo "<p><strong>Debugging (personeel_aanpassing.php):</strong></p>";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>Database verbinding succesvol.</p>";

    // Controleer of het formulier is ingediend via POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        echo "<p>Request methode is POST.</p>";
        echo "<p><strong>Inhoud van \$_POST array:</strong></p>";
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";

        // Haal de gegevens uit het formulier
        $pk_personeel = isset($_POST['pk_personeel']) ? $_POST['pk_personeel'] : null;
        $voornaam = isset($_POST['voornaam']) ? $_POST['voornaam'] : null;
        $familienaam = isset($_POST['familienaam']) ? $_POST['familienaam'] : null;
        $pin = isset($_POST['pin']) ? $_POST['pin'] : null;
        $email = isset($_POST['email']) ? $_POST['email'] : null;
        $gebruikersnaam = isset($_POST['gebruikersnaam']) ? $_POST['gebruikersnaam'] : null;
        $actief = isset($_POST['actief']) ? $_POST['actief'] : 0; // Standaard op 0 zetten als niet gespecificeerd

        echo "<p>pk_personeel: " . htmlspecialchars(print_r($pk_personeel, true)) . "</p>";
        echo "<p>voornaam: " . htmlspecialchars(print_r($voornaam, true)) . "</p>";
        echo "<p>familienaam: " . htmlspecialchars(print_r($familienaam, true)) . "</p>";
        echo "<p>pin: " . htmlspecialchars(print_r($pin, true)) . "</p>";
        echo "<p>email: " . htmlspecialchars(print_r($email, true)) . "</p>";
        echo "<p>gebruikersnaam: " . htmlspecialchars(print_r($gebruikersnaam, true)) . "</p>";
        echo "<p>actief: " . htmlspecialchars(print_r($actief, true)) . "</p>";


        // Controleer of de verplichte velden zijn ingevuld
        if ($pk_personeel && $voornaam && $familienaam && $pin && $email && $gebruikersnaam !== null) {
            // Voorbereiden van de SQL query om de stored procedure aan te roepen
            $sql = "CALL UpdatePersoneel(:pk_personeel, :voornaam, :familienaam, :pin, :email, :gebruikersnaam, :actief)";

            $stmt = $conn->prepare($sql);
            echo "<p>SQL query voorbereid: " . htmlspecialchars($sql) . "</p>";

            // Binden van de parameters
            $stmt->bindParam(':pk_personeel', $pk_personeel, PDO::PARAM_INT);
            $stmt->bindParam(':voornaam', $voornaam, PDO::PARAM_STR);
            $stmt->bindParam(':familienaam', $familienaam, PDO::PARAM_STR);
            $stmt->bindParam(':pin', $pin, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':gebruikersnaam', $gebruikersnaam, PDO::PARAM_STR);
            $stmt->bindParam(':actief', $actief, PDO::PARAM_INT); // Let op: boolean in stored procedure, maar hier int (0 of 1)
            echo "<p>Parameters gebonden.</p>";

            // Uitvoeren van de query
            if ($stmt->execute()) {
                echo "<p>Query succesvol uitgevoerd.</p>";
                // Succesvol geüpdatet, stuur de gebruiker terug naar de overzichtspagina
                header("Location: personen.html?melding=personeel_bijgewerkt"); // Pas de redirect-URL aan indien nodig
                exit();
            } else {
                // Fout bij het updaten
                echo "<p><strong>Er is een fout opgetreden bij het bijwerken van de personeelsgegevens.</strong></p>";
                $errorInfo = $stmt->errorInfo();
                echo "<p><strong>Foutinformatie:</strong></p>";
                echo "<pre>";
                print_r($errorInfo);
                echo "</pre>";
                error_log("Fout bij het bijwerken van personeelslid met ID: " . $pk_personeel . ". Error: " . print_r($stmt->errorInfo(), true));
            }
        } else {
            echo "<p><strong>Niet alle verplichte velden zijn ingevuld. Controleer de \$_POST array.</strong></p>";
        }
    } else {
        echo "<p>Deze pagina kan alleen via een POST request worden benaderd.</p>";
    }

} catch(PDOException $e) {
    echo "<p><strong>Fout bij de databaseverbinding:</strong> " . $e->getMessage() . "</p>";
    error_log("Database verbindingsfout in personeel_aanpassing.php: " . $e->getMessage());
}

$conn = null; // Sluit de database verbinding
echo "<p>Database verbinding gesloten (aan het einde van het script).</p>";
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verwerken...</title>
</head>
<body>
    <p><a href="personen.php">Terug naar overzicht</a></p>
</body>
</html>