# Wedding Gallery - Guide de handoff pilote (fr_FR)

## Portee

- Upload invite via shortcode: `[wedding_gallery_upload]`
- Lien invite protege par token (`wg_token`)
- QR code genere localement dans l'admin WordPress
- Stockage chiffre dans `wp-content/uploads/wedding-gallery/`
- Telechargement et gestion des medias reservees aux admins

## Mise en place

1. Copier le dossier `wedding-gallery/` dans `wp-content/plugins/`.
2. Activer le plugin dans WordPress.
3. Creer une page d'upload et y ajouter le shortcode `[wedding_gallery_upload]`.
4. Ouvrir **Wedding Gallery** dans l'admin.
5. Definir l'URL de la page d'upload.
6. Enregistrer puis partager le lien protege ou le QR code.

## Flux invite (token + QR)

1. L'admin configure l'URL de la page d'upload.
2. Le plugin genere l'URL protegee avec `wg_token`.
3. Les invites ouvrent le lien ou scannent le QR code.
4. Les invites uploadent des photos/videos depuis camera, galerie ou fichiers.

Important:
- Regenerer le token invalide les anciens liens et QR codes imprimes.
- Sans token valide, la page d'upload n'est pas utilisable.

## Sauvegarde et restauration (critique)

Toujours sauvegarder/restaurer ensemble:
- base de donnees WordPress (options plugin et cle de chiffrement)
- dossier `wp-content/uploads/wedding-gallery/` (blobs chiffres + metadonnees)

Si un seul element est restaure, les fichiers peuvent devenir indechiffrables.

## Suppression a la desinstallation

Option: **Cleanup On Uninstall**
- Desactive (par defaut): conserve les fichiers apres desinstallation.
- Active: supprime definitivement les medias/metadonnees dans `uploads/wedding-gallery/`.

Cette option ne s'applique pas a la simple desactivation du plugin.

## Limites connues (hebergement partage)

- Les limites serveur/PHP peuvent reduire la taille maximale effective d'upload.
- Le telechargement admin decrypte le fichier en memoire PHP (pas de streaming).
- Les anciens fichiers legacy sont signales dans les diagnostics admin.
