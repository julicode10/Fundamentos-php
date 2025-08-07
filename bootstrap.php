<?php

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

//$aiService = App\new FakeAiService();
// $aiService = new App\OllamaAiService();
$aiService = new App\OpenAiService();