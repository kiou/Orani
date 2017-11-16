## Administration
* Création d'une actualité
* Gestion d'une actualité
* Modifier une actualité
* Poid d'une actualité
* Publication d'une actualié
* Ajouter une catégorie
* Gestion des catégories
* Modifier une catégorie

## Client
* Liste des actualités
* Trier par catégorie
* Page d'une actualité
* Template mise en avant des actualités

## Dépendances
* RefrencementBundle
* img/actualite/tmp
* img/actualite/miniature
* Ajouter le menu dans le template menu du bundle GlobalBundle

## Menu
```twig
{% set menuActualite = ['admin_actualite_manager', 'admin_actualite_ajouter', 'admin_actualite_modifier'] %}

<a href="#" data-nav="actualite-menu" class="menuNav {{ getCurrentMenu(menuActualite) }}"> <i class="fa fa-newspaper-oxt"></i> Actualités <i class="fa fa-angle-right"></i></a>
<ul id="actualite-menu" class="{{ getCurrentMenu(menuActualite) }}">
    <li class="{{ getCurrentMenu(['admin_actualite_ajouter']) }}"><a href="{{ path('admin_actualite_ajouter')}}">Ajouter une actualité</a></li>
    <li class="{{ getCurrentMenu(['admin_actualite_manager']) }}"><a href="{{ path('admin_actualite_manager')}}">Gestion des actualités</a></li>
</ul>
```