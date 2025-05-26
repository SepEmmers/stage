<?php
session_start(); // Zorg ervoor dat de sessie gestart is

include "conn.php"; // Includeren van het database connectie bestand

if (isset($_POST['email']) && isset($_POST['password'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Roep de stored procedure aan voor de personeel tabel
  $sql = "CALL sp_PersoneelLogin(?, ?)";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "ss", $email, $password);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  if ($row = mysqli_fetch_assoc($result)) {
    // Controleer of de gebruiker een PIN moet instellen
    if (isset($row['NeedsPinSetup']) && $row['NeedsPinSetup'] == 1) {
      // Start de sessie en sla de personeel_id op, zodat we weten voor wie we de PIN moeten instellen
      $_SESSION['personeel_id_for_pin_setup'] = $row['pk_personeel'];
      echo json_encode(['redirect' => 'create_pin.html']);
      exit();
    }

    // Controleer het resultaat van de stored procedure
    if ($row['LoginSuccessful'] == 1) {
      // Start de sessie en sla personeel informatie op
      $_SESSION['personeel_id'] = $row['pk_personeel']; // Gebruik de primaire sleutel van de personeel tabel
      $_SESSION['personeel_email'] = $email;

      echo json_encode(['success' => true, 'message' => "personeel succesvol ingelogd.", 'redirect' => 'userstart.html']);
      exit();
    } else {
      echo json_encode(['success' => false, 'message' => "Ongeldige e-mail of wachtwoord voor personeel.2"]);
    }
  } else {
    echo json_encode(['success' => false, 'message' => "Ongeldige e-mail of wachtwoord voor personeel.1"]);
  }

  mysqli_free_result($result);
  mysqli_stmt_close($stmt);

} else {
  echo json_encode(['success' => false, 'message' => "E-mail en wachtwoord zijn verplicht."]);
}

mysqli_close($conn);
?>