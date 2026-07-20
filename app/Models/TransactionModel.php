<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table            = 'transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'type_operation_id', 
        'compte_source_id', 
        'compte_destination_id', 
        'montant', 
        'frais', 
        'statut', 
        'date_transaction'
    ];

    // Validation
    protected $validationRules = [
        'type_operation_id'     => 'required|is_natural_no_zero',
        'compte_source_id'      => 'permit_empty|is_natural_no_zero',
        'compte_destination_id' => 'permit_empty|is_natural_no_zero',
        'montant'               => 'required|decimal',
        'frais'                 => 'permit_empty|decimal',
        'statut'                => 'required|in_list[SUCCES,ECHEC]',
    ];

    /**
     * Récupère l'historique détaillé des transactions
     */
    public function getFullTransactions()
    {
        return $this->select('transactions.*, types_operations.libelle as operation, src.numero as numero_source, dest.numero as numero_destination')
                    ->join('types_operations', 'types_operations.id = transactions.type_operation_id')
                    ->join('comptes as src', 'src.id = transactions.compte_source_id', 'left')
                    ->join('comptes as dest', 'dest.id = transactions.compte_destination_id', 'left')
                    ->findAll();
    }
}
