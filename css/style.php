<?php
header("Content-type: text/css");

// globale variabelen aanmaken
$background = '';
$color = '';
$a_color = '';
$a_color_hover = '';

// waardes uit de databank halen
include "../conn.php";

$sql = "SELECT * FROM settings";

$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $background = $row["background"];
    $color = $row["color"];
    $a_color = $row["a_color"];
    $a_color_hover = $row["a_color_hover"];
}



?>

body {
 background:<?php echo $background?>;
 color:<?php echo $color?>;
}

.sidenav {
    background-color:<?php echo $color?>;
}

label, p, input[type=date], input[type=text],input[type=number], button, a, select{
                color:<?php echo $color?>;
                
}

input[type=date], input[type=text],input[type=number], select{
    box-shadow: inset 0 1.5px 3px <?php echo $color?>, 0 0 0 5px #f5f7f8;
    
}


.footer {
                position: fixed;
                left: 0;
                bottom: 0;
                width: 100%;
                background-color: <?php echo $color?>;
                color: <?php echo $background?>;
                text-align: center;
              }

.footer p {
    color: <?php echo $background?>;
}

a:hover {
    color: <?php echo $a_color_hover?>;
}

a{
    color: <?php echo $a_color?>;
}






