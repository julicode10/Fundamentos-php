<?php

namespace App;

use OpenAI;

class OpenAiService
{
    protected $client;

    public function __construct()
    {
        $this->client = OpenAI::client($_ENV['OPENAI_API_KEY']);
    }

    public function getResponse(string $question): string
    {
        try {
            $result = $this->client->chat()
                ->create([
                    'model' => 'gpt-4o-mini' , //'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => <<<EOT
                        Actúa como un asistente técnico enfocado exclusivamente en PHP.
                        - Tu objetivo es proporcionar respuestas claras, concisas y directamente útiles para desarrolladores, evitando información irrelevante.
                        - Si la consulta está relacionada con PHP, responde con precisión técnica en menos de 120 palabras, incluyendo ejemplos si son útiles.
                        - Si la consulta no está relacionada con PHP, responde: "Lo siento, este asistente está diseñado exclusivamente para consultas sobre PHP." ⚙️ Siempre prioriza buenas prácticas, compatibilidad con PHP 8.2+, y contextualiza según CLI, web, u otras tecnologías si se especifican.
                        EOT
                    ],
                        ['role' => 'user', 'content' => $question],
                    ]
                ]);
            
            return $result->choices[0]->message->content ?? 'No response from AI.';
            
        } catch (\Exception $e) {
        
            
            // Retornar un mensaje de error amigable al usuario
            return 'Lo siento, hubo un error al procesar tu consulta. Por favor, inténtalo nuevamente. Error: ' . $e->getMessage();
        }
    }
}
