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

---

# Module Client

## Models

- OK[ETU4395] Creer ConnectionClientModel pour la table comptes.
  Methodes : findByNumero(), creerCompte(), getFrais(), majSolde(), enregistrerTransaction(), estNumerovalide().
  Valider le prefixe (actif, 3 chiffres) et la longueur du numero (10 chiffres).

- OK[ETU4395] Creer DepotClientModel (herite de ConnectionClientModel).
  Methode deposer() : credite le solde et enregistre la transaction (frais = 0).

- OK[ETU4395] Creer RetraitClientModel (herite de ConnectionClientModel).
  Methode retirer() : verifie le solde, debite montant + frais, enregistre la transaction.

- OK[ETU4395] Creer TransfertClientModel (herite de ConnectionClientModel).
  Methode transferer() : verifie le solde source, cree le compte destinataire si inexistant, debite source et credite destination.

- OK[ETU4395] Creer HistoriqueClient pour la vue SQL vue_historique_transactions.
  Methode getHistoriqueByNumero() : recupere toutes les transactions du client (envoyees et recues).

## Controller

- OK[ETU4395] ConnectionClient::index()    GET  /client              : affiche le formulaire de connexion.
- OK[ETU4395] ConnectionClient::connect()  POST /client/connect      : valide le numero, cree ou connecte le compte, stocke en session.
- OK[ETU4395] ConnectionClient::dashboard() GET /client/dashboard    : affiche le tableau de bord du client connecte.
- OK[ETU4395] ConnectionClient::logout()   GET  /client/logout       : supprime la session et redirige.
- OK[ETU4395] DepotClient::process()       POST /client/depot        : traite le depot et met a jour la session.
- OK[ETU4395] RetraitClient::index()       GET  /client/retrait      : affiche le formulaire de retrait.
- OK[ETU4395] RetraitClient::process()     POST /client/retrait      : traite le retrait avec calcul des frais.
- OK[ETU4395] TransfertClient::index()     GET  /client/transfert    : affiche le formulaire de transfert.
- OK[ETU4395] TransfertClient::process()   POST /client/transfert    : traite le transfert avec calcul des frais.
- OK[ETU4395] HistoriqueClient::index()    GET  /client/historique   : affiche l'historique des transactions du client.

## Views

- OK[ETU4395] Creer connectionClient.php : formulaire de saisie du numero de telephone.
- OK[ETU4395] Creer dashboard.php        : affiche le solde, les boutons depot/retrait/transfert/historique.
- OK[ETU4395] Creer retrait.php          : formulaire de retrait avec affichage du solde.
- OK[ETU4395] Creer transfaire.php       : formulaire de transfert (numero destinataire + montant).
- OK[ETU4395] Creer historique.php       : tableau de l'historique des transactions du client.
.
dans la page de transfert , on choisi le prefixe par de liste deroulante , et ajoute aussi in check box qui permet de inclure frais de retrait lors de l’envoi. Quand le numere est meme operateur a le numereau du client qui fait le transfert, le checkbox est cochable(sinom il ne pas cochable). quand on coche le checkbox , les frais de retrais sont inclus dans le montant transferer(donc on envoye le frais montant et frais 2 fois) (ex:je veut transferer 10000 ar dans le meme operateur ; frais de transfert = 1000 ar ; frais de retrais = 1000 ar  .si on coche le checkbox , mon solde doit diminuer de 12000 ar : 1000 ar frais de transfert et 11000 ar  envoyer au destinataire  ). 