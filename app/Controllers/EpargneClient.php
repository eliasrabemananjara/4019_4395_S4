<?php

namespace App\Controllers;

use App\Models\DepotClientModel;

class EpargneClient extends BaseController
{
    /**
     * Traite le dépôt d'argent pour le client connecté.
     */
    public function index()
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
        $model = new EpargneClientModel();
        $compte = $model->find($clientId);

        $data = [
                'pourcentaEpargne' => $compte['pourcentage_epargne'] ?? 0,
                'soldeEpargne' => $compte['solde_epargne'] ?? 0,
        ];
        return view('client/epargne', $data );

    }

    public function process (){
        if (!session()-> has('client')){
            return redirect()->route('client.form');
        }
        $pourcentage = $this->request->getPost('pourcentage');

        if (!is_numeric($pourcentage) || $pourcentage < 0 || $pourcentage >100) {
            return redirect()->route('client.epargne')->with('error' , ' 0 a 100');
        }
        $clientId = session('client.id');
        $model = new EpargneClientModel();

        if ($model->definirPourcentage($clientId, (float) $pourcentage)){
            return redirect()->route('client.epargne')->with('success' , 'okay');
        }
        return redirect()->route('client.epargne')->with('error', 'problem')
    }
}
