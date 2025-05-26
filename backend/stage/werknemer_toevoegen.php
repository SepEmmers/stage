<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuwe Werknemer Toevoegen</title>
</head>
<body>
    <h1>Nieuwe Werknemer Toevoegen</h1>

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

            // Controleer of alle velden zijn ingevuld
            if (!empty($voornaam) && !empty($familienaam) && !empty($pin) && !empty($email) && !empty($gebruikersnaam)) {
                // Voorbereiden van de SQL query om een nieuwe werknemer toe te voegen
                $sql = "INSERT INTO personeel (voornaam, familienaam, pin, email, gebruikersnaam)
                        VALUES (:voornaam, :familienaam, :pin, :email, :gebruikersnaam)";

                $stmt = $conn->prepare($sql);

                // Binden van de parameters
                $stmt->bindParam(':voornaam', $voornaam);
                $stmt->bindParam(':familienaam', $familienaam);
                $stmt->bindParam(':pin', $pin);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':gebruikersnaam', $gebruikersnaam);

                // Uitvoeren van de query
                if ($stmt->execute()) {
                    $melding = "<p style='color: green;'>Nieuwe werknemer succesvol toegevoegd!</p>";
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

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <div>
            <label for="voornaam">Voornaam:</label>
            <input type="text" id="voornaam" name="voornaam" required>
        </div>
        <br>
        <div>
            <label for="familienaam">Familienaam:</label>
            <input type="text" id="familienaam" name="familienaam" required>
        </div>
        <br>
        <div>
            <label for="pin">PIN (4 cijfers):</label>
            <input type="text" id="pin" name="pin" pattern="[0-9]{4}" title="Vul een 4-cijferige PIN in" required>
        </div>
        <br>
        <div>
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <br>
        <div>
            <label for="gebruikersnaam">Gebruikersnaam:</label>
            <input type="text" id="gebruikersnaam" name="gebruikersnaam" required>
        </div>
        <br>
        <input type="submit" value="Werknemer Toevoegen">
    </form>

    <br>
    <a href="adminstart.php">Terug naar Admin Overzicht</a> </body>
</html>