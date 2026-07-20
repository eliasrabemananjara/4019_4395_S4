<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfert — Mobile Money</title>
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
                                <label class="form-label">Numéro du destinataire</label>
                                <div class="input-group">
                                    <select name="prefixe" id="prefixe" class="form-select" style="max-width: 120px;" required>
                                        <option value="">Préfixe</option>
                                        <?php if(isset($prefixes)) foreach ($prefixes as $p): ?>
                                            <option value="<?= esc($p['prefixe']) ?>" data-operateur="<?= esc($p['idOperateur']) ?>">
                                                <?= esc($p['prefixe']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="text" class="form-control" name="num_suite" id="num_suite" placeholder="1234567" maxlength="7" pattern="\d{7}" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="montant" class="form-label">Montant à transférer</label>
                                <input type="number" class="form-control" name="montant" id="montant" min="1" step="0.01" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" name="inclure_frais_retrait" id="inclure_frais_retrait" disabled>
                                <label class="form-check-label" for="inclure_frais_retrait">Inclure les frais de retrait (même opérateur uniquement)</label>
                            </div>
                            <button type="submit" class="btn btn-dark">Transférer</button>
                        </form>

                        <script>
                            const idOperateurClient = <?= json_encode($idOperateurClient ?? null) ?>;
                            const prefixeSelect = document.getElementById('prefixe');
                            const checkboxFrais = document.getElementById('inclure_frais_retrait');

                            prefixeSelect.addEventListener('change', function() {
                                const selectedOption = this.options[this.selectedIndex];
                                const idOperateurDest = selectedOption.getAttribute('data-operateur');
                                
                                if (idOperateurDest && idOperateurDest == idOperateurClient) {
                                    checkboxFrais.disabled = false;
                                } else {
                                    checkboxFrais.disabled = true;
                                    checkboxFrais.checked = false;
                                }
                            });
                        </script>

                        <a href="<?= route_to('client.dashboard') ?>" class="btn btn-outline-dark mt-3">Retour au tableau de bord</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
