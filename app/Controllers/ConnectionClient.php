<?php

namespace App\Controllers;

use App\Models\ConnectionClient as ConnectionModel;

class ConnectionClient extends BaseController
{
    /**
     * Affiche le formulaire de connexion client.
     */
    public function index(): string
    {
        return view('client/connectionClient');
    }

    /**
     * Traite la soumission du formulaire.
     * - Valide le numéro (préfixe autorisé + 7 chiffres = 10 au total)
     * - Si le numéro existe dans `comptes` → connexion
     * - Sinon → création du compte + connexion
     */
    public function connect(): \CodeIgniter\HTTP\RedirectResponse
    {
        $numetel = $this->request->getPost('numetel');

        $model = new ConnectionModel();

        // --- Validation ---
        if (empty($numetel)) {
            return redirect()->route('client.form')
                ->with('error', 'Veuillez entrer un numéro de téléphone.');
        }

        if (!$model->estNumerovalide($numetel)) {
            return redirect()->route('client.form')
                ->with('error', 'Numéro invalide. Il doit comporter 10 chiffres avec un préfixe autorisé (032, 033, 034, 037, 038).');
        }

        // --- Chercher ou créer le compte ---
        $compte = $model->findByNumero($numetel);

        if ($compte === null) {
            // Nouveau client → créer le compte
            $compte = $model->creerCompte($numetel);
            session()->setFlashdata('success', 'Compte créé avec succès ! Bienvenue.');
        }

        // --- Stocker en session ---
        session()->set('client', [
            'id'     => $compte['id'],
            'numero' => $compte['numero'],
            'solde'  => $compte['solde'],
        ]);

        // --- Rediriger vers le tableau de bord client ---
        return redirect()->route('client.dashboard');
    }

    /**
     * Affiche le tableau de bord du client connecté.
     */
    public function dashboard(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (!session()->has('client')) {
            return redirect()->route('client.form')
                ->with('error', 'Veuillez vous connecter d\'abord.');
        }

        return view('client/dashboard');
    }

    /**
     * Déconnecte le client.
     */
    public function logout(): \CodeIgniter\HTTP\RedirectResponse
    {
        session()->remove('client');
        return redirect()->route('client.form')
            ->with('success', 'Vous avez été déconnecté.');
    }
}
