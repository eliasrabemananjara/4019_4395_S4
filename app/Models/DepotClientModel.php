<?php

namespace App\Models;

class DepotClientModel extends ConnectionClientModel
{
    /**
     * Effectue un dépôt sur le compte.
     */
    public function deposer(int $compteId, float $montant): bool
    {
        $db = $this->getDatabase();

        $db->transStart();

        $this->majSolde($compteId, $montant, '+');

        $typeOpId = $this->getTypeOperationId('Dépôt', 1);
        $this->enregistrerTransaction($typeOpId, null, $compteId, $montant, 0, 'SUCCES');

        $db->transComplete();

        return $db->transStatus();
    }
}
