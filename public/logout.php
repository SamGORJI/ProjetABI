<?php
require_once '../config/config.php';
require_once '../includes/auth.php';

deconnecterUtilisateur();
header('Location: ../index.php');
exit;
