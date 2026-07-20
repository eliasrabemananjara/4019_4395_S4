<?php

namespace App\Models;

class TransfertClient extends ConnectionClient
{
    /**
     * Effectue un transfert vers un autre compte.
     */
    public function transferer(int $compteSourceId, string $numeroDestinataire, float $montant): array
    {
        // Valider le numéro destinataire
        if (!$this->estNumerovalide($numeroDestinataire)) {
            return ['success' => false, 'message' => 'Le numéro du destinataire est invalide.'];
        }

        $db = \Config\Database::connect('sqlite3');
        $db->transStart();

        // 1. Chercher ou créer le compte destinataire
        $compteDest = $this->findByNumero($numeroDestinataire);
        if (!$compteDest) {
            $compteDest = $this->creerCompte($numeroDestinataire);
        }

        // Si source == destination
        if ($compteDest['id'] == $compteSourceId) {
            $db->transRollback();
            return ['success' => false, 'message' => 'Vous ne pouvez pas transférer vers votre propre compte.'];
        }

        // 2. Vérifier le solde source + frais
        $compteSource = $this->find($compteSourceId);
        
        $typeOpTransfert = $db->table('types_operations')
                              ->where('libelle', 'Transfert')
                              ->get()
                              ->getRowArray();
        $frais = 0;
        if ($typeOpTransfert) {
            $bareme = $db->table('baremes_frais')
                         ->where('type_operation_id', $typeOpTransfert['id'])
                         ->where('montant_min <=', $montant)
                         ->where('montant_max >=', $montant)
                         ->get()
                         ->getRowArray();
            $frais = $bareme ? (float)$bareme['frais'] : 0;
        }

        $totalADebiter = $montant + $frais;

        if ($compteSource['solde'] < $totalADebiter) {
            $db->transRollback();
            return ['success' => false, 'message' => "Solde insuffisant pour transférer $montant Ar (Frais: $frais Ar)."];
        }

        // 3. Débiter le compte source
        $builderSource = $db->table($this->table);
        $builderSource->set('solde', "solde - {$totalADebiter}", false);
        $builderSource->where('id', $compteSourceId);
        $builderSource->update();

        // 4. Créditer le compte destination
        $builderDest = $db->table($this->table);
        $builderDest->set('solde', "solde + {$montant}", false);
        $builderDest->where('id', $compteDest['id']);
        $builderDest->update();

        // 5. Enregistrer la transaction
        $typeOpId = $typeOpTransfert ? $typeOpTransfert['id'] : 3;

        $db->table('transactions')->insert([
            'type_operation_id' => $typeOpId,
            'compte_source_id' => $compteSourceId,
            'compte_destination_id' => $compteDest['id'],
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
