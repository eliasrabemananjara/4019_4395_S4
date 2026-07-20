<?php

namespace App\Models;

class TransfertClientModel extends ConnectionClientModel
{
    /**
     * Effectue un transfert vers un autre compte.
     */
    public function transferer(int $compteSourceId, string $numeroDestinataire, float $montant): array
    {
        if (!$this->estNumerovalide($numeroDestinataire)) {
            return ['success' => false, 'message' => 'Le numéro du destinataire est invalide.'];
        }

        $db = $this->getDatabase();
        $db->transStart();

        $compteDest = $this->findByNumero($numeroDestinataire);
        if (!$compteDest) {
            $compteDest = $this->creerCompte($numeroDestinataire);
        }

        if ($compteDest['id'] == $compteSourceId) {
            $db->transRollback();
            return ['success' => false, 'message' => 'Vous ne pouvez pas transférer vers votre propre compte.'];
        }

        $compteSource = $this->find($compteSourceId);
        $frais = $this->getFrais('Transfert', $montant);
        $totalADebiter = $montant + $frais;

        if ($compteSource['solde'] < $totalADebiter) {
            $db->transRollback();
            return ['success' => false, 'message' => "Solde insuffisant pour transférer $montant Ar (Frais: $frais Ar)."];
        }

        $this->majSolde($compteSourceId, $totalADebiter, '-');
        $this->majSolde($compteDest['id'], $montant, '+');

        $typeOpId = $this->getTypeOperationId('Transfert', 3);
        $this->enregistrerTransaction($typeOpId, $compteSourceId, $compteDest['id'], $montant, $frais, 'SUCCES');

        $db->transComplete();

        if ($db->transStatus() === false) {
            return ['success' => false, 'message' => 'Erreur lors de la transaction.'];
        }

        return ['success' => true, 'frais' => $frais];
    }
}
