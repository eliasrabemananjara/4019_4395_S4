<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique — Mobile Money</title>
    <link rel="stylesheet" href="/assets/bootstrap/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h4 mb-3">Historique des transactions</h2>
                        <p class="text-muted">Numéro du compte : <span class="fw-bold text-dark"><?= esc($monNumero) ?></span></p>

                        <?php if (empty($transactions)): ?>
                            <div class="alert alert-secondary">Aucune transaction à afficher.</div>
                        <?php else: ?>
                            <div class="table-responsive mt-3">
                                <table class="table table-striped table-bordered align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Date</th>
                                            <th>Opération</th>
                                            <th>Détails (De / Vers)</th>
                                            <th>Montant</th>
                                            <th>Frais</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($transactions as $t): ?>
                                            <tr>
                                                <td><?= esc($t['date_transaction']) ?></td>
                                                <td><?= esc($t['operation']) ?></td>
                                                <td>
                                                    <?php if ($t['operation'] === 'Dépôt'): ?>
                                                        Crédité sur le compte
                                                    <?php elseif ($t['operation'] === 'Retrait'): ?>
                                                        Retiré du compte
                                                    <?php elseif ($t['operation'] === 'Transfert'): ?>
                                                        <?php if ($t['numero_source'] === $monNumero): ?>
                                                            Vers <?= esc($t['numero_destination']) ?>
                                                        <?php else: ?>
                                                            Reçu de <?= esc($t['numero_source']) ?>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                        $estEntrant = false;
                                                        if ($t['operation'] === 'Dépôt') {
                                                            $estEntrant = true;
                                                        } elseif ($t['operation'] === 'Transfert' && $t['numero_destination'] === $monNumero) {
                                                            $estEntrant = true;
                                                        }

                                                        if ($estEntrant) {
                                                            echo '<span class="text-success">+' . number_format($t['montant'], 2, ',', ' ') . ' Ar</span>';
                                                        } else {
                                                            echo '<span class="text-danger">-' . number_format($t['montant'], 2, ',', ' ') . ' Ar</span>';
                                                        }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                        if ($t['operation'] !== 'Dépôt' && $t['numero_source'] === $monNumero) {
                                                            echo '-' . number_format($t['frais'], 2, ',', ' ') . ' Ar';
                                                        } else {
                                                            echo '0,00 Ar';
                                                        }
                                                    ?>
                                                </td>
                                                <td><?= esc($t['statut']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <a href="<?= route_to('client.dashboard') ?>" class="btn btn-outline-dark mt-3">Retour au tableau de bord</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
