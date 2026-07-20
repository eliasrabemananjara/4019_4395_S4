TODO LIST

# Module Operateur

Gestion des Prefixes (prefixes.php)
  Gerer les prefixes de numeros autorises par l'operateur.
  - OK Table prefixes_operateur dans la base de donnees.
  - OK Modele PrefixesOperateurModel (validation requis, unique, longueur 3-5).
  - OK Actions prefixes() et ajouter() dans OperateurController.
  - OK Routes GET /operateur/prefixes et POST /operateur/prefixes/ajouter.
  - OK Vue app/Views/operateur/prefixes.php (liste et formulaire d'ajout).

Baremes de Frais (baremes.php & edit_bareme.php)
  Gerer les frais de transaction par tranche de montant.
  - OK Table baremes_frais et relation avec types_operations.
  - OK Modele BaremeFraisModel avec la methode getFullBaremes().
  - OK Actions baremes(), editBareme() et updateBareme() dans OperateurController.
  - OK Routes GET /operateur/baremes, GET /operateur/baremes/edit/(:num) et POST /operateur/baremes/update/(:num).
  - OK Vues app/Views/operateur/baremes.php et edit_bareme.php.

Situation des Comptes Clients (situation_clients.php)
  Visualiser les comptes clients et leur solde actuel.
  - OK Table comptes et vue SQL vue_situation_comptes.
  - OK Modele CompteModel.
  - OK Action clients() dans OperateurController.
  - OK Route GET /operateur/clients.
  - OK Vue app/Views/operateur/situation_clients.php.

Situation des Gains de l'Operateur (situation_gains.php)
  Suivre les gains generes par les retraits et transferts.
  - OK Table transactions et vue SQL vue_gains_operateur.
  - OK Modele TransactionModel.
  - OK Action gains() dans OperateurController.
  - OK Route GET /operateur/ma-gain.
  - OK Vue app/Views/operateur/situation_gains.php.

