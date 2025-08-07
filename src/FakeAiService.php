<?php

namespace App;

class FakeAiService implements AIServiceInterface
{
    public function getResponse(string $question): string
    {
        try {
            // Simulate processing time
            sleep(2); // Simulate thinking time

            if(strpos($question, 'PHP') !== false) {
                return 'AI: ' . $question;
            }
            return 'AI: I can only answer questions about PHP. Please ask something related to PHP.';    
        } catch (\Exception $e) {
            return 'Lo siento, hubo un error al procesar tu consulta. Por favor, inténtalo nuevamente. Error: ' . $e->getMessage();
        }
    }
}
