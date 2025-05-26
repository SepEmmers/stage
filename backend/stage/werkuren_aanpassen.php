<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Werkuren Aanpassen</title>
</head>
<body>
    <h1>Werkuren Aanpassen</h1>

    <?php
    // Controleer of de 'id' en 'pk' parameters aanwezig zijn in de URL
    if (isset($_GET['id']) && isset($_GET['pk'])) {
        $personeel_id = $_GET['id'];
        $werkuren_id = $_GET['pk'];

        // Database connectie gegevens (pas dit aan naar jouw configuratie in conn.php)
        include "conn.php";


        try {
            $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Voorbereiden van de stored procedure call
            $stmt = $conn->prepare("CALL usp_GetWerkuurByIdAndPersoneelId(:werkuur_id, :personeel_id)");
            $stmt->bindParam(':werkuur_id', $werkuren_id, PDO::PARAM_INT);
            $stmt->bindParam(':personeel_id', $personeel_id, PDO::PARAM_INT);
            $stmt->execute();

            // Ophalen van de resultaten als een associatieve array
            $werkuur = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($werkuur) {
                // Formatteer de start- en eindtijd naar alleen de uren
                $starttijd_uur = date('H:i', strtotime($werkuur['starttijd']));
                $eindtijd_uur = $werkuur['eindtijd'] ? date('H:i', strtotime($werkuur['eindtijd'])) : '';
                ?>
                <form action="verwerk_aanpassing.php" method="POST">
                    <input type="hidden" name="pk_werkuren" value="<?php echo htmlspecialchars($werkuur['pk_werkuren']); ?>">
                    <input type="hidden" name="personeel_fk" value="<?php echo htmlspecialchars($werkuur['personeel_fk']); ?>">
                    <input type="hidden" name="school_fk" value="<?php echo htmlspecialchars($werkuur['school_fk']); ?>">
                    <input type="hidden" name="job_fk" value="<?php echo htmlspecialchars($werkuur['job_fk']); ?>">

                    <div>
                        <label for="starttijd">Starttijd (uur):</label>
                        <input type="time" id="starttijd" name="starttijd" value="<?php echo htmlspecialchars($starttijd_uur); ?>">
                    </div>
                    <br>
                    <div>
                        <label for="eindtijd">Eindtijd (uur):</label>
                        <input type="time" id="eindtijd" name="eindtijd" value="<?php echo htmlspecialchars($eindtijd_uur); ?>">
                    </div>
                    <br>
                    <div>
                        <label for="datum">Datum:</label>
                        <input type="date" id="datum" name="datum" value="<?php echo htmlspecialchars($werkuur['datum']); ?>">
                    </div>
                    <br>
                    <input type="submit" value="Opslaan">
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

    <br>
    <a href="adminstart.php">Terug naar overzicht</a>
</body>
</html>