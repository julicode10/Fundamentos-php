<?php

namespace App;

class FakeAiService
{
    public function getResponse(string $question): string
    {

        sleep(2); // Simulate thinking time

        if(strpos($question, 'PHP') !== false) {
            return 'AI: ' . $question;
        }

        return 'AI: I can only answer questions about PHP. Please ask something related to PHP.';
    }
}
