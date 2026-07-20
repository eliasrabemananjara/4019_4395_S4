<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoriqueClient extends Model
{
    protected $table      = 'vue_historique_transactions';
    protected $primaryKey = 'id';
    protected $DBGroup    = 'sqlite3';

    protected $returnType     = 'array';

    /**
     * Récupère toutes les transactions (envoyées ou reçues) pour un numéro donné.
     */
    public function getHistoriqueByNumero(string $numero): array
    {
        return $this->where('numero_source', $numero)
                    ->orWhere('numero_destination', $numero)
                    ->orderBy('date_transaction', 'DESC')
                    ->findAll();
    }
}
