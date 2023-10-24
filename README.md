# IoT Simulation

Au cours des 5 derniers jours, j'ai travaillé assidûment sur ce projet. L'application que j'ai développée vous permet de créer des modules et de les simuler. Suite à la simulation, vous recevrez des informations sur l'état de fonctionnement des modules. De plus, l'application vous offre la possibilité de consulter l'historique des simulations précédentes. Pour une analyse plus poussée, un outil graphique est également disponible pour vous aider à visualiser et à interpréter l'ensemble des données.

## Installation

1. Assurez-vous d'avoir **composer** et **Symfony** installés sur votre machine. Si vous n'avez pas encore installé composer, exécutez cette commande : `composer install`

2. Si vous utilisez **Xampp**, placez le projet dans le dossier `htdocs`. Vous pouvez tout de même lancer le projet à l'aide de la console de VS Code sans avoir mit le projet dans htdocs. Pour cela, démarrez le serveur Symfony avec la commande : `symfony server:start`

3. Pour que les notification s'affiche a l'aide du bundle toastr vous devez l'installer avec la commande suivante : `composer require symfony/ux-toastr`
4. 



5. Assurez-vous de configurer la `Database_url` dans le fichier `.env` de votre projet Symfony pour qu'il corresponde à votre gestionnaire de base de données.

6. Pour migrer la base de données vers votre gestionnaire, exécutez la commande : `php bin/console doctrine:migrations:migrate`


## Technologies Utilisées

Ce projet a été développé en utilisant le framework **Symfony**. 

### Front-end:
- **Bootstrap**: J'ai utilisé Bootstrap pour le design, afin d'obtenir une interface esthétiquement plaisante.
- **Toastr Bundle**: Pour afficher les notifications, notamment lorsqu'une panne se produit ou lorsqu'un module redémarre.

### Back-end:
- **Controller Symfony**: Des fonctions ont été élaborées dans le contrôleur Symfony pour garantir la bonne marche des simulations.
- **Ajax**: Utilisé pour améliorer l'interaction avec l'utilisateur. Grâce à Ajax, les mises à jour du contenu sont plus fluides et il n'est pas nécessaire de recharger toute la page après chaque action, offrant une expérience web plus réactive.



