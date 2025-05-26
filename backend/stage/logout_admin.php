<?php
session_start();
session_destroy();
header("Location: login_admin.html"); // Redirect naar de login pagina na het uitloggen
exit();
?>