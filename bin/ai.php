#!/usr/bin/env php
<?php

echo 'Ask anything to AI'. PHP_EOL;

while (true) {
    $input = readline('> ');

    if ($input === 'exit' || $input === '') {
        break;
    }

    echo 'Thinking...' . PHP_EOL;

    sleep(2); // Simulate thinking time

    echo 'AI: ' . $input . PHP_EOL;
}