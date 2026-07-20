<?php

namespace App\Controllers;

use App\Models\TransfertClientModel;

class TransfertClient extends BaseController
{
    /**
     * Affiche le formulaire de transfert.
     */
    public function index()
    {
        if (!session()->has('client')) {
            return redirect()->route('client.form');
        }

        $db = \Config\Database::connect('sqlite3');
        $prefixes = $db->table('prefixes_operateur')->where('actif', 1)->get()->getResultArray();
        
        $clientNumero = session('client.numero');
        $clientPrefixe = substr($clientNumero, 0, 3);
        $clientOperateur = $db->table('prefixes_operateur')
                              ->where('prefixe', $clientPrefixe)
                              ->get()
                              ->getRowArray();
        
        $idOperateurClient = $clientOperateur ? $clientOperateur['idOperateur'] : null;

        $data = [
            'prefixes' => $prefixes,
            'idOperateurClient' => $idOperateurClient
        ];

        return view('client/transfaire', $data);
    }

    /**
     * Traite le transfert d'argent.
     */
    public function process()
    {
        if (!session()->has('client')) {
            return redirect()->route('client.form');
        }

        $montant = $this->request->getPost('montant');
        $prefixe = $this->request->getPost('prefixe');
        $numSuite = $this->request->getPost('num_suite');
        $numeroDestinataire = $prefixe . $numSuite;
        
        $inclureFrais = $this->request->getPost('inclure_frais_retrait') !== null;

        // Validation basique
        if (!is_numeric($montant) || $montant <= 0) {
            return redirect()->back()->with('error', 'Montant invalide.');
        }

        if (strlen($numSuite) !== 7 || !ctype_digit($numSuite)) {
            return redirect()->back()->with('error', 'Le numéro du destinataire (suite) doit comporter 7 chiffres.');
        }

        $clientId = session('client.id');
        $model = new TransfertClientModel();

        $resultat = $model->transferer($clientId, $numeroDestinataire, (float) $montant, $inclureFrais);

        if ($resultat['success']) {
            // Mettre à jour le solde dans la session
            $compte = $model->find($clientId);
            session()->set('client.solde', $compte['solde']);
            
            $frais = $resultat['frais'];
            $msgFraisRetrait = $inclureFrais ? " (inclus frais de retrait de ".number_format($resultat['frais_retrait'], 2, ',', ' ')." Ar)" : "";
            $msg = 'Transfert de ' . number_format($montant, 2, ',', ' ') . ' Ar' . $msgFraisRetrait . ' vers ' . esc($numeroDestinataire) . ' effectué avec succès. Frais de transfert : ' . number_format($frais, 2, ',', ' ') . ' Ar.';
            return redirect()->route('client.dashboard')->with('success', $msg);
        }

        return redirect()->back()->with('error', $resultat['message']);
    }
}
