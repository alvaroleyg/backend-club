# <ins>Prueba Técnica Backend</ins>

Aplicación CRUD desarrollada con Symfony, Doctrine y MySQL, dockerizada con contenedores Docker.

---

## Índice

- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Uso](#uso)
- [Testing con Postman](#testing-con-postman)
- [Estructura Docker](#estructura-docker)
- [Problemas comunes](#problemas-comunes)
- [Tecnologías](#tecnologías)
- [Licencia](#licencia)

---


## 📋 Requisitos

- Docker y Docker Compose instalados
- PHP 8.1 o superior
- Composer
- Postman (para pruebas de API)


## 🚀 Instalación

### 1. **Clonar repositorio:**

Primero clona el repositorio en tu sistema local:

```bash
   git clone https://github.com/alvaroleyg/backend-club.git
   cd backend-club
```

### 2. **Configurar las variables de entorno:**

Copia el archivo `.env` a `.env.local`. Si tu terminal es bash, usa:

```bash
    cp .env .env.local
```

Si es Powershell, usa:

```powershell
    Copy-Item .env .env.local
```
Si fuese necesario, ajusta la variable de `MYSQL_ROOT_PASSWORD` con tu contraseña real del usuario `root` de MySQL.


### 3. **Construir contenedores Docker:**

El proyecto incluye un archivo `docker-compose.yml` para facilitar la configuración del entorno. Para construir los volúmenes y levantar los contenedores, ejecuta:

```bash
    docker-compose up -d --build
```

Esto iniciará los servicios necesarios (Symfony, MySQL, Mailer y PHPMyAdmin).

### 4. **Instalar dependencias PHP:**

Una vez levantados los contenedores, instala las dependencias de Symfony ejecutando:

```bash
    docker exec -it backend-club-app-1 bash
    composer install
    exit
```

### 5. **Base de datos:**

Crea la base datos y genera las migraciones para crear la estructura.

```bash
    docker exec -it backend-club-app-1 bash
    php bin/console doctrine:migrations:migrate
    exit
```

Para trabajar con los datos de ejemplo, deberás importar el dump SQL proporcionado. Ejecuta esto si usas una terminal bash:

```bash
docker exec -i backend-club-db-1 mysql -u manager1 -ppassword1 backend_club < backup.sql
```
O esto otro si usas una terminal powershell:

```powershell
Get-Content .\backup.sql | docker exec -i backend-club-db-1 mysql -u manager1 -ppassword1 backend_club
```

Para manejar la base de datos, puedes usar `phpMyAdmin` en: http://localhost:8080

### 5. **Iniciar servidor Symfony:**

```bash
    docker exec -it backend-club-app-1 bash
    php bin/console server:start
    exit
```

## 🛠 Uso

El servidor Symfony estará disponible en:
http://localhost:8000

### Endpoints principales:
La URL base desde la cual realizar las peticiones será: http://localhost:8000/api

| Método | Endpoint |   Descripción   |
| -------- | -------- | -------- |
| POST   | /clubs   |  Crear un club |
| POST   | /players   |  Crear un jugador |
| POST   | /coaches   |  Crear un entrenador |
| POST   | /clubs/{clubId}/players/{playerId}  |  Añadir un jugador a un club |
| POST   | /clubs/{clubId}/players/{coachId}   |  Añadir un entrenador a un club |
| DELETE   | /clubs/{clubId}/players/{playerId}   |  Eliminar un jugador de un club |
| DELETE   | /clubs/{clubId}/players/{coachId}   |  Eliminar un entrenador de un club |
| PATCH   | /clubs/{clubId}/budget  |  Añadir / restar presupuesto a un cub |
| GET   | /players  |  Obtener todos los jugadores |
| GET   | /clubs/{clubId}/players?page={page}&filter[name]={name}   |  Obtener todos los jugadors de un club (con paginación y filtro) |
| DELETE   | /playes/{playerId}   |  Eliminar un jugador |
| DELETE   | /coaches/{coachId}  |  Eliminar un entrenador |


## 🧪 Testing

### 1. Pruebas con Postman

Para probar las peticiones HTTP, tienes a tu disposición una colección de Postman. Tan sólo abre Postman y haz lo siguiente:

1. Importar la colección desde `/postman/Club-Management-API.postman_collection.json`

2. Configura el environment de variables de Postman:

         base_url: http://localhost:8000/api

    (o utiliza directamente esa URL base para todas las peticiones)

3. Ejecuta la request que deseas. En los casos que sea necesario, el `Body` vendrá ya rellenado con un ejemplo del tipo de JSON que se debe introducir.

### 2. Tests unitarios

Para probar los tests unitarios, primero debes haber importado la base de datos con los datos de prueba. Después ejecuta:

```bash
docker exec -it backend-club-app-1 bash
./bin/phpunit
exit
```

## ✉️ Mailer

Al añadir o eliminar a un miembro de un club, automáticamente se envía un mail a una dirección (ficticia).

Puedes simular este servicio de la siguiente forma:
1. Abre http://localhost:8000/send-mail en tu navegador para enviar un correo.
2. Ve a http://localhost:8025 para ver el correo capturado por Mailpit.


## 🐳 Estructura Docker

Contenedores incluidos:

`app`: Servicio PHP  + Symfony

`db`: MySQL

`phpmyadmin` (opcional): Interfaz web para manejar la base de datos

`mailer`: Mailpit (SMTP + Web UI)


## 🔧 Troubleshooting

Problemas comunes:

- **Puertos en uso:** Verificar que 8000 (Symfony) y 3306 (MySQL) estén libres

- **Errores de DB:** Revisar credenciales en archivo .env y reiniciar contenedores

- **Datos no cargados:** Verificar importación del dump y permisos de usuario MySQL


## 📚 Tecnologías

- PHP 8.4.3

- Symfony CLI version 5.10.6

- Doctrine ORM

- MySQL 8.0

- Docker + Docker Compose

- Postman (Testing)

- Mailpit (Mailer)


## 📄 Licencia

Este proyecto está bajo la licencia MIT.