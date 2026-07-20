<?php

namespace App\Models;

use CodeIgniter\Model;

class CompteModel extends Model
{
    protected $table            = 'comptes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['numero', 'solde', 'date_creation'];

    // Validation
    protected $validationRules = [
        'numero' => 'required|is_unique[comptes.numero,id,{id}]|min_length[9]|max_length[15]',
        'solde'  => 'permit_empty|decimal',
    ];
}
