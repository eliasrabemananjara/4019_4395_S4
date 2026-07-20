TODO LIST

# Module Operateur

## Models

- OK[ETU4019] Creer PrefixesOperateurModel pour la table prefixes_operateur.
  Valider que le prefixe est requis, unique et a une longueur entre 3 et 5 caracteres.

- OK[ETU4019] Creer CompteModel pour la table comptes.
  Valider le numero de compte et le solde.

- OK[ETU4019] Creer BaremeFraisModel pour la table baremes_frais.
  Valider les tranches de montant (min, max) et les frais associes.
  Ajouter la methode getFullBaremes() qui fait une jointure avec types_operations.

- OK[ETU4019] Creer TransactionModel pour la table transactions.
  Valider les champs type_operation_id, montant, frais et statut.
  Ajouter la methode getFullTransactions() qui fait des jointures avec comptes et types_operations.

## Controller

- OK[ETU4019] Ajouter prefixes() dans OperateurController.
  Route GET /operateur/prefixes.
  Recuperer tous les prefixes et les passer a la vue.

- OK[ETU4019] Ajouter ajouter() dans OperateurController.
  Route POST /operateur/prefixes/ajouter.
  Valider et enregistrer un nouveau prefixe.

- OK[ETU4019] Ajouter baremes() dans OperateurController.
  Route GET /operateur/baremes.
  Recuperer tous les baremes via getFullBaremes() et les passer a la vue.

- OK[ETU4019] Ajouter editBareme($id) dans OperateurController.
  Route GET /operateur/baremes/edit/(:num).
  Recuperer un bareme par son id et l'afficher dans le formulaire.

- OK[ETU4019] Ajouter updateBareme($id) dans OperateurController.
  Route POST /operateur/baremes/update/(:num).
  Mettre a jour les champs montant_min, montant_max et frais du bareme.

- OK[ETU4019] Ajouter clients() dans OperateurController.
  Route GET /operateur/clients.
  Recuperer tous les comptes clients et les passer a la vue.

- OK[ETU4019] Ajouter gains() dans OperateurController.
  Route GET /operateur/ma-gain.
  Recuperer les transactions de type Retrait et Transfert.
  Calculer le total des frais et passer les donnees a la vue.

## Views

- OK[ETU4019] Creer prefixes.php.
  Afficher la liste des prefixes enregistres.
  Afficher un formulaire pour ajouter un nouveau prefixe.

- OK[ETU4019] Creer baremes.php.
  Afficher un tableau de toutes les tranches de frais avec le type d'operation.
  Ajouter un bouton Editer pour chaque tranche.

- OK[ETU4019] Creer edit_bareme.php.
  Afficher un formulaire pre-rempli avec les donnees du bareme selectionne.
  Soumettre la modification via POST.

- OK[ETU4019] Creer situation_clients.php.
  Afficher la liste de tous les comptes avec leur numero, solde et date de creation.

- OK[ETU4019] Creer situation_gains.php.
  Afficher le total des gains en haut de la page.
  Afficher le tableau de l'historique des transactions generant des frais.
