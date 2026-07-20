<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfert — Mobile Money</title>
    <link rel="stylesheet" href="/assets/bootstrap/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h4 mb-3">Faire un transfert</h2>
                        <p class="text-muted">Solde disponible : <span class="fw-bold text-dark"><?= number_format((float) session('client.solde'), 2, ',', ' ') ?> Ar</span></p>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
                        <?php endif; ?>

                        <form action="<?= route_to('client.transfert.process') ?>" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label for="num_destinataire" class="form-label">Numéro du destinataire</label>
                                <input type="text" class="form-control" name="num_destinataire" id="num_destinataire" placeholder="0321234567" required>
                            </div>
                            <div class="mb-3">
                                <label for="montant" class="form-label">Montant à transférer</label>
                                <input type="number" class="form-control" name="montant" id="montant" min="1" step="0.01" required>
                            </div>
                            <button type="submit" class="btn btn-dark">Transférer</button>
                        </form>

                        <a href="<?= route_to('client.dashboard') ?>" class="btn btn-outline-dark mt-3">Retour au tableau de bord</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
