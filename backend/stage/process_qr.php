<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "conn.php";

if (isset($_POST['qr_content'])) {
    $email = $_POST['qr_content'];

    $sql = "CALL usp_GetPersoneelIdByEmail(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $_SESSION['qr_personeel_id'] = $row['pk_personeel'];
        echo json_encode(['status' => 'success', 'redirect' => 'enter_pin.html']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Ongeldige QR-code of e-mailadres.']);
    }

    $stmt->close();
    mysqli_next_result($conn); // Voeg dit toe om de result set van de stored procedure te legen
} else {
    echo json_encode(['status' => 'error', 'message' => 'Geen QR-code inhoud ontvangen.']);
}

mysqli_close($conn);
?>