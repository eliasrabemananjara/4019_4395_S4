<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des préfixes</title>
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
                <h2 class="card-title mb-4">Gestion des préfixes opérateur</h2>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
                <?php endif; ?>

                <h3 class="h5">Liste des préfixes</h3>
                <?php if (!empty($prefixes)): ?>
                    <table class="table table-striped table-bordered mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>Préfixe</th>
                                <th>Actif</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($prefixes as $prefix): ?>
                                <tr>
                                    <td><?= esc($prefix['prefixe']) ?></td>
                                    <td><?= esc($prefix['actif'] ? 'Oui' : 'Non') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted">Aucun préfixe enregistré pour le moment.</p>
                <?php endif; ?>

                <h3 class="h5 mt-4">Ajouter un préfixe</h3>
                <form action="/operateur/prefixes/ajouter" method="post" class="mt-3">
                    <div class="mb-3">
                        <label for="prefixe" class="form-label">Préfixe</label>
                        <input type="text" class="form-control" id="prefixe" name="prefixe" maxlength="5" required>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="actif" value="1" checked>
                        <label class="form-check-label">Actif</label>
                    </div>

                    <button type="submit" class="btn btn-dark">Ajouter</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
