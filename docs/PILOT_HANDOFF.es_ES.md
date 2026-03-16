# Guest Upload Vault - Guia de handoff piloto (es_ES)

## Alcance

- Pagina de subida para invitados mediante shortcode: `[guest_upload_vault]`
- Enlace de invitados protegido por token (`guv_token`)
- Generacion local de codigo QR en admin de WordPress
- Almacenamiento cifrado en `wp-content/uploads/guest-upload-vault/`
- Descarga y gestion de medios solo para admins

## Configuracion

1. Copiar la carpeta `guest-upload-vault/` en `wp-content/plugins/`.
2. Activar el plugin en WordPress.
3. Crear una pagina de subida y anadir el shortcode `[guest_upload_vault]`.
4. Abrir **Guest Upload Vault** en el admin.
5. Configurar la URL de la pagina de subida.
6. Guardar y compartir el enlace protegido o el codigo QR.

## Flujo de invitados (token + QR)

1. El admin configura la URL de la pagina de subida.
2. El plugin genera la URL protegida con `guv_token`.
3. Los invitados abren el enlace o escanean el QR.
4. Los invitados suben fotos/videos desde camara, galeria o archivos.

Importante:
- Regenerar el token invalida enlaces antiguos y QR impresos.
- Sin token valido, la pagina de subida no es utilizable.

## Copia de seguridad y restauracion (critico)

Siempre hacer backup/restauracion conjunta de:
- base de datos de WordPress (opciones del plugin y clave de cifrado)
- carpeta `wp-content/uploads/guest-upload-vault/` (blobs cifrados + metadatos)

Si se restaura solo una parte, los archivos pueden quedar sin poder descifrarse.

## Limpieza al desinstalar

Opcion: **Cleanup On Uninstall**
- Desactivada (por defecto): mantiene los archivos tras desinstalar.
- Activada: elimina permanentemente medios/metadatos en `uploads/guest-upload-vault/`.

Esta opcion no aplica a la desactivacion normal del plugin.

## Limitaciones conocidas (hosting compartido)

- Los limites de servidor/PHP pueden reducir el tamano maximo efectivo de subida.
- La descarga admin descifra el archivo en memoria PHP (sin streaming).
- Los archivos legacy se marcan en los diagnosticos del admin.
