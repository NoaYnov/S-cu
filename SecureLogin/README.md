# DAL - COURS PHP

## Prérequis

- [XAMPP](https://www.apachefriends.org/fr/index.html) installé (avec PHP 8.2)
- [Postman](https://www.getpostman.com/) installé

## Installation

- Cloner le projet dans le dossier `htdocs` de Xampp
- Créer le dossier `credentials` dans le dossier `xampp`
- Y déposer un fichier `db.json` afin de configurer la connexion à la base de données

### Exemple d'un fichier db.json

```json 
{
    "servername": "localhost",
    "port": "3306",
    "dbname": "login",
    "charset": "utf8mb4",
    "username": "your_username",
    "password": "your_password"
}
```
## Utilisation

- Lancer XAMPP
- Ouvrir Postman

### Utilisation du DAL via l'API
- Ouvrir Postman
- Importer le fichier `Request Secure Login.postman_collection.json`
  Cela vous donnera accès à toutes les requêtes nécessaires pour tester l'API =)

### Listes des Requêtes
1. Création du compte temporaire :```localhost/API/SignUp/```
2. Vérification du compte :```localhost/API/VerifyAccount/```
3. Connexion à un compte :```localhost/API/SignIn/```
4. Déconnexion d'un compte :```localhost/API/SignOut/```
5. Vérification de la connexion :```localhost/API/SignedIn/```
6. Changement de mot de passe :```localhost/API/ChangePassword/```
7. Suppression du compte :```localhost/API/DeleteAccount/```
8. Ajout d'un service :```localhost/API/AddService/```
9. Affichage des services :```localhost/API/DisplayService/```
10. Suppression d'un service :```localhost/API/DeleteService/```
11. Lier un service à un compte :```localhost/API/LinkServiceToAccount/```
12. Délier un service à un compte :```localhost/API/UnlinkServiceToAccount/```

