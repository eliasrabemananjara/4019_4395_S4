<?php

namespace App\Models;

class EpargneClientModel extends ConnectionClientModel
{

    protected $allowedFields = ['numero' , 'solde' ,'pourccentage_epargne','solde_epargne'];
    public function definirPourcentage(int $compteId , float $pourcentage): bool{
        return $this->update($compteId , [
            'pourcentage_epargne' => $pourcentage,
        ]);
    }
}
