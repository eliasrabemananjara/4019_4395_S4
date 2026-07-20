<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barèmes de frais</title>
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
                <h2 class="card-title mb-4">Barèmes de frais</h2>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
                <?php endif; ?>

                <h3 class="h5">Liste des tranches</h3>
                <?php if (!empty($baremes)): ?>
                    <table class="table table-striped table-bordered mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>Type</th>
                                <th>Montant min</th>
                                <th>Montant max</th>
                                <th>Frais</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($baremes as $bareme): ?>
                                <tr>
                                    <td><?= esc($bareme['type_operation_libelle'] ?? '') ?></td>
                                    <td><?= esc($bareme['montant_min']) ?></td>
                                    <td><?= esc($bareme['montant_max']) ?></td>
                                    <td><?= esc($bareme['frais']) ?></td>
                                    <td>
                                        <a href="/operateur/baremes/edit/<?= esc($bareme['id'], 'attr') ?>" class="btn btn-sm btn-outline-dark">Éditer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">Aucun barème enregistré.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
