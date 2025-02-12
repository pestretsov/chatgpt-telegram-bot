<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Longman\TelegramBot\Telegram;
use TelegramBot\Bot;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Get the incoming update from Telegram
$update = json_decode(file_get_contents('php://input'), true);

// Initialize Telegram and Bot
$telegram = new Telegram(
    getenv('TELEGRAM_BOT_TOKEN') ?: throw new RuntimeException('Telegram bot token not set'),
    getenv('TELEGRAM_BOT_USERNAME') ?: throw new RuntimeException('Telegram bot username not set')
);

$bot = new Bot($telegram);

// Handle the update
$bot->handleUpdate($update); 