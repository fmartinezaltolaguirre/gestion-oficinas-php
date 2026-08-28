# Arquitectura

Navegador -> Router -> Controller -> Repository -> Microsoft Graph -> SharePoint.

## Capas

- `public`: punto de entrada y recursos publicos.
- `src/Controller`: controladores HTTP.
- `src/Repository`: acceso a listas.
- `src/SharePoint`: autenticacion y Microsoft Graph.
- `views`: plantillas PHP.
