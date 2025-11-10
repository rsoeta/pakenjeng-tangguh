<?php $title = $title ?? 'Terjadi Kesalahan'; ?>
<?php $message = $message ?? 'Telah terjadi kesalahan yang tidak diketahui.'; ?>
<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <title><?= esc($title) ?></title>
    <style type="text/css">
        <?= preg_replace('#[\r\n\t ]+#', ' ', file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'debug.css')) ?>
    </style>
</head>

<body>
    <div class="container">
        <h1><?= esc($title) ?></h1>
        <p><?= nl2br(esc($message)) ?></p>
    </div>
</body>

</html>