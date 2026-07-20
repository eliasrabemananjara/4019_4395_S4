<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Éditer un barème</title>
</head>
<body>
    <div>
        <p><a href="/operateur/baremes">Retour aux barèmes</a></p>
        <h2>Éditer le barème</h2>

        <form action="/operateur/baremes/update/<?= esc($bareme['id'], 'attr') ?>" method="post">
            <div>
                <label for="montant_min">Montant min</label><br>
                <input type="text" id="montant_min" name="montant_min" value="<?= esc($bareme['montant_min']) ?>" required>
            </div>
            <div>
                <label for="montant_max">Montant max</label><br>
                <input type="text" id="montant_max" name="montant_max" value="<?= esc($bareme['montant_max']) ?>" required>
            </div>
            <div>
                <label for="frais">Frais</label><br>
                <input type="text" id="frais" name="frais" value="<?= esc($bareme['frais']) ?>" required>
            </div>
            <br>
            <button type="submit">Enregistrer</button>
        </form>
    </div>
</body>
</html>
