# Guest Upload Vault - Guida handoff pilota (it_IT)

## Ambito

- Pagina upload ospiti via shortcode: `[guest_upload_vault]`
- Link ospiti protetto da token (`guv_token`)
- Generazione QR locale nell'admin WordPress
- Archiviazione cifrata in `wp-content/uploads/guest-upload-vault/`
- Download e gestione media riservati agli admin

## Configurazione

1. Copiare la cartella `guest-upload-vault/` in `wp-content/plugins/`.
2. Attivare il plugin in WordPress.
3. Creare una pagina upload e inserire lo shortcode `[guest_upload_vault]`.
4. Aprire **Guest Upload Vault** nell'admin.
5. Impostare l'URL della pagina upload.
6. Salvare e condividere link protetto o QR code.

## Flusso ospiti (token + QR)

1. L'admin configura l'URL della pagina upload.
2. Il plugin genera l'URL protetto con `guv_token`.
3. Gli ospiti aprono il link o scansionano il QR code.
4. Gli ospiti caricano foto/video da fotocamera, galleria o file.

Importante:
- Rigenerare il token invalida link vecchi e QR code stampati.
- Senza token valido, la pagina upload non e utilizzabile.

## Backup e ripristino (critico)

Eseguire sempre backup/ripristino congiunto di:
- database WordPress (opzioni plugin e chiave di cifratura)
- cartella `wp-content/uploads/guest-upload-vault/` (blob cifrati + metadati)

Se viene ripristinata solo una parte, i file possono diventare non decifrabili.

## Pulizia in disinstallazione

Opzione: **Cleanup On Uninstall**
- Disattiva (default): mantiene i file dopo la disinstallazione.
- Attiva: elimina in modo permanente media/metadati in `uploads/guest-upload-vault/`.

Questa opzione non si applica alla semplice disattivazione del plugin.

## Limiti noti (hosting condiviso)

- I limiti server/PHP possono ridurre la dimensione massima effettiva di upload.
- Il download admin decifra il file in memoria PHP (nessuno streaming).
- I file legacy vengono segnalati nei diagnostici admin.
