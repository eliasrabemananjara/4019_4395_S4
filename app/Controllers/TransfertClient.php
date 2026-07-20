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
     * Traite le transfert d'argent (simple ou multiple).
     */
    public function process()
    {
        if (!session()->has('client')) {
            return redirect()->route('client.form');
        }

        $montantTotal = $this->request->getPost('montant');
        $prefixes     = $this->request->getPost('prefixe');   // tableau
        $numSuites    = $this->request->getPost('num_suite'); // tableau
        $inclureFrais = $this->request->getPost('inclure_frais_retrait') !== null;

        if (!is_numeric($montantTotal) || $montantTotal <= 0) {
            return redirect()->back()->with('error', 'Montant invalide.');
        }

        if (!is_array($prefixes) || !is_array($numSuites) || count($prefixes) === 0) {
            return redirect()->back()->with('error', 'Veuillez saisir au moins un destinataire.');
        }

        // Construire la liste des numéros complets
        $destinataires = [];
        foreach ($prefixes as $i => $prefixe) {
            $suite = $numSuites[$i] ?? '';
            if (empty($prefixe) || strlen($suite) !== 7 || !ctype_digit($suite)) {
                return redirect()->back()->with('error', "Destinataire #" . ($i + 1) . " : numéro invalide.");
            }
            $destinataires[] = $prefixe . $suite;
        }

        $nbDestinataires = count($destinataires);
        $montantParDest  = round($montantTotal / $nbDestinataires, 2);

        $clientId = session('client.id');
        $model    = new TransfertClientModel();

        $messages = [];
        foreach ($destinataires as $numero) {
            $resultat = $model->transferer($clientId, $numero, $montantParDest, $inclureFrais);
            if (!$resultat['success']) {
                return redirect()->back()->with('error', "Echec vers $numero : " . $resultat['message']);
            }
            $messages[] = "$numero (" . number_format($montantParDest, 2, ',', ' ') . " Ar)";
        }

        // Mettre à jour le solde en session
        $compte = $model->find($clientId);
        session()->set('client.solde', $compte['solde']);

        $msg = 'Transferts effectués avec succès vers : ' . implode(', ', $messages) . '.';
        return redirect()->route('client.dashboard')->with('success', $msg);
    }
}
