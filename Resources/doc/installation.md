# Installation in 4 simple steps

`FSiAdminTreeBundle` works with conjunction with [Tree - Nestedset behavior extension for Doctrine 2](https://github.com/l3pp4rd/DoctrineExtensions/blob/master/doc/tree.md).
The best method to use `DoctrineExtensions - Tree` in Symfony2 application is [StofDoctrineExtensionsBundle](https://github.com/stof/StofDoctrineExtensionsBundle)

## 1. Downlaod AdminTreeBundle and [StofDoctrineExtensionsBundle](https://github.com/stof/StofDoctrineExtensionsBundle)

Add to `composer.json`:

```yaml
"require": {
    "gedmo/doctrine-extensions": "^2.4",
    "fsi/admin-tree-bundle": "^2.1",
}
```

## 2. Register bundles

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
        new FSi\Bundle\AdminTreeBundle\FSiAdminTreeBundle(),
    );
}
```

## 3. Register AdminTreeBundle routing

```yaml
# app/config/routing.yml

fsi_tree_bundle:
    resource: "@FSiAdminTreeBundle/Resources/config/routing.xml"
    prefix: /
```

## 4. Enable Tree Doctrine Extension

Add to `app/config/config.yml`

```yml
stof_doctrine_extensions:
    orm:
        default:
            tree: true
```
