<?php

require_once __DIR__ . '/../helpers/Auth.php';

Auth::logout();

header('Location: ' . PUBLIC_ROOT . '/index.php');
exit;


