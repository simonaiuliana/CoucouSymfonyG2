# Coucou !

Après la création de la database en `MySQL` nommée `coucousymfonyg2`,

Dans le `.env.local` on met le chemin vers la DB (en commentant le lien vers `postgresql`)

```env
DATABASE_URL="mysql://root:@127.0.0.1:3306/coucousymfonyg2?serverVersion=8.0.31&charset=utf8mb4"
```

## Création de la DB

    php bin/console doctrine:database:create

## Création d'entités

Nous allons créer des entités.

    php bin/console make:entity Post

    php bin/console make:entity Section

    php bin/console make:entity Tag

    php bin/console make:entity Comment

Nous avons 4 entités vides (mise à part l'id) et leurs Repository ("Managers" complémentaires à `Doctrine`).

Comme on va utiliser `MySQL`, on va modifier l'id en `unsigned`

```php
#[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

### modifié dans tous les Entities en
#[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(
        options:
            [
                'unsigned' => true,
            ]
    )]
    private ?int $id = null;
```

### Migration

    php bin/console make:migration

Création d'un fichier dans `migrations`

Migration:

    php bin/console doctrine:migration:migrate
    ou
    php bin/console d:m:m

### Modification de Post

    php bin/console make:entity Post

```php
<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(
        # on veut que l'ID soit 'unsigned'
        options:
        [
            'unsigned' => true,
        ]
    )]
    private ?int $id = null;

    #[ORM\Column(length: 160)]
    private ?string $postTitle = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $postDescription = null;

    #[ORM\Column(
        type: Types::DATETIME_MUTABLE,
        # valeur par défaut CURRENT_TIMESTAMP
        options: [
            'default' => 'CURRENT_TIMESTAMP',
        ]
    )]
    private ?\DateTimeInterface $postDateCreated = null;

    #[ORM\Column(
        # il peut être null
        type: Types::DATETIME_MUTABLE,
        nullable: true)]
    private ?\DateTimeInterface $postDatePublished = null;

    #[ORM\Column]
    private ?bool $postPublished = null;

    ### getters and setters

```

Si on vérifie qu'il y a des différences entre la DB et les entités :

    php bin/console doctrine:migrations:diff

Doctrine se permet de créer une migration si nécessaire !

Exécution de la migration

    php bin/console d:m:m

### Modification de Section

    php bin/console make:entity Section

```php
<?php



#[ORM\Entity(repositoryClass: SectionRepository::class)]
class Section
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(
        options:
        [
            'unsigned' => true,
        ]
    )]
    private ?int $id = null;

    #[ORM\Column(length: 160)]
    private ?string $sectionTitle = null;

    #[ORM\Column(length: 600, nullable: true)]
    private ?string $sectionDescription = null;

  
}
```

### Modification de Tag

    php bin/console make:entity Tag

```php
<?php



#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(
        options:
        [
            'unsigned' => true,
        ]
    )]
    private ?int $id = null;

    #[ORM\Column(
        # ce sera un champ unique
        length: 60,
        unique: true,
    )]
    private ?string $tagName = null;

   
}
```

### Modification de Comment

    php bin/console make:entity Comment

```php
<?php
#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(
        options:
            [
                'unsigned' => true,
            ]
    )]
    private ?int $id = null;

    #[ORM\Column(length: 2500)]
    private ?string $commentMessage = null;

    #[ORM\Column(
        type: Types::DATETIME_MUTABLE,
        options: [
            'default' => 'CURRENT_TIMESTAMP',
        ]
    )]
    private ?\DateTimeInterface $commentDateCreated = null;
```

On fait la migration


    php bin/console make:migration

    php bin/console d:m:m


### Les relations

#### Post -> M2M -> Section

On va commencer par la relation `ManyToMany` depuis `Post` vers `Section`

    php bin/console make:entity Post

On choisit `sections` -> `ManyToMany` -> `Section` -> `yes` -> `posts`

#### Post -> M2M -> Tag

    php bin/console make:entity Post

On choisit `tags` -> `ManyToMany` -> `Tag` -> `yes` -> `posts`


#### Post -> One2M -> Comment

    php bin/console make:entity Post

On choisit `comments` -> `OneToMany` -> `Comment` -> `post` -> `no` -> `no`

## Création d'un User

    php bin/console make:user

on choisit `User` -> `yes`-> `username` -> `yes`

Une table `user` est créée, avec la particularité de servir pour la connexion au site.

https://symfony.com/doc/current/security.html

### Relation User OneToMany Post

    php bin/console make:entity User

on choisit `posts` -> `OneToMany`-> `Post` -> `user` -> `no` -> `no`

### Relation User OneToMany Comment

```bash
php bin/console make:entity User
 Your entity already exists! So let's add some new fields!

 New property name (press <return> to stop adding fields):
 > comments

 Field type (enter ? to see all types) [string]:
 > OneToMany
OneToMany

 What class should this entity be related to?:
 > Comment
Comment

 A new property will also be added to the Comment class so that you can access and set the related User object from it.

 New field name inside Comment [user]:
 >

 Is the Comment.user property allowed to be null (nullable)? (yes/no) [yes]:
 > no

 Do you want to activate orphanRemoval on your relationship?
 A Comment is "orphaned" when it is removed from its related User.
 e.g. $user->removeComment($comment)

 NOTE: If a Comment may *change* from one User to another, answer "no".

 Do you want to automatically delete orphaned App\Entity\Comment objects (orphanRemoval)? (yes/no) [no]:
 >

 updated: src/Entity/User.php
 updated: src/Entity/Comment.php

```

On ajoute `commentPublished` avec false à `Comment`

```php
#[ORM\Column(
        options: [
            'default' => false,
        ]
    )]
    private ?bool $commentPublished = null;
```

On fait une migration

### Création du formulaire de connexion

    php bin/console make:security:form-login

```bash
php bin/console make:security:form-login

 Choose a name for the controller class (e.g. SecurityController) [SecurityController]:
 >

 Do you want to generate a '/logout' URL? (yes/no) [yes]:
 >

 Do you want to generate PHPUnit tests? [Experimental] (yes/no) [no]:
 >

 created: src/Controller/SecurityController.php
 created: templates/security/login.html.twig
 updated: config/packages/security.yaml


  Success!


 Next: Review and adapt the login template: security/login.html.twig to suit your needs.

```

On va créer un `User` manuellement dans la DB

Comme `username` :

    coucou

Comme `roles` :

    [
    "ROLE_ADMIN"
    ]

Comme `password` :

    coucou123

! on doit hacher le mot de passe avec la commende :

    php bin/console security:hash-password 

### Création d'un menu

`templates/coucou/_menu.html.twig`

```twig
<nav>
    <a href="{{ path('coucou') }}">Accueil</a>
    <a href="{{ path('app_login') }}">Login</a>
</nav>
```

Il est appelé depuis `templates/coucou/index.html.twig` et dans  avec 

    {% include 'coucou/_menu.html.twig' %}

On le modifie

```twig
<nav>
    <a href="{{ path('coucou') }}">Accueil</a>
    {#  si nous ne sommes pas connectés #}
    {% if not is_granted('IS_AUTHENTICATED_FULLY') %}
    <a href="{{ path('app_login') }}">Login</a>
    {% else %}
    <a href="{{ path('app_logout') }}">Logout</a>
    {% endif %}
</nav>
```

On peut empêcher un user connecté de retourner sur `/login` :

```php
# src/Controller/SecurityController.php

# ...
 #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // si on est déjà connecté et qu'on souhaite revenir sur login
        if($this->isGranted('IS_AUTHENTICATED_FULLY')) {
        // Ou if($this->getUser()) {
            // on retourne sur l'accueil
            return $this->redirectToRoute('coucou');
        }
# ...
```

## Les thèmes de formulaires

### Avec AssetMapper

documentation : https://symfony.com/doc/current/form/form_themes.html#symfony-builtin-forms

Dans le fichier, rajoutez le formulaire bootstrap

# config/packages/twig.yaml
    twig:
        form_themes: ['bootstrap_5_layout.html.twig']

Puis installons `bootstrap`

    php bin/console importmap:require bootstrap

    [OK] 3 new items (bootstrap, @popperjs/core,
    bootstrap/dist/css/bootstrap.min.css) added to the importmap.php!

Les fichiers se trouvent dans `asset`

Pour le `CSS`, on va dans `assets/app.js` et rajoute le lien vers le css

    import 'bootstrap/dist/css/bootstrap.min.css';

## Utilisation d'un template Bootstrap 5

Nous prenons ce template :

https://getbootstrap.com/docs/5.0/examples/navbar-static/

On va récupérer le code nécessaire et les mettre dans le dossier `assets`

`templates/base.html.twig`

```twig
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>{% block title %}CoucouSymfonyG2{% endblock %}</title>
        {% block stylesheets %}
        {% endblock %}

        {% block javascripts %}
            {% block importmap %}{{ importmap('app') }}{% endblock %}
        {% endblock %}
    </head>
    <body>
        {# utilisation de content pour nos templates #}
        {% block content %}{% endblock %}
        {# On laisse body pour les fichiers générés par symfony #}
        {% block body %}{% endblock %}
    </body>
</html>

```