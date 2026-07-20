<?php

namespace App\Controllers;

use App\Models\CompteModel;

class RetraitClient extends BaseController
{
    /**
     * Affiche le formulaire de retrait.
     */
    public function index()
    {
        if (!session()->has('client')) {
            return redirect()->route('client.form');
        }

        return view('client/retrait');
    }

    /**
     * Traite le retrait d'argent pour le client connecté.
     */
    public function process()
    {
        if (!session()->has('client')) {
            return redirect()->route('client.form');
        }

        $montant = $this->request->getPost('montant');

        // Validation basique
        if (!is_numeric($montant) || $montant <= 0) {
            return redirect()->back()->with('error', 'Montant invalide.');
        }

        $clientId = session('client.id');
        $model = new CompteModel();

        $resultat = $model->retirer($clientId, (float) $montant);

        if ($resultat['success']) {
            // Mettre à jour le solde dans la session
            $compte = $model->find($clientId);
            session()->set('client.solde', $compte['solde']);
            
            $frais = $resultat['frais'];
            $msg = 'Retrait de ' . number_format($montant, 2, ',', ' ') . ' Ar effectué avec succès. Frais: ' . number_format($frais, 2, ',', ' ') . ' Ar.';
            return redirect()->route('client.dashboard')->with('success', $msg);
        }

        return redirect()->back()->with('error', $resultat['message']);
    }
}
