<?php

namespace App\Controllers;

use App\Models\DepotClientModel;

class DepotClient extends BaseController
{
    /**
     * Traite le dépôt d'argent pour le client connecté.
     */
    public function process()
    {
        if (!session()->has('client')) {
            return redirect()->route('client.form');
        }

        $montant = $this->request->getPost('montant');

        // Validation basique
        if (!is_numeric($montant) || $montant <= 0) {
            return redirect()->route('client.dashboard')->with('error', 'Montant invalide.');
        }

        $clientId = session('client.id');
        $model = new DepotClientModel();

        if ($model->deposer($clientId, (float) $montant)) {
            // Mettre à jour le solde dans la session
            $compte = $model->find($clientId);
            session()->set('client.solde', $compte['solde']);
            return redirect()->route('client.dashboard')->with('success', 'Dépôt de ' . number_format($montant, 2, ',', ' ') . ' Ar effectué avec succès.');
        }

        return redirect()->route('client.dashboard')->with('error', 'Une erreur est survenue lors du dépôt.');
    }
}
