<?php
 
require 'Course.php';

$course = new Course(
        title: "Curso profesional de PHP",
        subtitle: "Aprende PHP desde cero",
        description: "Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quasi atque quos, rerum ullam quae placeat accusamus eaque incidunt, impedit sit quia repellat magnam consequuntur illum quisquam optio illo. Recusandae, cumque.",
        tags: ["PHP", "Desarrollo Web", "Programación"]
    );

$course->addTag("Backend");
$course->addTag("Bases de Datos");
$course->addTag("Backend");

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $course->getTitle(); ?></title>
</head>
<body>
    <h1>Bienvenido al <?= $course->getTitle(); ?></h1>

    <p><?= $course->getDescription(); ?></p>

    <h2>Etiquetas del curso:</h2>
    <ul>
        <?php foreach ($course->getTags() as $tag) : ?>
            <li><?= htmlspecialchars($tag); ?></li>
        <?php endforeach; ?>
    </ul>

</body>
</html>