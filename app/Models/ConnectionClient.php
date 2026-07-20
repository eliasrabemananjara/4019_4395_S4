<?php

namespace App\Models;

use CodeIgniter\Model;

class ConnectionClient extends Model
{
    protected $table      = 'comptes';
    protected $primaryKey = 'id';
    protected $DBGroup    = 'sqlite3';

    protected $allowedFields = ['numero', 'solde'];

    protected $useTimestamps = false;

    /**
     * Cherche un compte par numéro.
     */
    public function findByNumero(string $numero): ?array
    {
        return $this->where('numero', $numero)->first();
    }

    /**
     * Crée un nouveau compte avec solde 0 et retourne le compte créé.
     */
    public function creerCompte(string $numero): array
    {
        $this->insert(['numero' => $numero, 'solde' => 0]);
        return $this->findByNumero($numero);
    }

    /**
     * Vérifie que le préfixe du numéro est dans la table prefixes_operateur.
     * Le numéro doit avoir exactement 10 caractères : 3 de préfixe + 7 chiffres.
     */
    public function estNumerovalide(string $numero): bool
    {
        // Longueur exacte : 10 caractères
        if (strlen($numero) !== 10) {
            return false;
        }

        // Que des chiffres
        if (!ctype_digit($numero)) {
            return false;
        }

        $prefixe = substr($numero, 0, 3);

        $db = \Config\Database::connect('sqlite3');
        $row = $db->table('prefixes_operateur')
                  ->where('prefixe', $prefixe)
                  ->where('actif', 1)
                  ->get()
                  ->getRowArray();

        return $row !== null;
    }

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

        $typeOpId = $typeOp ? $typeOp['id'] : 1; // Fallback à 1 au cas où

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
        $frais = $this->getFrais('Transfert', $montant);
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
        $typeOp = $db->table('types_operations')
                     ->where('libelle', 'Transfert')
                     ->get()
                     ->getRowArray();
        $typeOpId = $typeOp ? $typeOp['id'] : 3;

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
