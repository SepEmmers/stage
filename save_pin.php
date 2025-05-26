<?php
session_start();
include "conn.php";

if (isset($_POST['newPin'])) {
    $newPin = $_POST['newPin'];

    // Controleer of de sessievariabele voor de personeel_id is ingesteld
    if (isset($_SESSION['personeel_id_for_pin_setup'])) {
        $personeel_id = $_SESSION['personeel_id_for_pin_setup'];

        // Roep de stored procedure aan om de PIN bij te werken
        $sql = "CALL sp_UpdatePersoneelPin(?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "is", $personeel_id, $newPin);

        if (mysqli_stmt_execute($stmt)) {
            // Verwijder de sessievariabele die we tijdelijk hadden opgeslagen
            unset($_SESSION['personeel_id_for_pin_setup']);
            echo json_encode(['success' => true, 'message' => "PIN succesvol ingesteld! Je kunt nu inloggen."]);
        } else {
            echo json_encode(['success' => false, 'message' => "Er is een fout opgetreden bij het opslaan van de PIN."]);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'message' => "Er is een probleem opgetreden. Probeer opnieuw in te loggen."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Er is geen PIN ontvangen."]);
}

mysqli_close($conn);
?>