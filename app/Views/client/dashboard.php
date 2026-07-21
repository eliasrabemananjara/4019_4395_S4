<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Compte — Mobile Money</title>
    <link rel="stylesheet" href="/assets/bootstrap/bootstrap.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="/">Client</a>
            <div class="navbar-nav">
                <a class="nav-link" href="<?= route_to('client.dashboard') ?>">Dépôt</a>
                <a class="nav-link" href="<?= route_to('client.retrait') ?>">Retrait</a>
                <a class="nav-link" href="<?= route_to('client.transfert') ?>">Transfert</a>
                <a class="nav-link" href="<?= route_to('client.historique') ?>">Historique</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h1 class="h3 mb-2">Bienvenue</h1>
                            <p class="h5 text-dark mb-0"><?= esc(session('client.numero')) ?></p>
                        </div>

                        <div class="border rounded p-3 mb-4 bg-light">
                            <p class="text-muted mb-1">Solde disponible</p>
                            <p class="h2 mb-0 text-success"><?= number_format((float) session('client.solde'), 2, ',', ' ') ?> Ar</p>
                        </div>

                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
                        <?php endif; ?>

                        <form action="<?= route_to('client.depot') ?>" method="post" class="mb-4">
                            <?= csrf_field() ?>
                            <label for="montant" class="form-label">Montant du dépôt</label>
                            <input type="number" class="form-control" name="montant" id="montant" min="1" step="0.01" required>
                            <button type="submit" class="btn btn-dark mt-3">Faire un dépôt</button>
                        </form>

                        <div class="d-grid gap-2">
                            <a href="<?= route_to('client.retrait') ?>" class="btn btn-outline-dark">Faire un retrait</a>
                            <a href="<?= route_to('client.transfert') ?>" class="btn btn-outline-dark">Faire un transfert</a>
                            <a href="<?= route_to('client.historique') ?>" class="btn btn-outline-dark">Voir l'historique</a>
                            <a href="<?= route_to('client.logout') ?>" class="btn btn-link text-danger">Se déconnecter</a>
                            <a href="<?= route_to('client.logout') ?>" class="btn btn-link text-danger">Se déconnecter</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
