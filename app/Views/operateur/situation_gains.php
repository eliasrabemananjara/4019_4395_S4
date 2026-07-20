<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Situation des gains</title>
    <link rel="stylesheet" href="/assets/bootstrap/bootstrap.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="/">Opérateur</a>
            <div class="navbar-nav">
                <a class="nav-link" href="/operateur/prefixes">Préfixes</a>
                <a class="nav-link" href="/operateur/baremes">Barèmes</a>
                <a class="nav-link" href="/operateur/ma-gain">Gains</a>
                <a class="nav-link" href="/operateur/clients">Clients</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow-sm">
            <div class="card-body">
                <p><a href="/" class="btn btn-outline-dark btn-sm">Retour à l'accueil</a></p>
                <h2 class="card-title mb-4">Situation des gains de l'opérateur</h2>

                <div class="alert alert-info">
                    <strong>Total des gains :</strong> <?= esc(number_format($total, 2, ',', ' ')) ?> ARIARY
                </div>

                <h4 class="mt-4 mb-3">Gains par opérateur</h4>
                <table class="table table-bordered mb-4">
                    <thead class="table-dark">
                        <tr>
                            <th>Opérateur</th>
                            <th>Total des Frais Collectés</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gainsParOperateur as $gp): ?>
                            <tr>
                                <td><?= esc($gp['nom_operateur']) ?></td>
                                <td><?= esc(number_format($gp['total_frais'], 2, ',', ' ')) ?> Ar</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h4 class="mt-4 mb-3">Détails des transactions</h4>
                <?php if (!empty($gains)): ?>
                    <?php
                        // Grouper les transactions par opérateur
                        $gainsGroupes = [];
                        foreach ($gains as $gain) {
                            $op = $gain['nom_operateur'];
                            $gainsGroupes[$op][] = $gain;
                        }
                    ?>
                    <?php foreach ($gainsGroupes as $nomOperateur => $transactions): ?>
                        <div class="mb-4">
                            <table class="table table-striped table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Opération</th>
                                        <th>Montant</th>
                                        <th>Frais</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $totalFraisOp = 0; ?>
                                    <?php foreach ($transactions as $gain): ?>
                                        <?php $totalFraisOp += $gain['frais']; ?>
                                        <tr>
                                            <td><?= esc($gain['type_operation']) ?></td>
                                            <td><?= esc(number_format($gain['montant'], 2, ',', ' ')) ?> Ar</td>
                                            <td><?= esc(number_format($gain['frais'], 2, ',', ' ')) ?> Ar</td>
                                            <td><?= esc($gain['date_transaction']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-secondary fw-bold">
                                    <tr>
                                        <td colspan="2">Total frais collectés</td>
                                        <td colspan="2"><?= esc(number_format($totalFraisOp, 2, ',', ' ')) ?> Ar</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">Aucune transaction de retrait ou de transfert à afficher.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
