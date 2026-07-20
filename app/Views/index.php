<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Money — Accueil</title>
   
    </style>
</head>
<body>
    <div class="card">
        <div class="logo-icon"> Mobile Money</div>
        <h1>Mobile Money</h1>
        <p class="subtitle">Choisissez votre type d'accès</p>

        <a href="<?= route_to('client.form') ?>" class="btn btn-client">
             Se connecter en tant que client
        </a>
        <br>
        <a href="#" class="btn btn-operateur">
             Voir en tant qu'opérateur
        </a>
    </div>
</body>
</html>