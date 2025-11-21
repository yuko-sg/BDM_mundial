# Instrucciones de Configuración - BDM Mundial

## Pasos para configurar la base de datos:

### 1. Asegúrate de que XAMPP esté corriendo
- Inicia Apache
- Inicia MySQL

### 2. Crear la base de datos y tablas
1. Abre phpMyAdmin en tu navegador: `http://localhost/phpmyadmin`
2. Ve a la pestaña "SQL"
3. Copia y pega el contenido del archivo `base de datos y tablas.sql`
4. Haz clic en "Continuar" para ejecutar el script

### 3. Insertar roles predeterminados
1. En phpMyAdmin, asegúrate de tener seleccionada la base de datos `bdm_mundial`
2. Ve a la pestaña "SQL"
3. Copia y pega el contenido del archivo `models/insert_roles.sql`
4. Haz clic en "Continuar" para ejecutar el script

Esto creará dos roles:
- ID 1: Administrador
- ID 2: Usuario (rol predeterminado para nuevos registros)

### 4. Verificar la configuración de conexión
Revisa el archivo `models/db_connection.php` y asegúrate de que las credenciales sean correctas:
- Host: localhost
- Usuario: root
- Contraseña: (vacía por defecto en XAMPP)
- Base de datos: bdm_mundial

### 5. (Opcional) Insertar datos de prueba
Para probar el dashboard con posts de ejemplo:
1. En phpMyAdmin, asegúrate de tener seleccionada la base de datos `bdm_mundial`
2. Ve a la pestaña "SQL"
3. Copia y pega el contenido del archivo `sample_data.sql`
4. Haz clic en "Continuar" para ejecutar el script

Esto creará:
- 23 mundiales (desde 1930 hasta 2026)
- 6 categorías (Goles, Jugadas, Noticias, etc.)
- 2 estados (aprobado, por aprobar)
- 8 posts aprobados de ejemplo

### 6. Probar la aplicación
1. Abre tu navegador
2. Ve a: `http://localhost/BDM_mundial/views/index.php`
3. Haz clic en "Regístrate"
4. Completa el formulario (todos los campos son obligatorios, incluyendo la foto)
5. Después de registrarte, serás redirigido a la página de inicio de sesión
6. Inicia sesión con tus credenciales
7. Serás redirigido al Dashboard

## Características implementadas:

### ✅ Dashboard
- Sidebar izquierda con navegación
- Información del usuario logueado
- Selector de Mundial por año
- Selector de Categoría
- Botón de filtrado
- Feed de posts aprobados
- Cada post muestra: fecha, mundial, categoría, contenido e imagen (si tiene)
- Botones de interacción (Me gusta, Comentar)
- Responsive design

### ✅ Registro de usuarios
- Validación de campos obligatorios
- Validación de formato de email
- Verificación de email duplicado
- Validación de tipo de archivo de imagen
- Límite de tamaño de imagen (5MB)
- Hash de contraseñas con `password_hash()`
- Almacenamiento de foto de perfil en base de datos

### ✅ Inicio de sesión
- Verificación de credenciales
- Uso de `password_verify()` para verificar contraseñas
- Creación de sesión de usuario
- Redirección a dashboard

### ✅ Mensajes de error y éxito
- Feedback visual para el usuario
- Mensajes específicos para cada tipo de error

## Notas importantes:

1. **Foto de perfil obligatoria**: El campo foto_perfil en la base de datos es `NOT NULL`, por lo tanto es obligatorio subir una imagen al registrarse.

2. **Seguridad de contraseñas**: Las contraseñas se almacenan hasheadas usando `password_hash()` con el algoritmo por defecto (bcrypt).

3. **Tamaño de contraseña en BD**: La columna `contrasena` está definida como `VARCHAR(30)` en el SQL, pero los hashes de bcrypt generan strings de ~60 caracteres. **Recomendación**: Cambiar a `VARCHAR(255)` para evitar problemas.

## SQL recomendado para corregir el tamaño de la contraseña:

```sql
ALTER TABLE usuarios MODIFY COLUMN contrasena VARCHAR(255) NOT NULL;
```

Ejecuta este comando en phpMyAdmin para permitir que se almacenen los hashes completos de las contraseñas.

Ejercicio práctico
Contexto: Librería “LibroMundo”
Tablas:
•	Inventory (inventory_id PK, book_id FK, store_id FK, last_update)
•	Author (author_id PK, first_name, last_name, last_update, photo, dateofbirth)
•	Book (book_id PK, title, description, publish_year, language_id FK, original_language_id FK, price DECIMAL(6,2), pages INT, rating, last_update)
•	book_newprices (book_id FK, new_price DECIMAL(6,2))
•	Payment (payment_id PK, customer_id FK, staff_id FK, order_id, amount, payment_date, last_update)
•	Customer (customer_id PK, store_id FK, first_name, last_name, email, address_id FK, active, create_date, last_update)
•	Staff (staff_id PK, first_name, last_name, email, address_id FK, store_id FK, active, username, password, create_date, last_update)
•	Store (store_id PK, name, manager_staff_id FK, address_id FK, last_update)
•	Language (language_id PK, name, last_update)
Instrucciones: Resuelvan los 4 puntos.
1)	Actualizar precios de libros solo cuando el nuevo precio es mayor
2)	Eliminar del inventario libros cuyo lenguaje sea distinto al lenguaje original o no tenga lenguaje (lenguaje null).
3)	Función que recibe tienda y regresa el nombre completo del manager; si no existe la sucursal, regresa mensaje
4)	Reporte: total de libros por sucursal y por idioma, ordenado por nombre de sucursal (A-Z)
