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
        
        // --- Calcul des frais de transfert ---
        $fraisTransfertBase = $this->getFrais('Transfert', $montant);
        $fraisTransfertExterne = 0;

        $prefixSource = substr($compteSource['numero'], 0, 3);
        $prefixDest = substr($numeroDestinataire, 0, 3);
        
        $opSource = $db->table('prefixes_operateur')->where('prefixe', $prefixSource)->get()->getRowArray();
        $opDest = $db->table('prefixes_operateur')->where('prefixe', $prefixDest)->get()->getRowArray();

        if ($opSource && $opDest && $opSource['idOperateur'] != $opDest['idOperateur']) {
            $fraisSupRow = $db->table('frais_sup')->get()->getRowArray();
            if ($fraisSupRow) {
                $fraisTransfertExterne = ($fraisSupRow['pourcentage'] / 100) * $montant;
            }
        }

        $fraisTransfert = $fraisTransfertBase + $fraisTransfertExterne;
        
        // --- Calcul des frais de retrait (si inclus et même opérateur) ---
        $fraisRetrait = 0;
        if ($inclureFraisRetrait && $opSource && $opDest && $opSource['idOperateur'] == $opDest['idOperateur']) {
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
