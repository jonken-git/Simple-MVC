<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
        <h1><?= $this->viewData["title"] ?? $title ?? "Hoppsan!" ?></h1>
    </header>
    <main>