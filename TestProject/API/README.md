# API - COURS PHP

## Prérequis

- [WAMP](http://www.wampserver.com/) ou [LAMP](https://doc.ubuntu-fr.org/lamp) installé (avec PHP 8.2)
- [Postman](https://www.getpostman.com/) installé

## Installation

- Cloner le projet dans le dossier `www` de WAMP ou `var/www/html` de LAMP

## Utilisation

- Lancer WAMP ou LAMP
- Ouvrir Postman

### POST

- Les requêtes POST sont les seules requêtes autorisées
- Les requêtes POST doivent être envoyées au format JSON dans le body
- La réponse est au format JSON

### Routes

- `/BubbleSort/` : tri à bulle
- `/GenerateArray/` : génération d'un tableau aléatoire
- `/QuickSort/` : tri rapide

### Paramètres

- `array` : tableau à trier
- `size` : taille du tableau à générer

## Exemples

### Tri à bulle

- Requête POST sur `http://localhost/API/BubbleSort/`
- Body : `{"array": [10, 43, 3, 2, 5]}`
- Réponse : `{"array": [2, 3, 5, 10, 43]}`

### Génération d'un tableau aléatoire

- Requête POST sur `http://localhost/API/GenerateArray/`
- Body : `{"size": 5}`
- Réponse : `{"array": [10, 43, 3, 2, 5]}`

### Tri rapide

- Requête POST sur `http://localhost/API/QuickSort/`
- Body : `{"array": [10, 43, 3, 2, 5]}`
- Réponse : `{"array": [2, 3, 5, 10, 43]}`

## Auteur

- [Valentin LAMINE](https://github.com/valentinlamine/)