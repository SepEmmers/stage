<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "conn.php";

if (isset($_POST['pin'])) {
    $pin = $_POST['pin'];

    if (isset($_SESSION['qr_personeel_id'])) {
        $personeel_id = $_SESSION['qr_personeel_id'];

        $sql = "CALL usp_VerifyPin(?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $personeel_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if ($pin === $row['pin']) {
                $_SESSION['personeel_id'] = $personeel_id;
                $_SESSION['loggedin'] = true;
                unset($_SESSION['qr_personeel_id']);
                echo json_encode(['status' => 'success', 'redirect' => 'userstart.html']); 
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Incorrecte pincode.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gebruiker niet gevonden.']);
        }
        $stmt->close();
        mysqli_next_result($conn); // Leeg de result set
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Er is iets misgegaan. Scan eerst de QR-code.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Geen pincode ontvangen.']);
}

mysqli_close($conn);
?>