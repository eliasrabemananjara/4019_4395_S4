<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Money — Accueil</title>
    <link rel="stylesheet" href="/assets/bootstrap/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4 text-center">
                        <h1 class="h3 mb-2">Mobile Money</h1>
                        <p class="text-muted mb-4">Choisissez votre type d'accès</p>

                        <div class="d-grid gap-2">
                            <a href="<?= route_to('client.form') ?>" class="btn btn-dark">
                                Se connecter en tant que client
                            </a>
                            <a href="/operateur/prefixes" class="btn btn-outline-dark">
                                Voir en tant qu'opérateur
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>