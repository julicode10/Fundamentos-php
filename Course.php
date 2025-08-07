<?php

class Course {

    public function __construct(
        protected string $title, 
        protected string $subtitle, 
        protected string $description, 
        protected array $tags
        ) {
            // Constructor para inicializar las propiedades del curso
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getSubtitle(): string {
        return $this->subtitle;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getTags(): array {
        return $this->tags;
    }

    public function addTag(string $tag): void {
        
        if (in_array($tag, $this->tags)) {
            return; // No agregar si la etiqueta ya existe
        }
        if (empty($tag)) {
            return; // No agregar si la etiqueta está vacía
        }

        if(count($this->tags) >= 5) {
            return; // No agregar si ya hay 5 etiquetas
        }
        $this->tags[] = $tag;
    }

    public function removeTag(string $tag): void {
        // Método para eliminar una etiqueta del curso
        $this->tags = array_filter($this->tags, fn($t) => $t !== $tag);
    }

}
