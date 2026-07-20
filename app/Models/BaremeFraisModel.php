<?php

namespace App\Models;

use CodeIgniter\Model;

class BaremeFraisModel extends Model
{
    protected $table            = 'baremes_frais';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['type_operation_id', 'montant_min', 'montant_max', 'frais'];

    // Validation
    protected $validationRules = [
        'type_operation_id' => 'required|is_natural_no_zero',
        'montant_min'       => 'required|decimal',
        'montant_max'       => 'required|decimal',
        'frais'             => 'required|decimal',
    ];

    /**
     * Récupère les barèmes avec le libellé de l'opération
     */
    public function getFullBaremes()
    {
        return $this->select('baremes_frais.*, types_operations.libelle as type_operation_libelle')
                    ->join('types_operations', 'types_operations.id = baremes_frais.type_operation_id')
                    ->findAll();
    }
}
    