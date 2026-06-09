<?php
session_start();
session_unset();
session_destroy(); // Apaga o "crachá" da memória
header("Location: ../views/login.php");
exit;
