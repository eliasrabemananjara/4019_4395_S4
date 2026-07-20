<?php

namespace App\Models;

class RetraitClientModel extends ConnectionClientModel
{
    /**
     * Effectue un retrait sur le compte.
     */
    public function retirer(int $compteId, float $montant): array
    {
        $db = $this->getDatabase();

        $db->transStart();

        $compte = $this->find($compteId);
        $frais = $this->getFrais('Retrait', $montant);
        $totalADebiter = $montant + $frais;

        if ($compte['solde'] < $totalADebiter) {
            $db->transRollback();
            return ['success' => false, 'message' => "Solde insuffisant pour retirer $montant Ar (Frais: $frais Ar)."];
        }

        $this->majSolde($compteId, $totalADebiter, '-');

        $typeOpId = $this->getTypeOperationId('Retrait', 2);
        $this->enregistrerTransaction($typeOpId, $compteId, null, $montant, $frais, 'SUCCES');

        $db->transComplete();

        if ($db->transStatus() === false) {
            return ['success' => false, 'message' => 'Erreur lors de la transaction.'];
        }

        return ['success' => true, 'frais' => $frais];
    }
}
