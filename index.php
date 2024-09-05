<?php

session_start();

require __DIR__ .  '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require __DIR__ . '/pages/layout.php';
