<?php
session_start();
require_once 'conn.php'; // Gebruik je bestaande mysqli connectie

if (isset($_POST['nfc_id'])) {
    $nfc_id = $_POST['nfc_id'];
    $nfc_id = trim($nfc_id);

    // **BELANGRIJK:** Voor mysqli moet je prepared statements anders gebruiken om SQL injectie te voorkomen.

    // Roep de stored procedure aan met mysqli
    $stmt = mysqli_prepare($conn, "CALL GetUserByNfcId(?)");
    mysqli_stmt_bind_param($stmt, "s", $nfc_id); // "s" betekent string

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            $_SESSION['personeel_id'] = $user['pk_personeel']; // Gebruik de juiste kolomnaam
            $_SESSION['username'] = $user['email'];    // Gebruik de juiste kolomnaam
            echo json_encode(['status' => 'success', 'redirect' => 'userstart.html']);
            exit();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'NFC kaart niet herkend.']);
            exit();
        }
        mysqli_free_result($result);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Er is een fout opgetreden bij het verwerken van de aanvraag: ' . mysqli_error($conn)]);
        error_log("MySQLi error: " . mysqli_error($conn));
        exit();
    }

    mysqli_stmt_close($stmt);

} else {
    echo json_encode(['status' => 'error', 'message' => 'Geen NFC ID ontvangen.']);
    exit();
}
?>