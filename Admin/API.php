<?php
session_start(); // Zorg ervoor dat de sessie gestart is

include "conn.php"; // Includeren van het database connectie bestand

// Endpoint om de personeel_id vanuit de sessie te retourneren
if (isset($_POST['get_session_personeel_id'])) {
    if (isset($_SESSION['personeel_id'])) {
        echo json_encode(['personeel_id' => $_SESSION['personeel_id']]);
    } else {
        echo json_encode(['personeel_id' => null, 'error' => 'Personeel ID niet gevonden in sessie.']);
    }
    exit(); // Stop de verdere uitvoering van het script
}

if (isset($_POST['get_start_info'])) {
    $personeel_id = $_POST['personeel_fk'];
    $sql = "CALL usp_GetStartInfo(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $personeel_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo json_encode(null);
    }
    $stmt->close();
}

if (isset($_POST['start_werkuur'])) {
    $personeel_id = $_POST['personeel_fk'];
    $school_fk = $_POST['school_fk'];
    $job_fk = $_POST['job_fk'];

    $sql = "CALL usp_StartWerkuur(?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $personeel_id, $school_fk, $job_fk);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row); // Verwacht een response van de stored procedure (bv. status)
    } else {
        echo json_encode(['status' => 'success']); // Als de stored procedure geen resultaatset teruggeeft, ga er dan van uit dat het gelukt is
    }
    $stmt->close();
}

if (isset($_POST['stop_werkuur'])) {
    $personeel_id = $_POST['personeel_fk'];
    $sql = "CALL usp_StopWerkuur(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $personeel_id);
    $stmt->execute();
    // Bij een UPDATE stored procedure is er vaak geen resultaatset
    echo json_encode(['success' => true]); // Ga er van uit dat het stoppen is gelukt
    $stmt->close();
}

if (isset($_POST['get_monthly_history'])) {
    $personeel_id = $_POST['personeel_fk'];
    $year = $_POST['year'];
    $month = $_POST['month'];

    $sql = "CALL usp_GetMonthlyWorkHistory(?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $personeel_id, $year, $month);
    $stmt->execute();
    $result = $stmt->get_result();
    $historyData = array();
    while ($row = $result->fetch_assoc()) {
        $historyData[]= $row;
    }
    echo json_encode($historyData);
    $stmt->close();
}

mysqli_close($conn);
?>