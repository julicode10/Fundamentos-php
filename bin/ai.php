#!/usr/bin/env php
<?php

require __DIR__ . '/../bootstrap.php';

echo 'Ask anything to AI'. PHP_EOL;

while (true) {
    $input = readline('> ');

    if ($input === 'exit' || $input === '') {
        break;
    }

    $response = $aiService->getResponse($input);
    echo $response . PHP_EOL;
}