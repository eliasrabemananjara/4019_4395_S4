<?php

namespace App\Models;

class TransfertClientModel extends ConnectionClientModel
{
    /**
     * Effectue un transfert vers un autre compte.
     */
    public function transferer(int $compteSourceId, string $numeroDestinataire, float $montant, bool $inclureFraisRetrait = false): array
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
        $fraisTransfert = $this->getFrais('Transfert', $montant);
        
        $fraisRetrait = 0;
        if ($inclureFraisRetrait) {
            $fraisRetrait = $this->getFrais('Retrait', $montant);
        }

        $montantEnvoye = $montant + $fraisRetrait;
        $totalADebiter = $montantEnvoye + $fraisTransfert;

        if ($compteSource['solde'] < $totalADebiter) {
            $db->transRollback();
            return ['success' => false, 'message' => "Solde insuffisant. Total à débiter: $totalADebiter Ar (Montant: $montant, Frais Retrait: $fraisRetrait, Frais Transfert: $fraisTransfert)."];
        }

        $this->majSolde($compteSourceId, $totalADebiter, '-');
        $this->majSolde($compteDest['id'], $montantEnvoye, '+');

        $typeOpId = $this->getTypeOperationId('Transfert', 3);
        $this->enregistrerTransaction($typeOpId, $compteSourceId, $compteDest['id'], $montantEnvoye, $fraisTransfert, 'SUCCES');

        $db->transComplete();

        if ($db->transStatus() === false) {
            return ['success' => false, 'message' => 'Erreur lors de la transaction.'];
        }

        return [
            'success' => true, 
            'frais' => $fraisTransfert,
            'frais_retrait' => $fraisRetrait
        ];
    }
}
