# Gestión de Álbumes - Proyecto 2DAW

Este proyecto es el trabajo realizado para el módulo de Desarrollo en Entorno Servidor del curso 2DAW. La aplicación web está desarrollada completamente en PHP sin uso de frameworks, utilizando estilos de Bootstrap.

## Descripción

La aplicación permite la gestión completa de álbumes de fotos, incluyendo la creación, edición, eliminación y visualización de álbumes, así como la gestión de imágenes dentro de estos álbumes. Además, incluye opciones de ordenación y búsqueda, y está diseñada para manejar diferentes perfiles de usuario con distintos privilegios.

## Características

- **PHP:** El lenguaje principal utilizado para el desarrollo del backend.
- **Bootstrap:** Utilizado para el diseño y los estilos del frontend, proporcionando una interfaz moderna y responsive.
- **Gestión de Álbumes:** Crear, editar, eliminar y mostrar álbumes.
- **Gestión de Imágenes:** Agregar imágenes a los álbumes con validación.
- **Perfiles y Privilegios:** Gestión de diferentes niveles de acceso para usuarios.
- **Ordenar y Buscar:** Funcionalidades de ordenación y búsqueda para los álbumes.

## Funcionalidades

### Lista de Álbumes
- Mostrar en una tabla los detalles de todos los álbumes: id, título, lugar, categoría, etiquetas, num_fotos, num_visitas.
- Botones de Acción:
  - Eliminar (álbum)
  - Editar (detalles de un álbum)
  - Mostrar (detalles de un álbum junto con todas las fotografías)
  - Agregar (permitirá agregar imágenes a un álbum)
- Menú de Álbumes:
  - Álbumes (mostrará la lista de álbumes creados)
  - Crear (permitirá crear nuevo álbum)
  - Ordenar (permitirá ordenar por distintos criterios álbum)
  - Buscar (permitirá filtrar los álbumes)

### Crear Nuevo Álbum
- Añadir nuevo álbum con los siguientes criterios de validación:
  - Título (obligatorio, menor que 100 caracteres)
  - Descripción (obligatoria)
  - Autor (obligatorio)
  - Fecha (obligatoria)
  - Lugar (obligatorio)
  - Categoría (obligatoria)
  - Etiquetas (no obligatorio)
  - Carpeta (obligatoria, sin espacios)
- Crear una carpeta vacía dentro del directorio `images`.

### Editar Álbum
- Modificar todos los detalles del álbum con validación.

### Eliminar Álbum
- Borrar los detalles del álbum y la carpeta asignada junto con todas las fotografías.
- Solicitar confirmación antes de eliminar.

### Mostrar Álbum
- Visualizador de un álbum que muestra los detalles y todas las imágenes.
- Utilizar plantillas de Bootstrap 5.3 para una vista atractiva.

### Agregar Imágenes
- Permitir añadir nuevas imágenes a un álbum con validación:
  - Validar en cliente mediante el parámetro `accept` de HTML (JPG, GIF, PNG)
  - Validar en cliente tamaño máximo de 5MB
  - Validar en servidor tipo de imagen y tamaño máximo de 5MB
  - Cancelar subida si una sola imagen no cumple con la validación

### Ordenar y Buscar
- Funcionalidades de ordenación y búsqueda de álbumes.

### Perfiles y Privilegios
- **Administrador:** Todas las opciones
- **Editor:** Todas las opciones menos eliminar
- **Registrado:** Sólo podrá consultar (lista, visualizar álbum, ordenar y buscar)
