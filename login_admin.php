<?php
    include "conn.php";

// Check connection
if (!$conn) {
    die(json_encode(['success' => false, 'message' => "Database connectie mislukt: " . mysqli_connect_error()]));
}

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Roep de stored procedure aan voor de admin tabel
    $sql = "CALL usp_AdminLogin(?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $email, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Controleer het resultaat van de stored procedure
        if ($row['LoginSuccessful'] == 1) {
            // Start de sessie en sla admin informatie op
            session_start();
            $_SESSION['admin_id'] = $row['pk_admin']; // Gebruik de primaire sleutel van de admin tabel
            $_SESSION['admin_email'] = $email;

            echo json_encode(['success' => true, 'message' => "Admin succesvol ingelogd."]);
        } else {
            echo json_encode(['success' => false, 'message' => "Ongeldige e-mail of wachtwoord voor admin.1"]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Ongeldige e-mail of wachtwoord voor admin.2"]);
    }

    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => "E-mail en wachtwoord zijn verplicht."]);
}

mysqli_close($conn);
?>