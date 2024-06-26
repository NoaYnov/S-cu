# API - COURS PHP

## Prérequis

- [XAMPP](https://www.apachefriends.org/fr/index.html) installé (avec PHP 8.2)
- [Postman](https://www.getpostman.com/) installé

## Installation

- Cloner le projet dans le dossier `htdocs` de Xampp

## Utilisation

- Lancer XAMPP
- Ouvrir phpMyAdmin
- Créer une base de données `login`
- Importer le fichier `login.sql` dans la base de données `login`
- Ouvrir Postman

## REQUETES

- Ouvrir Postman
- Importer le fichier `Request Secure Login.postman_collection.json`
Cela vous donnera accès à toutes les requêtes nécessaires pour tester l'API =)


# Endpoints

### Création d'un Compte (dans la table 'accounttmp') :
- Création d'un compte dans la table 'accounttmp'
- Si le compte existe déjà, on renvoie une erreur
### Verification du Compte (dans la table 'account') :
- Création d'un compte dans la table 'account'
- Création d'un user dans la table 'user'
- Création d'un signedin dans la table 'signedin' (pour savoir si le device est connecté)
- Si le compte existe déjà, on renvoie une erreur
- Si le compte n'existe pas dans `accounttmp`, on renvoie une erreur
### Connexion à un compte :
- Connexion à un compte (par mdp ou par token)
- Modifie le signedin à 1
- Si le mémorized est à 1, on crée un token et on le renvoie
- Si le mémorized est à 0, on ne crée pas de token
- Si le mdp est faux ou token expiré, on renvoie une erreur
- Si le compte n'existe pas, on renvoie une erreur
- Si le compte est déjà connecté, on renvoie une erreur
- Si le compte n'est pas vérifié, on renvoie une erreur
### Créer un token :
- Créer un token pour un compte
- Si le compte n'existe pas, on renvoie une erreur
- Si le compte n'est pas connecté, on renvoie une erreur
- Si le compte n'est pas vérifié, on renvoie une erreur
- Si le compte n'a pas de mdp, on renvoie une erreur
- Si le compte a déjà un token, on renvoie une erreur
- Si le token est expiré, on renvoie une erreur
### Déconnexion d'un compte :
- Déconnexion d'un compte
- Modifie le signedin à 0
- Si le compte n'existe pas, on renvoie une erreur
- Si le compte n'est pas connecté, on renvoie une erreur
- Si le compte n'est pas vérifié, on renvoie une erreur
### Verifie si un device est connecté :
- Verifie si un device est connecté
- Si le compte n'est pas connecté, on renvoie une qu'il n'est pas connecté
- Si le compte n'existe pas, on renvoie une erreur
- Si le compte n'est pas vérifié, on renvoie une erreur
### Changer de mot de passe :
- Changer de mot de passe
- Si le compte n'existe pas, on renvoie une erreur
- Si le compte n'est pas connecté, on renvoie une erreur
- Si le compte n'est pas vérifié, on renvoie une erreur
- Si le mdp est faux, on renvoie une erreur
- Si le nouveau mdp est le même que l'ancien, on renvoie une erreur
- Si le nouveau mdp ne respecte pas les normes, on renvoie une erreur
### Ajouter un service:
- Ajoute un service(Exemple: Google, Facebook, etc)
### Ajouter un service à un compte:
- Ajoute un service à un compte
### Supprimer un Compte:
- Supprime un compte
- Supprime le user
- Supprime le signedin
- Supprime le token
- Supprime le service auquel il est lié
- Si le compte n'existe pas, on renvoie une erreur
- Si le compte n'est pas connecté, on renvoie une erreur
- Si le compte n'est pas vérifié, on renvoie une erreur
- Si le mdp est faux, on renvoie une erreur



