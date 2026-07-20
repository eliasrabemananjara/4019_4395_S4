<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeOperationModel extends Model
{
    protected $table            = 'types_operations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['libelle', 'actif'];

    // Validation
    protected $validationRules = [
        'libelle' => 'required|is_unique[types_operations.libelle,id,{id}]',
        'actif'   => 'permit_empty|in_list[0,1]',
    ];
}
