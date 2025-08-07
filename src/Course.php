<?php

namespace App;

class Course {

    public function __construct(
        protected string $title, 
        protected string $subtitle, 
        protected string $description, 
        protected array $tags,  
        //type: free, paid
        protected CourseType $type = CourseType::FREE, // Valor por defecto
        ) {
            // Constructor para inicializar las propiedades del curso
    }

    public function __get($name){
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null; // O lanzar una excepción si se prefiere
    }

    public function __set($name, $value) {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    public function __toString(): string {
        return "Curso: {$this->title}, Descripción: {$this->description}, Etiquetas: " . implode(", ", $this->tags)
            . ", Tipo: {$this->type->label()}";
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
