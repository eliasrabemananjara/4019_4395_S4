TODO LIST

V1
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

- OK[ETU4019] Creer TypeOperationModel pour la table types_operations.
  Valider que le libelle est requis et unique.

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

## Base de donnees

- OK[ETU4019] Creer la table frais_sup avec le champ pourcentage.
  Stocker le pourcentage de frais supplementaires pour les transferts inter-operateurs.
  Donnee initiale : 10%.

- OK[ETU4019] Creer la vue vue_situation_comptes.
  Exposer id, numero, solde et date_creation de tous les comptes.

- OK[ETU4019] Creer la vue vue_gains_operateur.
  Joindre transactions, types_operations, comptes, prefixes_operateur et operateurs.
  Filtrer sur les operations Retrait et Transfert.

- OK[ETU4019] Creer la vue vue_total_gains_operateur.
  Calculer la somme des frais par operateur via LEFT JOIN pour inclure les operateurs sans transactions.

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
  Calculer les frais de transfert de base via le bareme.
  Ajouter un frais supplementaire (pourcentage de frais_sup) si le destinataire est d'un operateur different.
  Ajouter optionnellement les frais de retrait si le destinataire est du meme operateur (inclureFraisRetrait).

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

V2

---

# Module Client V2

## Controller

- OK[ETU4395] TransfertClient::index()   GET  /client/transfert : affiche le formulaire de transfert multi-destinataires.
- OK[ETU4395] TransfertClient::process() POST /client/transfert : traite les transferts vers plusieurs destinataires en une seule operation.
  Accepte des tableaux de numeros et de montants.
  Verifie que tous les destinataires appartiennent au meme operateur que l'expediteur.
  Debite le total (montants + frais) du compte source et credite chaque destinataire.

## Views

- OK[ETU4395] Modifier transfaire.php    : ajout d'un bouton "Ajouter destinataire" pour saisir plusieurs destinataires dynamiquement.
  Filtrage frontend pour restreindre les destinataires au meme operateur.
  Suppression dynamique de lignes destinataires.

---

# Module Operateur V2

## Views

- OK[ETU4019] Modifier situation_gains.php : grouper les details des transactions par operateur.
  Un tableau distinct est affiche pour chaque operateur avec ses propres transactions.
  Chaque tableau affiche un pied de page avec le total des frais collectes par cet operateur.
  La colonne "Operateur" est supprimee des lignes du tableau (remplacee par l'en-tete de groupe).