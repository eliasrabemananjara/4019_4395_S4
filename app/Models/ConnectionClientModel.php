<?php

namespace App\Models;

use CodeIgniter\Model;

class ConnectionClientModel extends Model
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

    protected function getDatabase()
    {
        return \Config\Database::connect($this->DBGroup);
    }

    protected function getTypeOperationId(string $libelle, int $defaultId): int
    {
        $db = $this->getDatabase();
        $typeOp = $db->table('types_operations')
            ->where('libelle', $libelle)
            ->get()
            ->getRowArray();

        return $typeOp ? (int) $typeOp['id'] : $defaultId;
    }

    protected function getFrais(string $libelleOperation, float $montant): float
    {
        $db = $this->getDatabase();
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

        return $bareme ? (float) $bareme['frais'] : 0;
    }

    protected function majSolde(int $compteId, float $montant, string $operation): void
    {
        $db = $this->getDatabase();
        $builder = $db->table($this->table);
        $builder->set('solde', "solde {$operation} {$montant}", false);
        $builder->where('id', $compteId);
        $builder->update();
    }

    protected function enregistrerTransaction(int $typeOperationId, ?int $compteSourceId, ?int $compteDestinationId, float $montant, float $frais, string $statut = 'SUCCES'): void
    {
        $db = $this->getDatabase();
        $db->table('transactions')->insert([
            'type_operation_id' => $typeOperationId,
            'compte_source_id' => $compteSourceId,
            'compte_destination_id' => $compteDestinationId,
            'montant' => $montant,
            'frais' => $frais,
            'statut' => $statut,
        ]);
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

        $db = $this->getDatabase();
        $row = $db->table('prefixes_operateur')
            ->where('prefixe', $prefixe)
            ->where('actif', 1)
            ->get()
            ->getRowArray();

        return $row !== null;
    }
}
