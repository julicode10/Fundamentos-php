<?php
// Variables en PHP
$course = "Curso profesional de PHP";
$price = 199.99;
$publication_date = "2023-10-01";
$isArchived = true;

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $course; ?></title>
</head>
<body>
    <h1>Bienvenido al <?= $course; ?></h1>

    <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quasi atque quos, rerum ullam quae placeat accusamus eaque incidunt, impedit sit quia repellat magnam consequuntur illum quisquam optio illo. Recusandae, cumque.</p>

    <p>Precio: $<?= number_format($price, 2); ?></p>
    <p>Fecha de publicación: <?= date("d-m-Y", strtotime($publication_date)); ?></p>
</body>
</html>