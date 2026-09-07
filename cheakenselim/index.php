<?php
require_once __DIR__ . '/config.php';

$user = getCurrentUser();

if (!$user) {
    header('Location: ../login.php?game=chicken');
    exit;
}

header('Location: game.html');
exit;
