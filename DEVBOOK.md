# UBeer - Devbook d'Intégration API

## 1. Analyse de l'API

### Endpoint Principal
```
https://ubeer-production.up.railway.app/
```

### Points d'accès disponibles

#### Bières (Beers)
- GET `/beers` : Liste toutes les bières
- GET `/beers/{id}` : Détails d'une bière spécifique
- PATCH `/beers/{id}` : Mise à jour du prix d'une bière

### Structure des données

#### Bière (Beer)
```json
{
    "id": integer,
    "beer": string,
    "price": float,
    "brewery_id": integer,
    "imageUrl": string
}
```

### Exemple d'utilisation

#### Récupération des bières
```bash
curl https://ubeer-production.up.railway.app/beers
```

#### Mise à jour du prix
```bash
curl -X PATCH 
     -H "Content-Type: application/json" 
     -d '{"price": 9.99}' 
     https://ubeer-production.up.railway.app/beers/5
```

## 2. Plan d'Intégration

### 2.1 Structure du Code

1. **Création des Entités**
   - Entity/Beer.php

2. **Services API**
   - Service/ApiService.php : Gestion des appels API
   - Service/BeerService.php : Logique métier pour les bières

3. **Controllers**
   - Controller/BeerController.php

### 2.2 Organisation de l'Affichage

#### Page d'Accueil
Trois sections principales basées sur les bières :

1. **Section "Bières Premium"**
   - Affichage des bières les plus chères
   - Grid de 3-4 bières par ligne

2. **Section "Bières Populaires"**
   - Grid de 3-4 bières par ligne
   - Chaque carte de bière affiche :
     * Image de la bière
     * Nom
     * Prix
     * Bouton "Détails" qui ouvre une modal avec :
       - Image plus grande
       - Description complète
       - Prix
       - Bouton d'ajout au panier

3. **Section "Nos Nouveautés"**
   - Similaire à "Bières Populaires"
   - Tri par ID (les plus récents)

### 2.3 Nouvelles Fonctionnalités

1. **Modal de Détails**
   ```html
   <!-- Structure de la modal -->
   <div class="modal fade" id="beerDetails" tabindex="-1">
     <div class="modal-dialog">
       <div class="modal-content">
         <div class="modal-header">
           <h5 class="modal-title">[Nom de la bière]</h5>
           <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
         </div>
         <div class="modal-body">
           <img src="[URL de l'image]" class="img-fluid">
           <p class="price">Prix : [Prix] €</p>
           <button class="btn btn-primary">Ajouter au panier</button>
         </div>
       </div>
     </div>
   </div>
   ```

2. **JavaScript pour la Modal**
   ```javascript
   function openBeerDetails(beerId) {
     fetch(`https://ubeer-production.up.railway.app/beers/${beerId}`)
       .then(response => response.json())
       .then(beer => {
         // Remplir la modal avec les détails
         document.querySelector('#beerDetails .modal-title').textContent = beer.beer;
         document.querySelector('#beerDetails img').src = beer.imageUrl;
         document.querySelector('#beerDetails .price').textContent = `Prix : ${beer.price} €`;
       });
   }
   ```

## 3. Sécurité et Performance

### 3.1 Sécurité
- Validation des données reçues
- Protection CSRF pour les formulaires de modification
- Vérification des droits pour les modifications de prix

### 3.2 Performance
- Lazy loading des images
- Mise en cache des requêtes API (1 heure)
- Préchargement des données pour la modal

## 4. Prochaines Étapes

1. **Phase 1 : Infrastructure**
   - [ ] Création de l'entité Beer
   - [ ] Mise en place du service API
   - [ ] Configuration du cache

2. **Phase 2 : Interface Utilisateur**
   - [ ] Intégration des cartes de bières
   - [ ] Création de la modal de détails
   - [ ] Implémentation du JavaScript

3. **Phase 3 : Fonctionnalités Avancées**
   - [ ] Système de panier
   - [ ] Gestion des prix en temps réel
   - [ ] Filtres de recherche
