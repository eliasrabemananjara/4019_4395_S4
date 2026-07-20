<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Éditer un barème</title>
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
            <div class="card-body p-4">
                <p><a href="/operateur/baremes" class="btn btn-outline-dark btn-sm">Retour aux barèmes</a></p>
                <h2 class="h4 mb-4">Éditer le barème</h2>

                <form action="/operateur/baremes/update/<?= esc($bareme['id'], 'attr') ?>" method="post">
                    <div class="mb-3">
                        <label for="montant_min" class="form-label">Montant min</label>
                        <input type="text" class="form-control" id="montant_min" name="montant_min" value="<?= esc($bareme['montant_min']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="montant_max" class="form-label">Montant max</label>
                        <input type="text" class="form-control" id="montant_max" name="montant_max" value="<?= esc($bareme['montant_max']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="frais" class="form-label">Frais</label>
                        <input type="text" class="form-control" id="frais" name="frais" value="<?= esc($bareme['frais']) ?>" required>
                    </div>

                    <button type="submit" class="btn btn-dark">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
