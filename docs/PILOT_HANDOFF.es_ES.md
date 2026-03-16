# Wedding Gallery - Guia de handoff piloto (es_ES)

## Alcance

- Pagina de subida para invitados mediante shortcode: `[wedding_gallery_upload]`
- Enlace de invitados protegido por token (`wg_token`)
- Generacion local de codigo QR en admin de WordPress
- Almacenamiento cifrado en `wp-content/uploads/wedding-gallery/`
- Descarga y gestion de medios solo para admins

## Configuracion

1. Copiar la carpeta `wedding-gallery/` en `wp-content/plugins/`.
2. Activar el plugin en WordPress.
3. Crear una pagina de subida y anadir el shortcode `[wedding_gallery_upload]`.
4. Abrir **Wedding Gallery** en el admin.
5. Configurar la URL de la pagina de subida.
6. Guardar y compartir el enlace protegido o el codigo QR.

## Flujo de invitados (token + QR)

1. El admin configura la URL de la pagina de subida.
2. El plugin genera la URL protegida con `wg_token`.
3. Los invitados abren el enlace o escanean el QR.
4. Los invitados suben fotos/videos desde camara, galeria o archivos.

Importante:
- Regenerar el token invalida enlaces antiguos y QR impresos.
- Sin token valido, la pagina de subida no es utilizable.

## Copia de seguridad y restauracion (critico)

Siempre hacer backup/restauracion conjunta de:
- base de datos de WordPress (opciones del plugin y clave de cifrado)
- carpeta `wp-content/uploads/wedding-gallery/` (blobs cifrados + metadatos)

Si se restaura solo una parte, los archivos pueden quedar sin poder descifrarse.

## Limpieza al desinstalar

Opcion: **Cleanup On Uninstall**
- Desactivada (por defecto): mantiene los archivos tras desinstalar.
- Activada: elimina permanentemente medios/metadatos en `uploads/wedding-gallery/`.

Esta opcion no aplica a la desactivacion normal del plugin.

## Limitaciones conocidas (hosting compartido)

- Los limites de servidor/PHP pueden reducir el tamano maximo efectivo de subida.
- La descarga admin descifra el archivo en memoria PHP (sin streaming).
- Los archivos legacy se marcan en los diagnosticos del admin.
