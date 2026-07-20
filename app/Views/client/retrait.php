<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retrait — Mobile Money</title>
</head>
<body>
    <div class="card">
        <h2>Faire un retrait</h2>
        <p>Solde disponible : <?= number_format((float) session('client.solde'), 2, ',', ' ') ?> Ar</p>

        <?php if (session()->getFlashdata('error')): ?>
            <p style="color: red;"><?= esc(session()->getFlashdata('error')) ?></p>
        <?php endif; ?>

        <form action="<?= route_to('client.retrait.process') ?>" method="post">
            <label for="montant">Montant à retirer :</label><br>
            <input type="number" name="montant" id="montant" min="1" step="0.01" required>
            <button type="submit">Retirer</button>
        </form>
        <br>
        <a href="<?= route_to('client.dashboard') ?>">Retour au tableau de bord</a>
    </div>
</body>
</html>
