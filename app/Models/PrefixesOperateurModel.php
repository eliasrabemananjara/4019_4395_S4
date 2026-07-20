<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixesOperateurModel extends Model
{
    protected $table            = 'prefixes_operateur';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['prefixe', 'actif'];

    // Validation
    protected $validationRules = [
        'prefixe' => 'required|is_unique[prefixes_operateur.prefixe,id,{id}]|min_length[3]|max_length[5]',
        'actif'   => 'permit_empty|in_list[0,1]',
    ];
}
