<?php
 
class Course {

    // public $title;
    // public $subtitle;
    // public $description;
    // public $tags;

    // public function __construct($title, $subtitle, $description, $tags) {
    //     $this->title = $title;
    //     $this->subtitle = $subtitle;
    //     $this->description = $description;
    //     $this->tags = $tags;
    // }  
    
    public Author $author;
    public Author $coAuthor;
    
    public function __construct(
        public string $title, 
        public string $subtitle, 
        public string $description, 
        public array $tags
        ) {
    }


}

$course = new Course(
        title: "Curso profesional de PHP",
        subtitle: "Aprende PHP desde cero",
        description: "Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quasi atque quos, rerum ullam quae placeat accusamus eaque incidunt, impedit sit quia repellat magnam consequuntur illum quisquam optio illo. Recusandae, cumque.",
        tags: ["PHP", "Desarrollo Web", "Programación"]
    );

class Author {
    public function __construct(
        public string $name,
        public string $email
    ) {}
}

$author1 = new Author(
    name: "Juan Pérez",
    email: "juan.perez@example.com"
);

$author2 = new Author(
    name: "María Gómez",
    email: "maria.gomez@example.com"
);

$course->author = $author1;
$course->coAuthor = $author2;

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $course->title; ?></title>
</head>
<body>
    <h1>Bienvenido al <?= $course->title; ?></h1>

    <p><?= $course->description; ?></p>

    <p>Autor: <?= $course->author->name; ?></p>
    <p>Coautor: <?= $course->coAuthor->name; ?></p>

    <h2>Etiquetas del curso:</h2>
    <ul>
        <?php foreach ($course->tags as $tag) : ?>
            <li><?= htmlspecialchars($tag); ?></li>
        <?php endforeach; ?>
    </ul>

</body>
</html>