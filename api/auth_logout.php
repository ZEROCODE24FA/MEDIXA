<?php
require_once __DIR__ . '/config.php';
sessionStart();
session_destroy();
header('Location: ../login.php');
exit;
