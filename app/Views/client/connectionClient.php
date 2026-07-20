<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Client — Mobile Money</title>
    <link rel="stylesheet" href="/assets/bootstrap/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h1 class="h3 mb-2">Mobile Money</h1>
                            <p class="text-muted">Entrez votre numéro pour accéder à votre compte</p>
                        </div>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger">⚠️ <?= esc(session()->getFlashdata('error')) ?></div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success">✅ <?= esc(session()->getFlashdata('success')) ?></div>
                        <?php endif; ?>

                        <form action="<?= route_to('client.connect') ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label for="numetel" class="form-label">Numéro de téléphone</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="numetel"
                                    id="numetel"
                                    placeholder="0321234567"
                                    maxlength="10"
                                    pattern="[0-9]{10}"
                                    inputmode="numeric"
                                    autocomplete="tel"
                                    required
                                >
                            </div>
                            <p class="form-text">10 chiffres requis — ex : 0321234567</p>

                            <button type="submit" class="btn btn-dark w-100">Se connecter</button>
                        </form>

                        <div class="mt-3 text-center">
                            <a href="/" class="btn btn-outline-dark btn-sm">Retour à l'accueil</a>
                        </div>

                        <div class="mt-4">
                            <div class="fw-bold mb-2">Préfixes acceptés</div>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-secondary">032</span>
                                <span class="badge bg-secondary">033</span>
                                <span class="badge bg-secondary">034</span>
                                <span class="badge bg-secondary">037</span>
                                <span class="badge bg-secondary">038</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>