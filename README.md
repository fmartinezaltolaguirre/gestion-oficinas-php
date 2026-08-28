# Gestión de Oficinas (PHP)

Aplicación ligera en PHP para gestionar oficinas mediante listas de SharePoint a través de Microsoft Graph. Sirve como reemplazo inicial de una base Microsoft Access para listar y crear oficinas desde una interfaz web sencilla.

Características
- Listado de oficinas desde una lista de SharePoint
- Creación de nuevas oficinas (campos: Title, Codigo)
- Integración con Microsoft Graph usando credenciales de aplicación (client_credentials)
- CSRF protection básica y escape de salida para XSS
- Manejo simple de errores y logs por error_log()

Arquitectura (resumen)
- public/index.php: punto de entrada
- src/Http/Router.php: router minimalista
- src/Controller/*Controller.php: controladores HTTP
- src/Repository/*Repository.php: lógica de acceso a datos (GraphClient)
- src/SharePoint/*: cliente Graph, provider de tokens y localizador de sitio
- views/: plantillas PHP simples

Requisitos
- PHP >= 8.1 (se desarrolló con PHP 8.5 en local)
- Composer
- Cuenta Entra ID (Azure AD) con una aplicación registrada y permisos para Microsoft Graph
- Lista de SharePoint creada y su ID disponible

Instalación

1. Clonar el repositorio
   git clone https://github.com/fmartinezaltolaguirre/gestion-oficinas-php.git
2. Copiar ejemplo de entorno y configurar variables
   cp .env.example .env
3. Instalar dependencias
   composer install

Variables de entorno (en .env)
- APP_ENV: entorno (local, production)
- APP_DEBUG: true|false (muestra mensajes de error en desarrollo)
- APP_URL: URL base de la app

Entra / Microsoft Graph
- ENTRA_TENANT_ID: Tenant ID de Entra (Azure AD)
- ENTRA_CLIENT_ID: Client ID de la app registrada
- ENTRA_CLIENT_SECRET: Client secret (no subir al repo)
- GRAPH_SCOPE: scope para token (por defecto: https://graph.microsoft.com/.default)

SharePoint
- SHAREPOINT_HOST: host del tenant (ej. empresa.sharepoint.com)
- SHAREPOINT_SITE_PATH: ruta del sitio (ej. /sites/GestionOficinas)
- SP_LIST_OFICINAS_ID: ID de la lista de Oficinas (obligatorio para crear/listar)

Uso (desarrollo)
- Levantar servidor integrado: php -S 127.0.0.1:8000 -t public
- Página principal: /
- Listado de oficinas: /oficinas
- Nueva oficina (formulario): /oficinas/nueva

Notas de seguridad y buenas prácticas
- No guardar secretos en el repositorio. Usa variables de entorno o un vault.
- CSRF: la app incluye token CSRF en el formulario de creación; mantener sesiones activas.
- XSS: las vistas usan htmlspecialchars() para escapar contenido mostrado.
- Tokens: TokenProvider cachea token en memoria y respeta expiración. Para alta disponibilidad, use cache compartido.

Logs y diagnóstico
- Se utiliza error_log() para mensajes de diagnóstico. En producción, reemplace con logger estructurado (monolog/u otro).

Contribuir
- Abrir issues o pull requests en GitHub. Seguir el estilo y no incluir secretos.

Licencia
- MIT

Contacto
- Repositorio: https://github.com/fmartinezaltolaguirre/gestion-oficinas-php

