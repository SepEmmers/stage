<!DOCTYPE html>
<html lang="en">
<head>
    <title>De sportclub</title>
    <?php include "templates/head.php"; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="js/index.js"></script>
    
</head>
<body>
<?php include "templates/nav.php"; ?>
    <h1>Welkom Admin</h1>
<div id="container">
<h2>Werkregistraties</h2>

<?php

if (isset ($_POST['get_registraties']));{
    echo json_encode(get_registraties());
}


function get_registraties() {
include "conn.php";

$sql_werkuren = "CALL usp_GetWerkuren()";
$result_werkuren = mysqli_query($conn, $sql_werkuren);
mysqli_close($conn);

$result_registraties = array();
while ($row_werkuren = mysqli_fetch_assoc($result_werkuren)) {
    $result_registraties[] = $row_werkuren;

}

return $result_registraties;
}

// Toon hier de werkregistraties
$sql_werkuren = "CALL usp_GetWerkuren()";
$result_werkuren = mysqli_query($conn, $sql_werkuren);

if ($result_werkuren) {
    // Controleer of er resultaten zijn voor werkuren
    if (mysqli_num_rows($result_werkuren) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Naam Personeel</th><th>Starttijd</th><th>Eindtijd</th></tr>";

        // Loop door de resultaten voor werkuren
        while ($row_werkuren = mysqli_fetch_assoc($result_werkuren)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row_werkuren['VolledigeNaam']) . "</td>";
            echo "<td>" . htmlspecialchars($row_werkuren['Starttijd']) . "</td>";
            echo "<td>" . htmlspecialchars($row_werkuren['Eindtijd']) . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "Geen werkregistraties gevonden.";
    }

    // Free result set
    mysqli_free_result($result_werkuren);
} else {
    echo "Fout bij het ophalen van de werkregistraties: " . mysqli_error($conn);
}

// Sluit de database connectie
mysqli_close($conn);

?>
<br><br>
</div>
<?php include "templates/footer.php"; ?>
</body>

</html>