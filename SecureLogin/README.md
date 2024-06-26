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

