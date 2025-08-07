<?php
 
require __DIR__ . '/vendor/autoload.php';

use App\Course;
use App\CourseType;

$course = new Course(
        title: "Curso profesional de PHP",
        subtitle: "Aprende PHP desde cero",
        description: "Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quasi atque quos, rerum ullam quae placeat accusamus eaque incidunt, impedit sit quia repellat magnam consequuntur illum quisquam optio illo. Recusandae, cumque.",
        tags: ["PHP", "Desarrollo Web", "Programación"],
        type: CourseType::PAID // Cambiando el tipo a PAID
    );

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

    <h2>Etiquetas del curso:</h2>
    <ul>
        <?php foreach ($course->tags as $tag) : ?>
            <li><?= htmlspecialchars($tag); ?></li>
        <?php endforeach; ?>
    </ul>
        
    <?= $course ?>


</body>
</html>