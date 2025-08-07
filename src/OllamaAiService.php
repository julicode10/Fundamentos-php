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
                            You are a specialized PHP programming language assistant. You MUST respond ONLY in Spanish language and ONLY to PHP-related questions or programming fundamentals applied to PHP.
                            
                            STRICT FILTERING RULES:
                            - ONLY answer questions about PHP programming language or programming fundamentals explained through PHP
                            - If the question is NOT about PHP or programming fundamentals with PHP, respond EXACTLY: "Solo respondo consultas sobre PHP."
                            
                            TOPICS YOU CAN ANSWER:
                            - PHP syntax, functions, classes, OOP in PHP
                            - PHP frameworks (Laravel, Symfony, CodeIgniter)
                            - Databases with PHP (MySQL, PostgreSQL, etc.)
                            - PHP best practices and design patterns
                            - PHP versions and features (PHP 8.x)
                            - Composer and dependency management
                            - Programming fundamentals applied to PHP: variables, loops, conditionals, data types, arrays
                            - Object-oriented programming concepts explained with PHP examples
                            - Algorithm concepts implemented in PHP
                            - Data structures using PHP (arrays, objects)
                            - Programming principles (SOLID, DRY) applied to PHP
                            - Basic computer science concepts explained through PHP
                            
                            TOPICS YOU CANNOT ANSWER:
                            - Other programming languages (unless comparing with PHP)
                            - General programming concepts without PHP context
                            - Non-programming topics
                            - Personal questions or non-technical topics
                            
                            RESPONSE RULES:
                            - Always respond in Spanish (español), never in English
                            - Maximum 100 words for technical answers
                            - Be direct and practical
                            - Always include PHP code examples when explaining concepts
                            - Use PHP 8.2+ syntax and best practices
                            - Focus on actionable, specific PHP solutions
                            - When explaining fundamentals, always use PHP syntax and examples
                            
                            RESPONSE FORMAT:
                            - Direct technical answers only
                            - No introduction phrases
                            - Code examples with Spanish comments
                            - Practical PHP implementations
                            - Always relate fundamentals back to PHP usage
                            
                            IMPORTANT: Before answering, verify the question is about PHP or programming fundamentals that can be explained with PHP. If not, respond with the exact phrase: "Solo respondo consultas sobre PHP."
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
            
            // Detectar si ya tiene la respuesta correcta para consultas no-PHP
            if (preg_match('/^Solo respondo consultas sobre PHP\.?$/i', $cleanLine)) {
                return 'Solo respondo consultas sobre PHP.';
            }
            
            // Detectar inicio de respuesta válida en español sobre PHP o fundamentos
            if (preg_match('/^(PHP es|PHP se|Para|En PHP|Puedes|La respuesta|El código|Un ejemplo|Usa|Utiliza|function|class|array|\$|Una variable|Un bucle|Una función|Los tipos|Las variables|Un algoritmo|La programación)/i', $cleanLine)) {
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
        
        // Verificar si la pregunta no era sobre PHP y el modelo respondió incorrectamente
        if ($this->isNonPHPResponse($result)) {
            return 'Solo respondo consultas sobre PHP.';
        }
        
        // Si la respuesta está principalmente en inglés, forzar mensaje en español
        if ($this->isResponseInEnglish($result)) {
            return 'La respuesta se generó en inglés. Por favor, reformula tu pregunta para obtener una respuesta en español.';
        }
        
        // Validar que tenemos contenido útil
        if (empty($result) || strlen($result) < 10) {
            return 'No se pudo obtener una respuesta válida. Por favor, intenta reformular tu pregunta sobre PHP.';
        }
        
        // Verificar que la respuesta esté en español y sea coherente
        if (preg_match('/\b(wellformedes|typedef|structs|name \(name\))\b/i', $result)) {
            return 'La respuesta generada no es coherente. Por favor, intenta con otra pregunta sobre PHP.';
        }
        
        return $result;
    }
    
    /**
     * Verifica si la respuesta parece ser sobre temas no relacionados con PHP
     */
    private function isNonPHPResponse(string $text): bool
    {
        $nonPHPKeywords = [
            'javascript', 'python', 'java', 'c++', 'c#', 'ruby', 'go', 'rust',
            'html', 'css', 'react', 'vue', 'angular', 'node.js', 'typescript',
            'machine learning', 'inteligencia artificial', 'blockchain',
            'matemáticas', 'física', 'historia', 'geografía', 'biología'
        ];
        
        // Palabras clave que indican fundamentos de programación válidos cuando se mencionan con PHP
        $fundamentalsKeywords = [
            'variable', 'función', 'clase', 'objeto', 'array', 'bucle', 'ciclo',
            'condicional', 'algoritmo', 'estructura de datos', 'tipo de dato',
            'programación orientada a objetos', 'oop', 'herencia', 'polimorfismo',
            'encapsulación', 'abstracción', 'solid', 'dry', 'patrón de diseño'
        ];
        
        $textLower = strtolower($text);
        
        // Si contiene palabras de otros lenguajes, no es válido
        foreach ($nonPHPKeywords as $keyword) {
            if (strpos($textLower, $keyword) !== false) {
                return true;
            }
        }
        
        // Si menciona fundamentos pero también menciona PHP, es válido
        $mentionsFundamentals = false;
        foreach ($fundamentalsKeywords as $keyword) {
            if (strpos($textLower, $keyword) !== false) {
                $mentionsFundamentals = true;
                break;
            }
        }
        
        // Si menciona fundamentos Y menciona PHP, es válido
        if ($mentionsFundamentals && preg_match('/\bphp\b/i', $text)) {
            return false;
        }
        
        // Si no menciona PHP en absoluto y es una respuesta larga, probablemente no es sobre PHP
        if (!preg_match('/\bphp\b/i', $text) && strlen($text) > 50) {
            // A menos que sea sobre fundamentos explicados con código que parece PHP
            if (preg_match('/(\$[a-zA-Z_]|function\s+\w+|class\s+\w+|echo\s|print\s)/i', $text)) {
                return false; // Tiene sintaxis de PHP
            }
            return true;
        }
        
        return false;
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
