<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Client — Mobile Money</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
</head>
<body>
    <div class="card">
        <div class="logo">
            <div class="logo-icon">📱</div>
            <h1>Mobile Money</h1>
            <p class="subtitle">Entrez votre numéro pour accéder à votre compte</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert-error">
                ⚠️ <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert-success">
                ✅ <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <form action="<?= route_to('client.connect') ?>" method="post">
            <?= csrf_field() ?>

            <label for="numetel">Numéro de téléphone</label>
            <div class="input-wrapper">
                <span class="input-prefix">📞</span>
                <input
                    type="text"
                    name="numetel"
                    id="numetel"
                    placeholder="032 XX XXX XX"
                    maxlength="10"
                    pattern="[0-9]{10}"
                    inputmode="numeric"
                    autocomplete="tel"
                    required
                >
            </div>
            <p class="hint">10 chiffres requis — ex : 0321234567</p>

            <button type="submit">Se connecter →</button>
        </form>

        <div class="prefixes-label">Préfixes acceptés</div>
        <div class="prefixes-list">
            <span class="badge">032</span>
            <span class="badge">033</span>
            <span class="badge">034</span>
            <span class="badge">037</span>
            <span class="badge">038</span>
        </div>
    </div>
</body>
</html>