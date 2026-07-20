<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfert — Mobile Money</title>
</head>
<body>
    <div class="card">
        <h2>Faire un transfert</h2>
        <p>Solde disponible : <?= number_format((float) session('client.solde'), 2, ',', ' ') ?> Ar</p>

        <?php if (session()->getFlashdata('error')): ?>
            <p style="color: red;"><?= esc(session()->getFlashdata('error')) ?></p>
        <?php endif; ?>

        <form action="<?= route_to('client.transfert.process') ?>" method="post">
            <label for="num_destinataire">Numéro du destinataire :</label><br>
            <input type="text" name="num_destinataire" id="num_destinataire" placeholder="032 XX XXX XX" required>
            <br><br>
            <label for="montant">Montant à transférer :</label><br>
            <input type="number" name="montant" id="montant" min="1" step="0.01" required>
            <br><br>
            <button type="submit">Transférer</button>
        </form>
        <br>
        <a href="<?= route_to('client.dashboard') ?>">Retour au tableau de bord</a>
    </div>
</body>
</html>
