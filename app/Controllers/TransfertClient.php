<?php

namespace App\Controllers;

use App\Models\CompteModel;

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

        return view('client/transfaire');
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
        $numeroDestinataire = $this->request->getPost('num_destinataire');

        // Validation basique
        if (!is_numeric($montant) || $montant <= 0) {
            return redirect()->back()->with('error', 'Montant invalide.');
        }

        if (empty($numeroDestinataire)) {
            return redirect()->back()->with('error', 'Veuillez saisir le numéro du destinataire.');
        }

        $clientId = session('client.id');
        $model = new CompteModel();

        $resultat = $model->transferer($clientId, $numeroDestinataire, (float) $montant);

        if ($resultat['success']) {
            // Mettre à jour le solde dans la session
            $compte = $model->find($clientId);
            session()->set('client.solde', $compte['solde']);
            
            $frais = $resultat['frais'];
            $msg = 'Transfert de ' . number_format($montant, 2, ',', ' ') . ' Ar vers ' . esc($numeroDestinataire) . ' effectué avec succès. Frais: ' . number_format($frais, 2, ',', ' ') . ' Ar.';
            return redirect()->route('client.dashboard')->with('success', $msg);
        }

        return redirect()->back()->with('error', $resultat['message']);
    }
}
