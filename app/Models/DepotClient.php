<?php

namespace App\Models;

class DepotClient extends ConnectionClient
{
    /**
     * Effectue un dépôt sur le compte.
     */
    public function deposer(int $compteId, float $montant): bool
    {
        $db = \Config\Database::connect('sqlite3');
        
        $db->transStart();

        // Mettre à jour le solde
        $builder = $db->table($this->table);
        $builder->set('solde', "solde + {$montant}", false);
        $builder->where('id', $compteId);
        $builder->update();

        // Récupérer l'ID pour l'opération Dépôt
        $typeOp = $db->table('types_operations')
                     ->where('libelle', 'Dépôt')
                     ->get()
                     ->getRowArray();

        $typeOpId = $typeOp ? $typeOp['id'] : 1;

        // Insérer la transaction
        $db->table('transactions')->insert([
            'type_operation_id' => $typeOpId,
            'compte_source_id' => null,
            'compte_destination_id' => $compteId,
            'montant' => $montant,
            'frais' => 0,
            'statut' => 'SUCCES'
        ]);

        $db->transComplete();

        return $db->transStatus();
    }
}
