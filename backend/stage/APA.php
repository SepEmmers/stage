<?php

// Zet error reporting aan voor debugging (verwijder dit in productie)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Standaard response array
$response = array('success' => false, 'error' => '');

try {
    include "conn.php";

    // Controleer of de verbinding is gelukt
    if (!$conn) {
        throw new Exception("Databaseverbinding mislukt: " . mysqli_connect_error());
    }

    if (isset($_POST['get_monthly_history_admin'])) {
        $year = $_POST['year'];
        $month = $_POST['month'];

        $sql = "CALL usp_GetMonthlyWorkHistoryadmin(?, ?)";
        $stmt = $conn->prepare($sql);

        // Controleer of het statement is voorbereid
        if ($stmt === false) {
            throw new Exception("Fout bij het voorbereiden van de query: " . $conn->error);
        }

        $stmt->bind_param("ii", $year, $month);

        // Controleer of de parameters zijn gebonden
        if ($stmt->errno) {
            throw new Exception("Fout bij het binden van parameters: " . $stmt->error);
        }

        $stmt->execute();

        // Controleer of de query is uitgevoerd
        if ($stmt->errno) {
            throw new Exception("Fout bij het uitvoeren van de query: " . $stmt->error);
        }

        $result = $stmt->get_result();

        // Controleer of er een resultaat is
        if ($result === false) {
            throw new Exception("Fout bij het ophalen van het resultaat: " . $stmt->error);
        }

        $historyData = array();
        while ($row = $result->fetch_assoc()) {
            $historyData[]= $row;
        }

        $response['success'] = true;
        $response['data'] = $historyData;
        echo json_encode($response);

        $stmt->close();
    } elseif (isset($_POST['get_start_info'])) {
        // Hier komt de code voor het verwerken van get_start_info indien nodig
        // Op dit moment doet de ajaxcall functie in je JS niets met de response
        $response['success'] = true; // Voorbeeld response
        echo json_encode($response);
    } elseif (isset($_GET['id']) && isset($_GET['pk'])) {
        $id = $_GET['id']; // Dit is de pk_personeel
        $pk = $_GET['pk']; // Dit is de pk_werkuren

        // Roep de stored procedure aan om werkuren gegevens op te halen
        $sql = "CALL usp_GetWerkuurByIdAndPersoneelId(?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            throw new Exception("Fout bij het voorbereiden van de query: " . $conn->error);
        }

        // Let op de volgorde: eerst pk (pk_werkuren), dan id (pk_personeel)
        $stmt->bind_param("ii", $pk, $id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $data = $result->fetch_assoc();
                $response['success'] = true;
                $response['data'] = $data;
            } else {
                $response['error'] = 'Geen werkuren gevonden met de opgegeven ID en personeelslid.';
            }
        } else {
            throw new Exception("Fout bij het uitvoeren van de query: " . $stmt->error);
        }
        echo json_encode($response);
        $stmt->close();
    } else {
        $response['error'] = 'Ongeldige request.';
        echo json_encode($response);
    }

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    echo json_encode($response);
} finally {
    // Sluit de verbinding in de finally block om er zeker van te zijn dat het gebeurt
    if (isset($conn)) {
        mysqli_close($conn);
    }
}
?>