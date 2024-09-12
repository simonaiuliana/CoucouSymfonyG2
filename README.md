# Coucou !

Après la création de la database en `MySQL` nommée `coucousymfonyg2`,

Dans le `.env.local` on met le chemin vers la DB (en commentant le lien vers `postgresql`)

```env
DATABASE_URL="mysql://root:@127.0.0.1:3306/coucousymfonyg2?serverVersion=8.0.31&charset=utf8mb4"
```

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

