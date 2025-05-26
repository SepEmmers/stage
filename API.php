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

// Helper function to get the active pk_werkuren for a given personeel_fk
function getActiveWerkuurId($conn, $personeel_fk) {
    if (!$personeel_fk) {
        return null;
    }
    // Ensure $conn is a mysqli object
    if (!$conn || !($conn instanceof mysqli)) {
        error_log("getActiveWerkuurId: Invalid database connection.");
        return null;
    }

    $sql = "SELECT pk_werkuren FROM werkuren WHERE personeel_fk = ? AND eindtijd IS NULL ORDER BY starttijd DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("getActiveWerkuurId: Prepare failed: " . $conn->error);
        return null;
    }

    $stmt->bind_param("i", $personeel_fk);
    if (!$stmt->execute()) {
        error_log("getActiveWerkuurId: Execute failed: " . $stmt->error);
        $stmt->close();
        return null;
    }

    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stmt->close();
        return (int)$row['pk_werkuren'];
    } else {
        $stmt->close();
        return null;
    }
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

                        // Initialize $output_data with values from usp_GetEnhancedStartInfo
                        $output_data = [
                            'personeelsnaam' => $start_info_raw['personeelsnaam'] ?? null,
                            'isWerkuurGestart' => isset($start_info_raw['isWerkuurGestart']) ? (bool)$start_info_raw['isWerkuurGestart'] : false,
                            'isVandaagAlGewerktofGestopt' => isset($start_info_raw['isVandaagAlGewerktofGestopt']) ? (bool)$start_info_raw['isVandaagAlGewerktofGestopt'] : false,
                            'polygons' => [], // Default to empty, will be populated below
                            'current_break_start_timestamp' => null,
                            'current_break_accumulated_seconds_before_pause' => 0
                        ];

                        // Verwerk de polygonen JSON string uit de database
                        if (isset($start_info_raw['polygons_json']) && !empty($start_info_raw['polygons_json'])) {
                            $polygon_strings_array = json_decode($start_info_raw['polygons_json'], true);
                            if (is_array($polygon_strings_array)) {
                                foreach ($polygon_strings_array as $coord_string) {
                                    if (is_string($coord_string)) {
                                        $decoded_poly = json_decode($coord_string, true);
                                        if (is_array($decoded_poly)) {
                                            $output_data['polygons'][] = $decoded_poly;
                                        }
                                    }
                                }
                            }
                        }

                        // Fetch break data if a work hour is started
                        if ($output_data['isWerkuurGestart']) {
                            $active_werkuur_id = getActiveWerkuurId($conn, $personeel_id); // Use personeel_id from the outer scope
                            if ($active_werkuur_id) {
                                $stmt_break_data = $conn->prepare("SELECT current_break_start_timestamp, current_break_accumulated_seconds_before_pause FROM werkuren WHERE pk_werkuren = ?");
                                if ($stmt_break_data) {
                                    $stmt_break_data->bind_param("i", $active_werkuur_id);
                                    $stmt_break_data->execute();
                                    $result_break_data = $stmt_break_data->get_result();
                                    $break_data = $result_break_data->fetch_assoc();
                                    $stmt_break_data->close();

                                    if ($break_data) {
                                        $output_data['current_break_start_timestamp'] = $break_data['current_break_start_timestamp'];
                                        $output_data['current_break_accumulated_seconds_before_pause'] = (int)$break_data['current_break_accumulated_seconds_before_pause'];
                                    }
                                } else {
                                    error_log("Prepare statement failed for break data: " . $conn->error);
                                }
                            }
                        }
                        echo json_encode($output_data);

                    } else {
                        // Geen resultaat gevonden door de stored procedure usp_GetEnhancedStartInfo
                        // Still provide default break data structure
                        echo json_encode([
                            'error' => 'Kon startinformatie niet ophalen (geen data).', 
                            'personeel_id' => $personeel_id,
                            'current_break_start_timestamp' => null,
                            'current_break_accumulated_seconds_before_pause' => 0,
                            'isWerkuurGestart' => false // Explicitly set
                        ]);
                    }
                    // Sluit het statement
                    $stmt->close();
                    // Belangrijk na een stored procedure die resultaten teruggeeft
                    // om eventuele volgende result sets op te ruimen
                    while (mysqli_next_result($conn)) { if (!mysqli_more_results($conn)) break; }

                } else {
                    // Fout bij het voorbereiden van de SQL statement voor usp_GetEnhancedStartInfo
                    error_log("Prepare statement failed for usp_GetEnhancedStartInfo: " . $conn->error); // Log de fout server-side
                    echo json_encode([
                        'error' => 'Databasefout bij voorbereiden.', 
                        'details' => $conn->error,
                        'current_break_start_timestamp' => null,
                        'current_break_accumulated_seconds_before_pause' => 0,
                        'isWerkuurGestart' => false // Explicitly set
                    ]);
                }

            } else {
                // Benodigde parameter 'personeel_fk' ontbreekt
                echo json_encode([
                    'error' => 'Personeel FK niet gespecificeerd.',
                    'current_break_start_timestamp' => null,
                    'current_break_accumulated_seconds_before_pause' => 0,
                    'isWerkuurGestart' => false // Explicitly set
                ]);
            }
            break;
        // --- *** EINDE BIJGEWERKTE CASE GET_START_INFO *** ---

        case 'start_break':
            // header('Content-Type: application/json'); // Already set globally
            $personeel_fk = $_SESSION['personeel_id'] ?? null;
            if (!$personeel_fk) { echo json_encode(['status' => 'error', 'message' => 'User not logged in.']); exit; }

            $active_werkuur_id = getActiveWerkuurId($conn, $personeel_fk);
            $client_timestamp_ms = isset($_POST['client_timestamp_ms']) ? $_POST['client_timestamp_ms'] : null;

            if ($active_werkuur_id && $client_timestamp_ms) {
                // Convert milliseconds to a MySQL DATETIME format (YYYY-MM-DD HH:MM:SS)
                // Assuming client_timestamp_ms is UTC. If it's local, further conversion might be needed.
                $server_timestamp_formatted = gmdate("Y-m-d H:i:s", $client_timestamp_ms / 1000);
                
                $stmt = $conn->prepare("CALL SP_SetBreakStartTimestamp(?, ?)");
                if ($stmt) {
                    $stmt->bind_param("is", $active_werkuur_id, $server_timestamp_formatted);
                    if ($stmt->execute()) {
                        echo json_encode(['status' => 'success', 'message' => 'Break started']);
                    } else {
                        error_log("Execute failed for SP_SetBreakStartTimestamp: " . $stmt->error);
                        echo json_encode(['status' => 'error', 'message' => 'Failed to start break (SP execution)', 'details' => $stmt->error]);
                    }
                    $stmt->close();
                    while (mysqli_next_result($conn)) { if (!mysqli_more_results($conn)) break; }
                } else {
                    error_log("Prepare statement failed for SP_SetBreakStartTimestamp: " . $conn->error);
                    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Missing parameters or no active work session.']);
            }
            break;

        case 'pause_break':
            // header('Content-Type: application/json'); // Already set globally
            $personeel_fk = $_SESSION['personeel_id'] ?? null;
            if (!$personeel_fk) { echo json_encode(['status' => 'error', 'message' => 'User not logged in.']); exit; }

            $active_werkuur_id = getActiveWerkuurId($conn, $personeel_fk);
            $accumulated_seconds = isset($_POST['accumulated_seconds']) ? (int)$_POST['accumulated_seconds'] : null;

            if ($active_werkuur_id && $accumulated_seconds !== null) {
                $stmt = $conn->prepare("CALL SP_PauseBreak(?, ?)");
                if ($stmt) {
                    $stmt->bind_param("ii", $active_werkuur_id, $accumulated_seconds);
                    if ($stmt->execute()) {
                        echo json_encode(['status' => 'success', 'message' => 'Break paused']);
                    } else {
                        error_log("Execute failed for SP_PauseBreak: " . $stmt->error);
                        echo json_encode(['status' => 'error', 'message' => 'Failed to pause break (SP execution)', 'details' => $stmt->error]);
                    }
                    $stmt->close();
                    while (mysqli_next_result($conn)) { if (!mysqli_more_results($conn)) break; }
                } else {
                    error_log("Prepare statement failed for SP_PauseBreak: " . $conn->error);
                    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Missing parameters or no active work session.']);
            }
            break;

        case 'resume_break':
            // header('Content-Type: application/json'); // Already set globally
            // This is functionally the same as 'start_break' according to the SQL file (uses SP_SetBreakStartTimestamp)
            $personeel_fk = $_SESSION['personeel_id'] ?? null;
            if (!$personeel_fk) { echo json_encode(['status' => 'error', 'message' => 'User not logged in.']); exit; }
            
            $active_werkuur_id = getActiveWerkuurId($conn, $personeel_fk);
            $client_timestamp_ms = isset($_POST['client_timestamp_ms']) ? $_POST['client_timestamp_ms'] : null;
            
            if ($active_werkuur_id && $client_timestamp_ms) {
                $server_timestamp_formatted = gmdate("Y-m-d H:i:s", $client_timestamp_ms / 1000);
                $stmt = $conn->prepare("CALL SP_SetBreakStartTimestamp(?, ?)"); 
                if ($stmt) {
                    $stmt->bind_param("is", $active_werkuur_id, $server_timestamp_formatted);
                    if ($stmt->execute()) {
                        echo json_encode(['status' => 'success', 'message' => 'Break resumed']);
                    } else {
                        error_log("Execute failed for SP_SetBreakStartTimestamp (resume): " . $stmt->error);
                        echo json_encode(['status' => 'error', 'message' => 'Failed to resume break (SP execution)', 'details' => $stmt->error]);
                    }
                    $stmt->close();
                    while (mysqli_next_result($conn)) { if (!mysqli_more_results($conn)) break; }
                } else {
                    error_log("Prepare statement failed for SP_SetBreakStartTimestamp (resume): " . $conn->error);
                    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Missing parameters or no active work session.']);
            }
            break;

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
            // header('Content-Type: application/json'); // Already set globally
            $personeel_fk = $_SESSION['personeel_id'] ?? null;
            if (!$personeel_fk) { 
                echo json_encode(['success' => false, 'message' => 'User not logged in.']); 
                exit; 
            }

            $active_werkuur_id = getActiveWerkuurId($conn, $personeel_fk);
            // total_final_break_seconds should come from the client, which has been tracking the break time.
            $total_final_break_seconds = isset($_POST['total_final_break_seconds']) ? (int)$_POST['total_final_break_seconds'] : 0;

            if ($active_werkuur_id) {
                // Call SP_FinalizeWorkSessionBreaks
                $stmt_finalize_break = $conn->prepare("CALL SP_FinalizeWorkSessionBreaks(?, ?)");
                if ($stmt_finalize_break) {
                    $stmt_finalize_break->bind_param("ii", $active_werkuur_id, $total_final_break_seconds);
                    $break_finalized_success = $stmt_finalize_break->execute();
                    $stmt_finalize_break->close();
                    while (mysqli_next_result($conn)) { if (!mysqli_more_results($conn)) break; }

                    if ($break_finalized_success) {
                        // Now update the eindtijd for the work session
                        $stmt_stop_work = $conn->prepare("UPDATE werkuren SET eindtijd = NOW() WHERE pk_werkuren = ?");
                        if ($stmt_stop_work) {
                            $stmt_stop_work->bind_param("i", $active_werkuur_id);
                            if ($stmt_stop_work->execute()) {
                                echo json_encode(['success' => true, 'message' => 'Werkuur gestopt, break time recorded.']);
                            } else {
                                error_log("Execute failed for UPDATE werkuren SET eindtijd: " . $stmt_stop_work->error);
                                echo json_encode(['success' => false, 'message' => 'Werkuur gestopt, but failed to finalize work stop details (update eindtijd).', 'details' => $stmt_stop_work->error]);
                            }
                            $stmt_stop_work->close();
                        } else {
                            error_log("Prepare statement failed for UPDATE werkuren SET eindtijd: " . $conn->error);
                            echo json_encode(['success' => false, 'message' => 'Database error during stop werkuur (prepare update eindtijd).', 'details' => $conn->error]);
                        }
                    } else {
                        error_log("Execute failed for SP_FinalizeWorkSessionBreaks: " . ($stmt_finalize_break->error ?? $conn->error)); // $stmt_finalize_break is closed, so error might be on $conn
                        echo json_encode(['success' => false, 'message' => 'Failed to finalize break times. Work session not stopped.', 'details' => ($stmt_finalize_break->error ?? $conn->error)]);
                    }
                } else {
                    error_log("Prepare statement failed for SP_FinalizeWorkSessionBreaks: " . $conn->error);
                    echo json_encode(['success' => false, 'message' => 'Database error during stop werkuur (prepare SP_FinalizeWorkSessionBreaks).', 'details' => $conn->error]);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Geen actieve werksessie gevonden.']);
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