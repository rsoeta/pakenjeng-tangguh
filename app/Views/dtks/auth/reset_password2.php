<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>

<body>
    <h1>Reset Password</h1>
    <?php if (session()->getFlashdata('message')) : ?>
        <div class="alert <?= session()->getFlashdata('message')['type'] === 'success' ? 'alert-success' : 'alert-danger' ?>">
            <?= session()->getFlashdata('message')['text'] ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('reset-password') ?>" method="post">
        <input type="hidden" name="token" value="<?= esc($token) ?>">
        <label for="password">Password Baru:</label>
        <input type="password" name="password" id="password" required>
        <br>
        <label for="password_confirm">Konfirmasi Password:</label>
        <input type="password" name="password_confirm" id="password_confirm" required>
        <br>
        <button type="submit">Reset Password</button>
    </form>
</body>

</html>