# DAL - COURS PHP

## Prérequis

- [WAMP](http://www.wampserver.com/) ou [LAMP](https://doc.ubuntu-fr.org/lamp) installé (avec PHP 8.2)
- [Postman](https://www.getpostman.com/) installé

## Installation

- Cloner le projet dans le dossier `www` de WAMP ou `var/www/html` de LAMP
- Créer le dossier `credentials` dans le dossier `wamp` ou `lamp`
- Y déposer un fichier `db.json` afin de configurer la connexion à la base de données

### Exemple d'un fichier db.json

```json 
{
    "servername": "localhost",
    "port": "3306",
    "dbname": "php_b2",
    "charset": "utf8mb4",
    "username": "your_username",
    "password": "your_password"
}
```
## Utilisation

- Lancer WAMP ou LAMP
- Ouvrir Postman

### Utilisation du DAL via l'API

- Requêtes POST sur `http://localhost/API/TestDal/`
- Les requêtes POST sont les seules requêtes autorisées

### Informations annexes sur le DAL

Tout d'abord monsieur, je tiens à m'excuser, car ce TP DAL n'est pas complet.
Entre problèmes de santé (Covid, migraine ophtalmique...) et un manque d'anticipation de ma part, je me suis retrouvé à faire ce TP trop tard.
J'ai donc préféré vous rendre quelque chose de fonctionnel, mais incomplet, plutôt que de ne rien rendre du tout.
J'ai ainsi fait le choix de faire mon maximum pour créer toutes les fonctions du DAL, hélas sans avoir eu le temps de beaucoup les tester.
Elles contiennent probablement de nombreuses erreurs, même si en théorie celle appellé dans l'endpoint `TestDal` fonctionne.
Je vous prie de bien vouloir m'excuser pour cette erreur, et vous remercie pour votre compréhension.
Si vous avez des questions, n'hésitez pas à me contacter.

## Auteur

- [Valentin LAMINE](https://github.com/valentinlamine/)