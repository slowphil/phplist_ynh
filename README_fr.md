# Phplist pour YunoHost

[![Integration level](https://dash.yunohost.org/integration/phplist.svg)](https://dash.yunohost.org/appci/app/phplist)

> Ce package vous permet d'installer [Phplist] (https://www.phplist.org/) sur un serveur YunoHost.  
Si vous n'avez pas YunoHost, veuillez consulter [ici](https://yunohost.org/#/install) pour savoir comment l'installer et en profiter.*

## Vue d'ensemble

Phplist gère l'envoi de courriels à des listes. Les listes peuvent être téléchargées mais l'utilisateur peut également s'abonner/se désabonner.

**Note :** This App est mature, mais son Yunohost package est très récent. Le package passe tous les tests automatisés donc les fonctionalités basiques d'installation/suppression/sauvergade/restauration devraient fonctionner normalement. Il peut cependant rester des problèmes à l'utilisation liés au packaging. -- SVP, faites moi savoir si vous rencontrez ce genre de problème!

**Version livrée:** 3.6.1
[source repo](https://github.com/phpList/phplist3)

## Démo
[page de démonstration](https://www.phplist.org/demo/)

## Configuration
Lors de l'installation, vous définissez un utilisateur qui a le droit d'administration pour phplist. Lorsqu'il est connecté à Yunohost, cet utilisateur accède automatiquement à la page d'administration de phplist en cliquant sur la tuile (ou en ajoutant /admin/ à l'url de base de l'application). D'autres utilisateurs peuvent également recevoir cette permission. Le compte "admin" créé par défaut peut être supprimé sans problème.

Après l'installation, allez dans le panneau d'administration > onglet "config". Vous voudrez probablement personnaliser de nombreuses choses concernant la réception d'abonnements et l'envoi de courriels pour votre organisation. Voir le [manuel] (https://www.phplist.org/manual/books/phplist-manual/). Vous pouvez également importer des listes d'adresses existantes.

### Basculer le mode test
Immédiatement après l'installation, Phplist fonctionne en mode test et n'envoie pas de courriels. Ce n'est pas un bogue : avant d'être utilisé, beaucoup de choses doivent être configurées correctement et doivent être testées (sinon vous seriez rapidement catalogué comme spammeur). 

Une fois configuré et testé, le mode démo peut être désactivé depuis le panneau de configuration (expérimental) de l'application.
Pour accéder à ce panneau de configuration, allez dans votre panneau d'administration Yunohost, naviguez jusqu'à votre installation Phplist et ajoutez /config-panel au chemin, par exemple
https://mydomain.tld/yunohost/admin/#/apps/phplist/config-panel 

## Documentation
Cet installateur est dérivé de l'application [modèle](https://github.com/YunoHost/example_ynh) de yunohost
avec des petits bouts (panneau de configuration et actions) tirés de [my_webapp_ynh](https://github.com/YunoHost-Apps/my_webapp_ynh) ([documentation](https://github.com/YunoHost/doc/blob/master/app_my_webapp.md)).

[documentation Phplist](https://www.phplist.org/)

## Caractéristiques spécifiques de YunoHost

#### Support multi-utilisateurs

Les auteurs LDAP et HTTP sont-ils pris en charge ? Oui, via un plugin SSO de Yunohost.

L'application peut-elle être utilisée par plusieurs utilisateurs ? Oui. L'application est destinée à être publiquement visible (les visiteurs peuvent y accéder) afin que les gens puissent s'abonner à vos listes.  
Tous les utilisateurs qui reçoivent la permission d'administrateur de phplist (la même que l'utilisateur administrateur déclaré lors de l'installation) sont considérés comme des super-utilisateurs dans phplist.

#### Architectures supportées
* x86-64 - [![Build Status](https://ci-apps.yunohost.org/ci/logs/phplist.svg)](https://ci-apps.yunohost.org/ci/apps/phplist/)
* ARMv8-A - [![Build Status](https://ci-apps-arm.yunohost.org/ci/logs/phplist.svg)](https://ci-apps-arm.yunohost.org/ci/apps/phplist/)

## Limitations
Fail2ban n'est pas encore configuré, mais la page d'admin est quand même protégée par le SSO de Yunohost. Contactez-moi si vous savez comment configurer F2B.
Aucun log d'activité n'est créé pour le moment.

## Informations complémentaires
Dans ce paquet, phpList est livré avec les plugins par défaut (pas tous activés), plus un plugin pour le SSO Yunohost. Cependant, pour des raisons de sécurité, l'ajout de plugins supplémentaires pour phpList ne peut se faire qu'avec l'aide d'un administrateur Yunohost (le répertoire des plugins est en lecture seule).

Le plugin Captcha est activé à l'installation, néanmoins, la page d'abonnement par défaut n'en fait pas usage. Créez une votre page d'inscription pour en bénéficier. 

L'API est désactivée (c'est la valeur par défaut pour phplist). Si vous en avez besoin, clonez ce répertoire et modifiez le script d'installation pour l'activer (non testé).

La mise à jour intégrée de phpList a été supprimée. Les mises à jour seront gérées par ce paquet.

## Liens

 * Signaler un bogue : https://github.com/slowphil/phplist_ynh/issues
 * Site web de YunoHost : https://yunohost.org/

---
