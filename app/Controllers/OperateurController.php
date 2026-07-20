<?php

namespace App\Controllers;

use App\Models\BaremeFraisModel;
use App\Models\CompteModel;
use App\Models\PrefixesOperateurModel;
use App\Models\TransactionModel;

class OperateurController extends BaseController
{
    public function __construct()
    {
        helper('form');
    }

    public function prefixes(): string
    {
        $model = new PrefixesOperateurModel();
        $db = \Config\Database::connect('sqlite3');

        $prefixes = $model
            ->select('prefixes_operateur.id, prefixes_operateur.idOperateur, prefixes_operateur.prefixe, prefixes_operateur.actif, operateurs.nomOperateur')
            ->join('operateurs', 'operateurs.id = prefixes_operateur.idOperateur', 'left')
            ->orderBy('prefixes_operateur.id', 'ASC')
            ->findAll();

        $operateurs = $db->table('operateurs')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'prefixes' => $prefixes,
            'operateurs' => $operateurs,
        ];

        return view('operateur/prefixes', $data);
    }

    public function ajouter()
    {
        $model = new PrefixesOperateurModel();

        $prefixe = $this->request->getPost('prefixe');
        $idOperateur = $this->request->getPost('idOperateur');
        $actif = (bool) $this->request->getPost('actif');

        if (empty($prefixe)) {
            session()->setFlashdata('error', 'Le préfixe est obligatoire.');
            return redirect()->to('/operateur/prefixes');
        }

        if (empty($idOperateur) || !is_numeric($idOperateur)) {
            session()->setFlashdata('error', 'Veuillez sélectionner un opérateur.');
            return redirect()->to('/operateur/prefixes');
        }

        $model->save([
            'idOperateur' => (int) $idOperateur,
            'prefixe'     => trim($prefixe),
            'actif'       => $actif ? 1 : 0,
        ]);

        if ($model->errors()) {
            session()->setFlashdata('error', $model->errors()[0]);
            return redirect()->to('/operateur/prefixes');
        }

        session()->setFlashdata('success', 'Préfixe ajouté avec succès.');
        return redirect()->to('/operateur/prefixes');
    }

    public function baremes(): string
    {
        $model = new BaremeFraisModel();

        $data = [
            'baremes' => $model->getFullBaremes(),
        ];

        return view('operateur/baremes', $data);
    }

    public function editBareme($id = null)
    {
        $model = new BaremeFraisModel();
        $bareme = $model->find($id);

        if (!$bareme) {
            session()->setFlashdata('error', 'Barème introuvable.');
            return redirect()->to('/operateur/baremes');
        }

        $data = [
            'bareme' => $bareme,
        ];

        return view('operateur/edit_bareme', $data);
    }

    public function updateBareme($id = null)
    {
        $model = new BaremeFraisModel();

        if (!$model->find($id)) {
            session()->setFlashdata('error', 'Barème introuvable.');
            return redirect()->to('/operateur/baremes');
        }

        $data = [
            'montant_min' => $this->request->getPost('montant_min'),
            'montant_max' => $this->request->getPost('montant_max'),
            'frais'       => $this->request->getPost('frais'),
        ];

        if (!$model->update($id, $data)) {
            session()->setFlashdata('error', 'Erreur lors de la mise à jour du barème.');
            return redirect()->to('/operateur/baremes/edit/' . $id);
        }

        session()->setFlashdata('success', 'Barème mis à jour avec succès.');
        return redirect()->to('/operateur/baremes');
    }

    public function gains(): string
    {
        $transactionModel = new TransactionModel();

        $gains = $transactionModel
            ->select('transactions.id, transactions.montant, transactions.frais, transactions.date_transaction, types_operations.libelle as operation')
            ->join('types_operations', 'types_operations.id = transactions.type_operation_id')
            ->whereIn('types_operations.libelle', ['Retrait', 'Transfert'])
            ->orderBy('transactions.date_transaction', 'DESC')
            ->findAll();

        $total = 0;
        foreach ($gains as $gain) {
            $total += (float) $gain['frais'];
        }

        return view('operateur/situation_gains', [
            'gains' => $gains,
            'total' => $total,
        ]);
    }

    public function clients(): string
    {
        $compteModel = new CompteModel();

        $clients = $compteModel
            ->select('id, numero, solde, date_creation')
            ->orderBy('id', 'ASC')
            ->findAll();

        return view('operateur/situation_clients', [
            'clients' => $clients,
        ]);
    }
}
