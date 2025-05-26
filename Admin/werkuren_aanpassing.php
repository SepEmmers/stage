<?php
// Database connectie gegevens (pas dit aan naar jouw configuratie)
include "conn.php";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Controleer of het formulier is ingediend via POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        print_r($_POST);
        print_r($_POST['pk_werkuren']);
        print_r($_POST['personeel_fk']);
        print_r($_POST['school_fk']);
        print_r($_POST['job_fk']);
        print_r($_POST['begintijd']);
        print_r($_POST['eindtijd']);
        print_r($_POST['datum']);

        // Haal de gegevens uit het formulier
        $pk_werkuren = isset($_POST['pk_werkuren']) ? $_POST['pk_werkuren'] : null;
        $personeel_fk = isset($_POST['personeel_fk']) ? $_POST['personeel_fk'] : null;
        $starttijd_uur = isset($_POST['begintijd']) ? $_POST['begintijd'] : null;
        $eindtijd_uur = isset($_POST['eindtijd']) ? $_POST['eindtijd'] : null;
        $datum = isset($_POST['datum']) ? $_POST['datum'] : null;
        $school_fk = isset($_POST['school_fk']) ? $_POST['school_fk'] : null;
        $job_fk = isset($_POST['job_fk']) ? $_POST['job_fk'] : null;

        // Controleer of de verplichte velden zijn ingevuld (voeg hier eventueel meer validatie toe)
        if ($pk_werkuren && $personeel_fk && $starttijd_uur && $school_fk && $job_fk) {
            // Combineer de datum en starttijd/eindtijd om de volledige datetime te krijgen
            $starttijd = $datum . " " . $starttijd_uur . ":00"; // Voeg seconden toe
            $eindtijd = $eindtijd_uur ? $datum . " " . $eindtijd_uur . ":00" : null; // Voeg seconden toe

            // Voorbereiden van de SQL query om de stored procedure aan te roepen
            $sql = "CALL UpdateWerkuren(:pk_werkuren, :personeel_fk, :starttijd, :eindtijd, :datum, :school_fk, :job_fk)";

            $stmt = $conn->prepare($sql);

            // Binden van de parameters
            $stmt->bindParam(':pk_werkuren', $pk_werkuren, PDO::PARAM_INT);
            $stmt->bindParam(':personeel_fk', $personeel_fk, PDO::PARAM_INT);
            $stmt->bindParam(':starttijd', $starttijd);
            $stmt->bindParam(':eindtijd', $eindtijd);
            $stmt->bindParam(':datum', $datum);
            $stmt->bindParam(':school_fk', $school_fk);
            $stmt->bindParam(':job_fk', $job_fk);

            // Uitvoeren van de query
            if ($stmt->execute()) {
                // Succesvol geüpdatet, stuur de gebruiker terug naar de werkgeschiedenis pagina
                header("Location: admin.html?melding=succes"); // Vervang index.php door de juiste pagina
                exit();
            } else {
                // Fout bij het updaten
                echo "Er is een fout opgetreden bij het opslaan van de wijzigingen.";
                error_log("Fout bij het updaten van werkuur met ID: " . $pk_werkuren . ". Error: " . print_r($stmt->errorInfo(), true));
            }
        } else {

            echo "Niet alle verplichte velden zijn ingevuld.";
        }
    } else {

        echo "Deze pagina kan alleen via een POST request worden benaderd.";
    }

} catch(PDOException $e) {
    sleep(100);
    echo "Fout bij de databaseverbinding: " . $e->getMessage();
    error_log("Database verbindingsfout in verwerk_aanpassing.php: " . $e->getMessage());
}

$conn = null; // Sluit de database verbinding
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verwerken...</title>
</head>
<body>
    <p><a href="admin.html">Terug naar overzicht</a></p> </body>
</html>