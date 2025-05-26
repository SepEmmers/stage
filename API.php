<?php
session_start();

// Stel de content type header in, zodat de browser weet dat het JSON is
header('Content-Type: application/json');

include "conn.php"; // Zorg ervoor dat $conn hier correct wordt geïnitialiseerd

// Controleer of $conn correct is ingesteld na include
if (!isset($conn) || !$conn instanceof mysqli) {
    // Stuur een foutmelding als de verbinding mislukt is
    echo json_encode(['error' => 'Database connectie mislukt. Controleer conn.php']);
    exit(); // Stop verdere uitvoering
}

// Bepaal de actie op basis van de 'action' parameter in de POST data
if (isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'get_session_personeel_id':
            // Haal personeel_id uit de sessie
            if (isset($_SESSION['personeel_id'])) {
                echo json_encode(['personeel_id' => $_SESSION['personeel_id']]);
            } else {
                echo json_encode(['personeel_id' => null, 'error' => 'Personeel ID niet gevonden in sessie.']);
            }
            break;

        // --- *** BIJGEWERKTE CASE GET_START_INFO *** ---
        case 'get_start_info':
            if (isset($_POST['personeel_fk'])) {
                $personeel_id = (int)$_POST['personeel_fk']; // Veiliger: cast naar integer
                $school_fk = 101; // Behoud hardcoded school_fk zoals in origineel

                // Roep de nieuwe stored procedure aan die alle info in één keer ophaalt
                $sql = "CALL usp_GetEnhancedStartInfo(?, ?)";
                $stmt = $conn->prepare($sql);

                // Controleer of het voorbereiden gelukt is
                if ($stmt) {
                    $stmt->bind_param("ii", $personeel_id, $school_fk);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    // Controleer of er een resultaat is
                    if ($result && $result->num_rows > 0) {
                        $start_info_raw = $result->fetch_assoc(); // Haal de rij met data op

                        // Verwerk de polygonen JSON string uit de database
                        $polygons_array = []; // Start met een lege array voor de polygonen
                        if (isset($start_info_raw['polygons_json']) && !empty($start_info_raw['polygons_json'])) {
                            // 1. Decodeer de hoofd JSON array string (die JSON strings van polygonen bevat)
                            $polygon_strings_array = json_decode($start_info_raw['polygons_json'], true); // true voor associatieve array

                            // Controleer of het decoderen een array opleverde
                            if (is_array($polygon_strings_array)) {
                                // 2. Loop door elke polygon coordinate string in de array
                                foreach ($polygon_strings_array as $coord_string) {
                                    // 3. Decodeer de individuele JSON string van één polygoon
                                    if (is_string($coord_string)) { // Controleer of het een string is
                                        $decoded_poly = json_decode($coord_string, true); // true voor associatieve array
                                        // 4. Voeg toe aan de uiteindelijke array als het decoderen gelukt is
                                        if (is_array($decoded_poly)) {
                                            $polygons_array[] = $decoded_poly;
                                        }
                                    }
                                }
                            }
                        }

                        // Bouw de JSON response voor de frontend
                        $output_data = [
                            // Neem personeelsnaam over (gebruik null coalescing voor veiligheid)
                            'personeelsnaam' => $start_info_raw['personeelsnaam'] ?? null,
                            // Converteer database 0/1 naar echte booleans
                            'isWerkuurGestart' => isset($start_info_raw['isWerkuurGestart']) ? (bool)$start_info_raw['isWerkuurGestart'] : false,
                            'isVandaagAlGewerktofGestopt' => isset($start_info_raw['isVandaagAlGewerktofGestopt']) ? (bool)$start_info_raw['isVandaagAlGewerktofGestopt'] : false,
                            // Voeg de verwerkte array met polygonen toe
                            'polygons' => $polygons_array
                            // Eventueel 'success' status toevoegen:
                            // 'success' => isset($start_info_raw['success']) ? (bool)$start_info_raw['success'] : true
                        ];

                        echo json_encode($output_data);

                    } else {
                        // Geen resultaat gevonden door de stored procedure
                        echo json_encode(['error' => 'Kon startinformatie niet ophalen (geen data).', 'personeel_id' => $personeel_id]);
                    }
                    // Sluit het statement
                    $stmt->close();
                    // Belangrijk na een stored procedure die resultaten teruggeeft
                    // om eventuele volgende result sets op te ruimen
                    while (mysqli_next_result($conn)) { if (!mysqli_more_results($conn)) break; }

                } else {
                    // Fout bij het voorbereiden van de SQL statement
                    error_log("Prepare statement failed for usp_GetEnhancedStartInfo: " . $conn->error); // Log de fout server-side
                    echo json_encode(['error' => 'Databasefout bij voorbereiden.', 'details' => $conn->error]); // Stuur generieke fout naar client
                }

            } else {
                // Benodigde parameter 'personeel_fk' ontbreekt
                echo json_encode(['error' => 'Personeel FK niet gespecificeerd.']);
            }
            break;
        // --- *** EINDE BIJGEWERKTE CASE GET_START_INFO *** ---

        case 'start_werkuur':
            // Structuur behouden zoals origineel, met kleine checks toegevoegd
            if (isset($_POST['personeel_fk'], $_POST['school_fk'], $_POST['job_fk'])) { // Combineer isset checks
                $personeel_id = (int)$_POST['personeel_fk'];
                $school_fk = (int)$_POST['school_fk'];
                $job_fk = (int)$_POST['job_fk'];

                $sql = "CALL usp_StartWerkuur(?, ?, ?)";
                $stmt = $conn->prepare($sql);
                if ($stmt) { // Controleer of prepare gelukt is
                    $stmt->bind_param("iii", $personeel_id, $school_fk, $job_fk);
                    $stmt->execute();
                    $result = $stmt->get_result(); // Resultaat bevat status/message van SP
                    if ($result && $result->num_rows > 0) { // Controleer of $result geldig is
                        $row = $result->fetch_assoc();
                        echo json_encode($row); // Stuur status/message van SP terug
                    } else {
                        // Fallback als SP geen output geeft (zou niet mogen volgens SP definitie)
                        echo json_encode(['status' => 'success', 'message' => 'Gestart (geen specifieke response).']);
                    }
                    $stmt->close();
                    while (mysqli_next_result($conn)) { if (!mysqli_more_results($conn)) break; }
                } else {
                    error_log("Prepare statement failed for usp_StartWerkuur: " . $conn->error);
                    echo json_encode(['status' => 'error', 'message' => 'Databasefout bij start.', 'details' => $conn->error]);
                }
            } else {
                echo json_encode(['error' => 'Niet alle vereiste parameters zijn aanwezig (personeel_fk, school_fk, job_fk).']);
            }
            break;

        case 'stop_werkuur':
            // Structuur behouden zoals origineel, met kleine checks toegevoegd
            if (isset($_POST['personeel_fk'])) {
                $personeel_id = (int)$_POST['personeel_fk'];
                $sql = "CALL usp_StopWerkuur(?)";
                $stmt = $conn->prepare($sql);
                if ($stmt) { // Controleer of prepare gelukt is
                    $stmt->bind_param("i", $personeel_id);
                    // Controleer of execute succesvol was
                    if ($stmt->execute()) {
                        echo json_encode(['success' => true]); // Neem aan dat SP succesvol was
                    } else {
                         error_log("Execute failed for usp_StopWerkuur: " . $stmt->error);
                         echo json_encode(['success' => false, 'error' => 'Kon werkuur niet stoppen.', 'details' => $stmt->error]);
                    }
                    $stmt->close();
                    while (mysqli_next_result($conn)) { if (!mysqli_more_results($conn)) break; }
                } else {
                    error_log("Prepare statement failed for usp_StopWerkuur: " . $conn->error);
                    echo json_encode(['success' => false, 'error' => 'Databasefout bij stop.', 'details' => $conn->error]);
                }
            } else {
                echo json_encode(['error' => 'Personeel FK niet gespecificeerd.']);
            }
            break;

        case 'get_monthly_history':
            // Structuur behouden zoals origineel, met kleine checks toegevoegd
            if (isset($_POST['personeel_fk'], $_POST['year'], $_POST['month'])) { // Combineer isset checks
                $personeel_fk = (int)$_POST['personeel_fk'];
                $year = (int)$_POST['year'];
                $month = (int)$_POST['month'];

                $stmt = $conn->prepare("CALL usp_GetMonthlyWorkHistory(?, ?, ?)");
                if ($stmt) { // Controleer of prepare gelukt is
                    $stmt->bind_param("iii", $personeel_fk, $year, $month);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $history = array();
                    if ($result) { // Controleer of $result geldig is
                        while ($row = $result->fetch_assoc()) {
                            $history[] = $row;
                        }
                    }
                    echo json_encode($history); // Stuur geschiedenis (kan lege array zijn)
                    $stmt->close();
                    while (mysqli_next_result($conn)) { if (!mysqli_more_results($conn)) break; }
                } else {
                     error_log("Prepare statement failed for usp_GetMonthlyWorkHistory: " . $conn->error);
                     echo json_encode(['error' => 'Databasefout bij ophalen maandgeschiedenis.', 'details' => $conn->error]);
                }
            } else {
                echo json_encode(['error' => 'Niet alle vereiste parameters zijn aanwezig (personeel_fk, year, month).']);
            }
            break; // Break is nodig hier

        case 'get_daily_history':
            // Structuur behouden zoals origineel, met kleine checks toegevoegd
            if (isset($_POST['personeel_fk'], $_POST['date'])) { // Combineer isset checks
                $personeel_id = (int)$_POST['personeel_fk'];
                $date = $_POST['date']; // Overweeg validatie formaat 'YYYY-MM-DD'

                $sql = "CALL usp_GetDailyWorkHistory(?, ?)";
                $stmt = $conn->prepare($sql);
                 if ($stmt) { // Controleer of prepare gelukt is
                    $stmt->bind_param("is", $personeel_id, $date);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $dailyHistoryData = array();
                    if ($result) { // Controleer of $result geldig is
                        while ($row = $result->fetch_assoc()) {
                            $dailyHistoryData[]= $row;
                        }
                    }
                    echo json_encode($dailyHistoryData); // Stuur daggeschiedenis (kan lege array zijn)
                    $stmt->close();
                     while (mysqli_next_result($conn)) { if (!mysqli_more_results($conn)) break; }
                } else {
                    error_log("Prepare statement failed for usp_GetDailyWorkHistory: " . $conn->error);
                     echo json_encode(['error' => 'Databasefout bij ophalen daggeschiedenis.', 'details' => $conn->error]);
                }
            } else {
                echo json_encode(['error' => 'Niet alle vereiste parameters zijn aanwezig (personeel_fk, date).']);
            }
            break; // Break is nodig hier

        default:
            // Actie niet herkend
            echo json_encode(['error' => 'Ongeldige actie.']);
            break;
    }
    // Zorg ervoor dat het script stopt na het afhandelen van een geldige actie
    exit();
} else {
    // Geen 'action' parameter ontvangen
    echo json_encode(['error' => 'Geen actie gespecificeerd.']);
    // exit(); // Optioneel hier
}

// Sluit de databaseverbinding alleen als deze succesvol geopend was
if (isset($conn) && $conn instanceof mysqli) {
    mysqli_close($conn);
}
?>