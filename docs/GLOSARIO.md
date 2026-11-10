# Glosario del código

Los nombres que creamos en el proyecto están en español. Esta tabla muestra dónde está cada pieza y cómo se llamaba antes.

## Clases y archivos

| Qué es | Nombre actual | Antes | Archivo |
|---|---|---|---|
| Modelo del usuario | `Usuario` | `User` | `app/Models/Usuario.php` |
| Modelo del contacto | `Contacto` | `Contact` | `app/Models/Contacto.php` |
| Controlador de login, registro, logout y perfil | `ControladorAutenticacion` | `AuthController` | `app/Http/Controllers/Api/V1/ControladorAutenticacion.php` |
| Controlador del CRUD de contactos | `ControladorContactos` | `ContactController` | `app/Http/Controllers/Api/V1/ControladorContactos.php` |
| Controlador de /health | `ControladorSalud` | `HealthController` | `app/Http/Controllers/Api/V1/ControladorSalud.php` |
| Validación del login | `SolicitudInicioSesion` | `LoginRequest` | `app/Http/Requests/Autenticacion/` |
| Validación del registro | `SolicitudRegistro` | `RegisterRequest` | `app/Http/Requests/Autenticacion/` |
| Validación del perfil | `SolicitudActualizarPerfil` | `UpdateProfileRequest` | `app/Http/Requests/Autenticacion/` |
| Mensajes de error de usuario | `MensajesUsuario` | `UserMessages` | `app/Http/Requests/Autenticacion/` |
| Validación para crear contacto | `SolicitudCrearContacto` | `StoreContactRequest` | `app/Http/Requests/Contactos/` |
| Validación para actualizar contacto | `SolicitudActualizarContacto` | `UpdateContactRequest` | `app/Http/Requests/Contactos/` |
| Mensajes de error de contacto | `MensajesContacto` | `ContactMessages` | `app/Http/Requests/Contactos/` |
| JSON del usuario | `RecursoUsuario` | `UserResource` | `app/Http/Resources/RecursoUsuario.php` |
| JSON del contacto | `RecursoContacto` | `ContactResource` | `app/Http/Resources/RecursoContacto.php` |
| Permisos sobre contactos | `PoliticaContacto` | `ContactPolicy` | `app/Policies/PoliticaContacto.php` |
| Usuarios falsos para pruebas | `FabricaUsuario` | `UserFactory` | `database/factories/FabricaUsuario.php` |
| Contactos falsos para pruebas | `FabricaContacto` | `ContactFactory` | `database/factories/FabricaContacto.php` |
| Datos de prueba | `SembradorContactos` | `ContactSeeder` | `database/seeders/SembradorContactos.php` |

## Métodos

| Clase | Método actual | Antes | Ruta |
|---|---|---|---|
| ControladorAutenticacion | `registrar()` | `register()` | POST /auth/register |
| ControladorAutenticacion | `iniciarSesion()` | `login()` | POST /auth/login |
| ControladorAutenticacion | `perfil()` | `me()` | GET /auth/me |
| ControladorAutenticacion | `actualizarPerfil()` | `updateProfile()` | PUT /auth/me |
| ControladorAutenticacion | `cerrarSesion()` | `logout()` | POST /auth/logout |
| ControladorContactos | `listar()` | `index()` | GET /contacts |
| ControladorContactos | `crear()` | `store()` | POST /contacts |
| ControladorContactos | `ver()` | `show()` | GET /contacts/{contacto} |
| ControladorContactos | `actualizar()` | `update()` | PUT /contacts/{contacto} |
| ControladorContactos | `eliminar()` | `destroy()` | DELETE /contacts/{contacto} |
| PoliticaContacto | `ver()`, `actualizar()`, `eliminar()` | `view()`, `update()`, `delete()` | |
| PoliticaContacto | `esDueno()` | `owns()` | |
| Usuario | `contactos()` (relación) | `contacts()` | |
| Contacto | `usuario()` (relación) | `user()` | |
| MensajesUsuario / MensajesContacto | `todos()` | `all()` | |

Otros nombres:

| Qué es | Nombre actual | Antes |
|---|---|---|
| Variable de la petición | `$solicitud` | `$request` |
| Variables de usuario, contacto y datos | `$usuario`, `$contacto`, `$contactos`, `$datos` | `$user`, `$contact`, `$contacts`, `$data` |
| Límite de intentos | `throttle:inicio-sesion` | `throttle:login` |
| Nombres de ruta | `auth.iniciar_sesion`, `contactos.ver`, etc. | `auth.login`, `contacts.show`, etc. |
| Pruebas | `AutenticacionTest`, `ContactosTest`, `PerfilTest`, `SaludTest` | `AuthTest`, `ContactTest`, `ProfileTest`, `HealthTest` |
| Funciones de apoyo en pruebas | `datosLogin()`, `datosRegistro()`, `datosContacto()` | `loginData()`, `registerData()`, `contactData()` |

## Lo que sigue en inglés y por qué

- **Lo que pide el documento del proyecto:** las tablas `users` y `contacts` y sus columnas (`first_name`, `phone_number`, `address`, `deleted_at`...).
- **Las URL de la API** (`/auth/login`, `/contacts`...) y **los campos del JSON** (`token`, `message`, `data`...). Son el contrato con quien usa la API; la colección de Insomnia sigue funcionando igual.
- **Lo que Laravel llama por su nombre.** Si se cambian, deja de funcionar:
  - en los modelos: `$fillable`, `$hidden`, `$table`, `casts()`, `newFactory()`;
  - en las validaciones: `authorize()`, `rules()`, `messages()`;
  - en los recursos: `toArray()`;
  - en las fábricas: `definition()`;
  - en el sembrador: `run()`;
  - en el controlador de salud: `__invoke()`;
  - las clases base `Controller`, `Model`, `FormRequest`, `DatabaseSeeder` y `AppServiceProvider`;
  - los métodos de Laravel como `create()`, `update()`, `delete()`, `where()`, `paginate()` y `createToken()`.
- **Los archivos de prueba** terminan en `Test.php` porque así los encuentra Pest.
