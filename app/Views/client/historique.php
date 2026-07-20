<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique — Mobile Money</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .positif {
            color: green;
        }
        .negatif {
            color: red;
        }
    </style>
</head>
<body>
    <div class="card" style="padding: 20px;">
        <h2>Historique des transactions</h2>
        <p>Numéro du compte : <?= esc($monNumero) ?></p>

        <?php if (empty($transactions)): ?>
            <p>Aucune transaction à afficher.</p>
        <?php else: ?>
            <table>
                <thead>
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
                                    // Déterminer si l'argent rentre ou sort pour ce client
                                    $estEntrant = false;
                                    if ($t['operation'] === 'Dépôt') {
                                        $estEntrant = true;
                                    } elseif ($t['operation'] === 'Transfert' && $t['numero_destination'] === $monNumero) {
                                        $estEntrant = true;
                                    }

                                    if ($estEntrant) {
                                        echo '<span class="positif">+' . number_format($t['montant'], 2, ',', ' ') . ' Ar</span>';
                                    } else {
                                        echo '<span class="negatif">-' . number_format($t['montant'], 2, ',', ' ') . ' Ar</span>';
                                    }
                                ?>
                            </td>
                            <td>
                                <?php 
                                    // Les frais ne sont payés que par celui qui initie (source) sauf pour un dépôt où c'est gratuit.
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
        <?php endif; ?>

        <br>
        <a href="<?= route_to('client.dashboard') ?>">Retour au tableau de bord</a>
    </div>
</body>
</html>
