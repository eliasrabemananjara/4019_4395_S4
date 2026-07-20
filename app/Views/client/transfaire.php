<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfert — Mobile Money</title>
    <link rel="stylesheet" href="/assets/bootstrap/bootstrap.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="/">Client</a>
            <div class="navbar-nav">
                <a class="nav-link" href="<?= route_to('client.dashboard') ?>">Dépôt</a>
                <a class="nav-link" href="<?= route_to('client.retrait') ?>">Retrait</a>
                <a class="nav-link active" href="<?= route_to('client.transfert') ?>">Transfert</a>
                <a class="nav-link" href="<?= route_to('client.historique') ?>">Historique</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h4 mb-1">Faire un transfert</h2>
                        <p class="text-muted mb-3">Solde disponible : <span class="fw-bold text-dark"><?= number_format((float) session('client.solde'), 2, ',', ' ') ?> Ar</span></p>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
                        <?php endif; ?>

                        <form action="<?= route_to('client.transfert.process') ?>" method="post" id="form-transfert">
                            <?= csrf_field() ?>

                            <!-- Montant total -->
                            <div class="mb-4">
                                <label for="montant" class="form-label fw-semibold">Montant total à transférer (Ar)</label>
                                <input type="number" class="form-control" name="montant" id="montant" min="1" step="0.01" required placeholder="Ex: 10000">
                                <div id="info-repartition" class="form-text text-primary fw-semibold mt-1" style="display:none;"></div>
                            </div>

                            <!-- Liste des destinataires -->
                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <label class="form-label fw-semibold mb-0">Destinataires</label>
                                    <button type="button" class="btn btn-sm btn-outline-dark" id="btn-ajouter">+ Ajouter un destinataire</button>
                                </div>
                                <!-- Note multi-transfert (cachée par défaut) -->
                                <div id="note-multi" class="form-text text-warning fw-semibold mb-2" style="display:none;">
                                    Transfert multiple : uniquement vers des numéros du même opérateur que vous.
                                </div>
                                <div id="liste-destinataires"></div>
                            </div>

                            <div class="mb-3 form-check" id="bloc-frais-retrait" style="display:none;">
                                <input type="checkbox" class="form-check-input" name="inclure_frais_retrait" id="inclure_frais_retrait">
                                <label class="form-check-label" for="inclure_frais_retrait">Inclure les frais de retrait (même opérateur uniquement)</label>
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-dark flex-grow-1">Transférer</button>
                                <a href="<?= route_to('client.dashboard') ?>" class="btn btn-outline-secondary">Retour</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    const idOperateurClient = <?= json_encode($idOperateurClient ?? null) ?>;
    const prefixes          = <?= json_encode($prefixes ?? []) ?>;
    // Prefixes of the client's own operator only
    const prefixesMemeOp    = prefixes.filter(p => String(p.idOperateur) === String(idOperateurClient));

    const listEl      = document.getElementById('liste-destinataires');
    const montantInput = document.getElementById('montant');
    const infoRep     = document.getElementById('info-repartition');
    const noteMulti   = document.getElementById('note-multi');
    const blocFrais   = document.getElementById('bloc-frais-retrait');
    const btnAjouter  = document.getElementById('btn-ajouter');

    function buildOptions(liste, selectedVal = '') {
        let html = '<option value="">Préfixe</option>';
        liste.forEach(p => {
            const sel = p.prefixe === selectedVal ? 'selected' : '';
            html += `<option value="${p.prefixe}" data-operateur="${p.idOperateur}" ${sel}>${p.prefixe}</option>`;
        });
        return html;
    }

    function isMultiMode() {
        return listEl.querySelectorAll('.destinataire-row').length > 1;
    }

    /** Rebuild all prefix selects to match the current mode (single = all, multi = same-op only) */
    function rebuildAllSelects() {
        const liste = isMultiMode() ? prefixesMemeOp : prefixes;
        listEl.querySelectorAll('.prefixe-sel').forEach(sel => {
            const current = sel.value;
            sel.innerHTML = buildOptions(liste, current);
            // If the previously selected prefix is no longer in the list, deselect
            const stillValid = Array.from(sel.options).some(o => o.value === current && current !== '');
            if (!stillValid) sel.value = '';
        });
    }

    function ajouterLigne() {
        // Build the new row with appropriate prefix list
        const liste = isMultiMode() ? prefixesMemeOp : prefixes;
        const div = document.createElement('div');
        div.className = 'input-group mb-2 destinataire-row';
        div.innerHTML = `
            <select name="prefixe[]" class="form-select prefixe-sel" style="max-width:110px;" required>
                ${buildOptions(liste)}
            </select>
            <input type="text" class="form-control num-suite" name="num_suite[]" placeholder="1234567" maxlength="7" pattern="\\d{7}" required>
            <button type="button" class="btn btn-outline-danger btn-supprimer" title="Supprimer">−</button>
        `;
        listEl.appendChild(div);
        attachListeners(div);

        // If this is the second row added, switch all existing selects to same-op list
        rebuildAllSelects();
        updateUI();
    }

    function attachListeners(row) {
        row.querySelector('.btn-supprimer').addEventListener('click', function () {
            const rows = listEl.querySelectorAll('.destinataire-row');
            if (rows.length > 1) {
                row.remove();
                // If we're back to 1 row, restore full prefix list
                rebuildAllSelects();
            } else {
                // Keep at least one row — just clear it
                row.querySelector('.prefixe-sel').value = '';
                row.querySelector('.num-suite').value = '';
            }
            updateUI();
        });

        row.querySelector('.prefixe-sel').addEventListener('change', updateFraisCheckbox);
    }

    function updateUI() {
        updateInfo();
        updateNote();
        updateFraisCheckbox();
    }

    function updateInfo() {
        const montant = parseFloat(montantInput.value);
        const nb = listEl.querySelectorAll('.destinataire-row').length;
        if (!isNaN(montant) && montant > 0 && nb > 1) {
            const part = (montant / nb).toFixed(2);
            infoRep.textContent = `${montant.toFixed(2)} Ar ÷ ${nb} destinataires = ${part} Ar chacun.`;
            infoRep.style.display = 'block';
        } else {
            infoRep.style.display = 'none';
        }
    }

    function updateNote() {
        noteMulti.style.display = isMultiMode() ? 'block' : 'none';
    }

    function updateFraisCheckbox() {
        const selects = listEl.querySelectorAll('.prefixe-sel');
        let tousMemesOp = selects.length > 0;
        selects.forEach(sel => {
            const opt = sel.options[sel.selectedIndex];
            const idOp = opt ? opt.getAttribute('data-operateur') : null;
            if (!idOp || String(idOp) !== String(idOperateurClient)) tousMemesOp = false;
        });

        const cb = document.getElementById('inclure_frais_retrait');
        if (tousMemesOp && idOperateurClient) {
            blocFrais.style.display = 'block';
            cb.disabled = false;
        } else {
            blocFrais.style.display = 'none';
            cb.disabled = true;
            cb.checked = false;
        }
    }

    btnAjouter.addEventListener('click', ajouterLigne);
    montantInput.addEventListener('input', updateInfo);

    // Init with first row (shows all prefixes)
    ajouterLigne();
</script>
</body>
</html>
