<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Rafly, Surya, Rheal, Andhika" />
    <meta name="description" content="Aplikasi Ujian Online berbasis website" />
    <meta name="keyword" content="ujian, ujian online, Aplikasi ujian" />
    <title><?= $data["title"] ?></title>
    <link rel="stylesheet" href="<?= Constant::DIRNAME ?>css/global.css">

    <!-- ICON -->
    <link rel="stylesheet" type="text/css"
        href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/regular/style.css" />
    <link rel="stylesheet" type="text/css"
        href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/fill/style.css" />

    <!-- FONT -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- TOAST -->
     <script src="<?= Constant::DIRNAME ?>js/toast.js"></script>
</head>
<body class="<?= isset($data['css']) ? str_replace(['style.', '.'], ['', '-'], $data['css']) : '' ?>">

<?php Flasher::getFlash() ?>