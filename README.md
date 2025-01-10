
# Proyecto Librería - Despliegue para Desarrollo

Este proyecto es una API desarrollada en Laravel que gestiona recursos relacionados con autores, géneros y libros. A continuación, se describen **todos los pasos necesarios** para configurar y desplegar el entorno de desarrollo utilizando Docker y MinIO.

---

## 0. Clonar el Repositorio

Puedes clonar este repositorio de dos maneras:

- **Por SSH**  
`
git clone git@github.com:Faber16/Libreria.git
`

- **Por HTTPS**  
`
git clone https://github.com/Faber16/Libreria.git
`

Una vez clonado, ingresa a la carpeta del proyecto:

`
cd Libreria
`

---

## **Pasos para el Despliegue en Entorno de Desarrollo**

### 1. Levantar las Imágenes y Construir las que se Deban Construir

Ejecuta el siguiente comando para construir las imágenes y levantar los contenedores necesarios para el proyecto:

`
docker compose up -d --build
`

---

### 2. Crear el Archivo `.env`

1. Crea un archivo `.env` en la raíz del proyecto copiando el contenido del archivo `.env.example`:

`
cp .env.example .env
`

---

### 3. Configurar el Entorno de Laravel

Dentro del contenedor `backend`, ejecuta los siguientes comandos en el orden indicado:

1. Limpia la caché de configuración:
`
docker compose exec backend php artisan config:clear
`

2. Ejecuta las migraciones para crear las tablas en la base de datos:
`
docker compose exec backend php artisan migrate
`

3. Siembra la base de datos con datos iniciales:
`
docker compose exec backend php artisan db:seed
`

---

### 4. Configurar MinIO para Almacenamiento S3

1. Abre tu navegador y accede a la interfaz de MinIO:
   `
   http://localhost:9001
    `

2. Inicia sesión utilizando las siguientes credenciales:
   - **Usuario**: `minioadmin`
   - **Contraseña**: `minioadmin`

3. Dentro de la interfaz de MinIO, crea los siguientes buckets:
   - **Bucket para desarrollo/producción**: `data`
   - **Bucket para pruebas (testing)**: `data-testing`

---

## **Acceso al Proyecto**

Después de realizar todos los pasos, el proyecto estará disponible en las siguientes ubicaciones:

- **APP + API**: `http://localhost:8000`
- **MinIO (Interfaz de Almacenamiento S3)**: `http://localhost:9001`
- **Documentación API**: `http://localhost:8000/api/documentation#/`

---

## **Notas Importantes**

1. **Docker Compose**:
   - El comando `docker compose up -d --build` es esencial para levantar las imágenes y construir las que lo necesiten.

2. **Archivo `.env`**:
   - Asegúrate de que el archivo `.env` esté correctamente configurado para que el entorno funcione según lo esperado.

3. **MinIO**:
   - Los buckets `data` y `data-testing` deben ser creados manualmente en la interfaz de MinIO antes de ejecutar operaciones relacionadas con almacenamiento S3.

4. **Recomendación entorno**:
   - El entorno recomendado para ejecutar este proyecto es dentro de una maquina `Linux/Ubuntu` que tenga instalado `Docker` y `Docker compose`.
---

