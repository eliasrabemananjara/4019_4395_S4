<?php

namespace App\Models;

class RetraitClient extends ConnectionClient
{
    /**
     * Calcule les frais pour une opération et un montant donnés.
     */
    public function getFrais(string $libelleOperation, float $montant): float
    {
        $db = \Config\Database::connect('sqlite3');
        $typeOp = $db->table('types_operations')
                     ->where('libelle', $libelleOperation)
                     ->get()
                     ->getRowArray();

        if (!$typeOp) {
            return 0;
        }

        $bareme = $db->table('baremes_frais')
                     ->where('type_operation_id', $typeOp['id'])
                     ->where('montant_min <=', $montant)
                     ->where('montant_max >=', $montant)
                     ->get()
                     ->getRowArray();

        return $bareme ? (float)$bareme['frais'] : 0;
    }

    /**
     * Effectue un retrait sur le compte.
     */
    public function retirer(int $compteId, float $montant): array
    {
        $db = \Config\Database::connect('sqlite3');
        
        $db->transStart();

        $compte = $this->find($compteId);
        $frais = $this->getFrais('Retrait', $montant);
        $totalADebiter = $montant + $frais;

        if ($compte['solde'] < $totalADebiter) {
            $db->transRollback();
            return ['success' => false, 'message' => "Solde insuffisant pour retirer $montant Ar (Frais: $frais Ar)."];
        }

        // Mettre à jour le solde
        $builder = $db->table($this->table);
        $builder->set('solde', "solde - {$totalADebiter}", false);
        $builder->where('id', $compteId);
        $builder->update();

        // Récupérer l'ID pour l'opération Retrait
        $typeOp = $db->table('types_operations')
                     ->where('libelle', 'Retrait')
                     ->get()
                     ->getRowArray();

        $typeOpId = $typeOp ? $typeOp['id'] : 2;

        // Insérer la transaction
        $db->table('transactions')->insert([
            'type_operation_id' => $typeOpId,
            'compte_source_id' => $compteId,
            'compte_destination_id' => null,
            'montant' => $montant,
            'frais' => $frais,
            'statut' => 'SUCCES'
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return ['success' => false, 'message' => 'Erreur lors de la transaction.'];
        }

        return ['success' => true, 'frais' => $frais];
    }
}
