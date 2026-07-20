<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients et solde restant</title>
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
                <h2 class="card-title mb-4">Liste des clients et solde restant</h2>

                <?php if (!empty($clients)): ?>
                    <table class="table table-striped table-bordered mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>Numéro</th>
                                <th>Solde restant</th>
                                <th>Date de création</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $client): ?>
                                <tr>
                                    <td><?= esc($client['numero']) ?></td>
                                    <td><?= esc(number_format($client['solde'], 2, ',', ' ')) ?></td>
                                    <td><?= esc($client['date_creation']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">Aucun client trouvé.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
