<?php
// Variables en PHP
$course = [
    'title' => "Curso profesional de PHP",
    'description' => "Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quasi atque quos, rerum ullam quae placeat accusamus eaque incidunt, impedit sit quia repellat magnam consequuntur illum quisquam optio illo. Recusandae, cumque.",

    'price' => 199.99,
    'publication_date' => "2023-10-01",
    'tags' => [
        "PHP",
        "Desarrollo Web",
        "Programación"
    ],

    'lessons' => [
        "Introducción a PHP",
        "Sintaxis básica",
        "Variables y tipos de datos",
        "Estructuras de control",
        "Funciones y programación orientada a objetos"
    ]

]

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $course['title']; ?></title>
</head>
<body>
    <h1>Bienvenido al <?= $course['title']; ?></h1>

    <p><?= $course['description']; ?></p>

    <p>Precio: $<?= number_format($course['price'], 2); ?></p>
    <p>Fecha de publicación: <?= date("d-m-Y", strtotime($course['publication_date'])); ?></p>

    <h2>Etiquetas del curso:</h2>
    <ul>
        <?php foreach ($course['tags'] as $tag) : ?>
            <li><?= htmlspecialchars($tag); ?></li>
        <?php endforeach; ?>
    </ul>

    <h2>Lecciones del curso:</h2>
    <ul>
        <?php foreach ($course['lessons'] as $lesson) : ?>
            <li><?= htmlspecialchars($lesson); ?></li>
        <?php endforeach; ?>
    </ul>

</body>
</html>