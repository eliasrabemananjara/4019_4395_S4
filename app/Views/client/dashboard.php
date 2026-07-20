<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Compte — Mobile Money</title>
   
</head>
<body>
    <div class="card">
        <p class="welcome">Bienvenue </p>
        <p class="numero"><?= esc(session('client.numero')) ?></p>
        <p class="solde-label">Solde disponible</p>
        <p class="solde-value"><?= number_format((float) session('client.solde'), 2, ',', ' ') ?> Ar</p>
        
        <?php if (session()->getFlashdata('success')): ?>
            <p style="color: green;"><?= esc(session()->getFlashdata('success')) ?></p>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <p style="color: red;"><?= esc(session()->getFlashdata('error')) ?></p>
        <?php endif; ?>

        <form action="<?= route_to('client.depot') ?>" method="post">
            <label for="montant">Montant du dépôt :</label><br>
            <input type="number" name="montant" id="montant" min="1" step="0.01" required>
            <button type="submit">Faire un dépôt</button>
        </form>
        <br>
        <a href="<?= route_to('client.retrait') ?>"><button>Faire un retrait</button></a>
        <br><br>
        <a href="<?= route_to('client.transfert') ?>"><button>Faire un transfert</button></a>
        <br><br>
        <a href="<?= route_to('client.historique') ?>"><button>Voir l'historique</button></a>
        <br><br>
        <a href="<?= route_to('client.logout') ?>" class="logout">Se déconnecter</a>
    </div>
</body>
</html>
