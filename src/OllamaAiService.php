<?php

namespace App;

use ArdaGnsrn\Ollama\Ollama;

class OllamaAiService implements AIServiceInterface
{
    protected $client;

    public function __construct()
    {
        $this->client = Ollama::client();
    }   

    public function getResponse(string $question): string
    {
        try {
            $result = $this->client->chat()
                ->create([
                    'model' => 'deepseek-r1:1.5b',
                    'messages' => [
                        ['role' => 'system', 'content' => <<<EOT
                            You are a PHP technical assistant. You MUST respond ONLY in Spanish language.
                            
                            MANDATORY RULES:
                            - Always respond in Spanish (español), never in English
                            - Keep responses under 100 words
                            - Be direct and technical
                            - If not PHP-related, say: "Solo respondo consultas sobre PHP"
                            - Include code examples when helpful
                            - Use PHP 8.2+ best practices
                            
                            RESPONSE FORMAT:
                            - Direct answers only
                            - No "thinking out loud"
                            - Technical information only
                            - Examples in Spanish comments
                            
                            Remember: ALWAYS respond in Spanish language, no exceptions.
                            EOT
                        ],
                        ['role' => 'user', 'content' => $question],
                    ]
                ]);
            
            $content = $result->message->content ?? 'No response from AI.';
            
            // Filtrar el pensamiento del modelo DeepSeek R1
            return $this->filterThinking($content);
            
        } catch (\Exception $e) {

            
            // Retornar un mensaje de error amigable al usuario
            return 'Lo siento, hubo un error al procesar tu consulta. Por favor, inténtalo nuevamente. Error: ' . $e->getMessage();
        }
        
    }

    /**
     * Filtra el "pensamiento" del modelo DeepSeek R1 y retorna solo la respuesta final
     */
    private function filterThinking(string $content): string
    {
        // Remover bloques de pensamiento entre etiquetas XML-style
        $content = preg_replace('/<think>.*?<\/think>/s', '', $content);
        $content = preg_replace('/<thinking>.*?<\/thinking>/s', '', $content);
        $content = preg_replace('/<thought>.*?<\/thought>/s', '', $content);
        
        // Dividir en líneas para procesamiento línea por línea
        $lines = explode("\n", $content);
        $cleanLines = [];
        $inThinkingBlock = false;
        $foundValidContent = false;
        
        foreach ($lines as $line) {
            $cleanLine = trim($line);
            
            // Saltar líneas vacías al inicio
            if (empty($cleanLine) && !$foundValidContent) {
                continue;
            }
            
            // Detectar patrones de pensamiento en inglés
            if (preg_match('/^(Let me think|I need to|I should|Let me analyze|First, I|I\'ll|Now I|So I|The user|Looking at|Based on)/i', $cleanLine)) {
                $inThinkingBlock = true;
                continue;
            }
            
            // Detectar patrones de pensamiento en español
            if (preg_match('/^(Déjame pensar|Necesito|Debería|Voy a analizar|Primero|El usuario|Mirando|Basándome en)/i', $cleanLine)) {
                $inThinkingBlock = true;
                continue;
            }
            
            // Detectar texto incoherente o mezclado
            if (preg_match('/(name \(name\)|wellformedes|well-formed structures|typedef|structs|unstructureds)/i', $cleanLine)) {
                continue;
            }
            
            // Detectar inicio de respuesta válida en español
            if (preg_match('/^(PHP es|PHP se|Para|En PHP|Puedes|La respuesta|El código|Un ejemplo|Usa|Utiliza)/i', $cleanLine)) {
                $inThinkingBlock = false;
                $foundValidContent = true;
                $cleanLines[] = $cleanLine;
                continue;
            }
            
            // Detectar inicio de respuesta válida en inglés y saltar (forzar español)
            if (preg_match('/^(Here\'s|The answer|To|In PHP|For PHP|You can|PHP is|The code|Use|You should)/i', $cleanLine)) {
                $inThinkingBlock = true; // Saltar contenido en inglés
                continue;
            }
            
            // Si no estamos en bloque de pensamiento, incluir la línea
            if (!$inThinkingBlock && !empty($cleanLine)) {
                $foundValidContent = true;
                $cleanLines[] = $cleanLine;
            }
        }
        
        $result = implode("\n", $cleanLines);
        
        // Limpiar texto corrupto adicional
        $result = preg_replace('/\b(name \(name\)|wellformedes|typedef|structs|unstructureds)\b/i', '', $result);
        $result = preg_replace('/still the basics are present in both languages\.?/i', '', $result);
        $result = preg_replace('/¿Qué te sirve\?\s*$/i', '', $result);
        
        // Limpiar espacios múltiples y saltos de línea extra
        $result = preg_replace('/\s+/', ' ', $result);
        $result = trim($result);
        
        // Si la respuesta está principalmente en inglés, forzar mensaje en español
        if ($this->isResponseInEnglish($result)) {
            return 'La respuesta se generó en inglés. Por favor, reformula tu pregunta para obtener una respuesta en español.';
        }
        
        // Validar que tenemos contenido útil
        if (empty($result) || strlen($result) < 10) {
            return 'No se pudo obtener una respuesta válida. Por favor, intenta reformular tu pregunta.';
        }
        
        // Verificar que la respuesta esté en español y sea coherente
        if (preg_match('/\b(wellformedes|typedef|structs|name \(name\))\b/i', $result)) {
            return 'La respuesta generada no es coherente. Por favor, intenta con otra pregunta.';
        }
        
        return $result;
    }
    
    /**
     * Verifica si la respuesta está principalmente en inglés
     */
    private function isResponseInEnglish(string $text): bool
    {
        $englishWords = ['the', 'and', 'you', 'can', 'use', 'this', 'that', 'with', 'for', 'are', 'is', 'in', 'to', 'of'];
        $spanishWords = ['el', 'la', 'y', 'puedes', 'usar', 'esto', 'eso', 'con', 'para', 'son', 'es', 'en', 'de', 'que'];
        
        $englishCount = 0;
        $spanishCount = 0;
        $words = str_word_count(strtolower($text), 1);
        
        foreach ($words as $word) {
            if (in_array($word, $englishWords)) {
                $englishCount++;
            }
            if (in_array($word, $spanishWords)) {
                $spanishCount++;
            }
        }
        
        // Si hay más palabras en inglés que en español, considerar que está en inglés
        return $englishCount > $spanishCount && $englishCount > 2;
    }
}
