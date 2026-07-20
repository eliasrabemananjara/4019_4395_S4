<?php

namespace App\Controllers;

use App\Models\HistoriqueClient as HistoriqueModel;

class HistoriqueClient extends BaseController
{
    /**
     * Affiche l'historique des transactions du client connecté.
     */
    public function index()
    {
        if (!session()->has('client')) {
            return redirect()->route('client.form');
        }

        $numero = session('client.numero');
        
        $model = new HistoriqueModel();
        $transactions = $model->getHistoriqueByNumero($numero);

        $data = [
            'transactions' => $transactions,
            'monNumero' => $numero
        ];

        return view('client/historique', $data);
    }
}
