<?php

class Album extends Controller
{

    function __construct()
    {

        parent::__construct();
    }

    // Muestra el CRUD de albumes
    function render()
    {

        // Iniciar o continuar sesión 
        session_start();

        // Comprobar autenticación
        if (!isset($_SESSION['id'])) {
            $_SESSION['notify'] = "El usuario debe autenticarse";
            header("location:" . URL . "login");
        } else if ((!in_array($_SESSION['id_rol'], $GLOBALS['album']['main']))) {
            $_SESSION['mensaje'] = "Acción restringida. Usuario sin privilegios";
            header('location:' . URL . 'index');
        } else {

            if (isset($_SESSION['mensaje'])) {
                $this->view->mensaje = $_SESSION['mensaje'];
                unset($_SESSION['mensaje']);
            }

            // Cargamos propiedad en la vista -> titulo
            $this->view->title = "Home - Panel Control Albumes";

            // Cargamos propiedad en la vista -> albumes
            $this->view->albumes = $this->model->get();

            $this->view->render('album/main/index');
        }
    }

    // Muestra los detalles y el contenido de su carpeta
    public function show($param = [])
    {

        // Iniciar o continuar sesión
        session_start();

        // Comprobar autenticación
        if (!isset($_SESSION['id'])) {
            $_SESSION['mensaje'] = "El usuario debe autenticarse";
            header("location:" . URL . "login");
        } else if ((!in_array($_SESSION['id_rol'], $GLOBALS['album']['show']))) {
            $_SESSION['mensaje'] = "Acción restringida. Usuario sin privilegios";
            header('location:' . URL . 'album');
        } else {

            $id = $param[0];

            // Incrementamos el campo num_visitas
            $this->model->sumNumVisitas($id);

            $this->view->title = "Formulario Álbum Mostar";
            $this->view->album = $this->model->getAlbum($id);
            $this->view->render("album/show/index");
        }
    }

    // Controla acciones en el formulario Nuevo
    function new()
    {

        // Iniciar o continuar sesión
        session_start();

        // Comprobar autenticación
        if (!isset($_SESSION['id'])) {
            $_SESSION['notify'] = "El usuario debe autenticarse";

            header("location:" . URL . "login");
        } else if ((!in_array($_SESSION['id_rol'], $GLOBALS['album']['new']))) {
            $_SESSION['mensaje'] = "Acción restringida. Usuario sin privilegios";
            header('location:' . URL . 'album');
        } else {

            // Instancia de album
            $this->view->album = new classAlbum();

            // Si hay errores -> venimos de un formulario no válido
            if (isset($_SESSION['error'])) {

                // Cargamos el error para el mensaje
                $this->view->error = $_SESSION['error'];

                // Autorelleno del formulario
                $this->view->album = unserialize($_SESSION['album']);

                // Cargamos los errores como propiedad de la vista
                $this->view->errores = $_SESSION['errores'];

                // Limpiamos las variables de sesión
                unset($_SESSION['error']);
                unset($_SESSION['album']);
                unset($_SESSION['errores']);
            }

            // Titulo del formulario
            $this->view->title = "Nuevo Album - Gestión Album";

            // Mostramos la vista nuevo album
            $this->view->render('album/new/index');
        }
    }

    // Control de datos del formulario Nuevo; Sanitiza y valida
    // Si todo va bién -> carga los datos en create();
    // Si hay errores -> carga el formulario
    function create($param = [])
    {
        // Iniciar o continuar sesión
        session_start();

        if (!isset($_SESSION['id'])) {
            $_SESSION['mensaje'] = "El usuario debe autenticarse";
            header("location:" . URL . "login");
        } else if ((!in_array($_SESSION['id_rol'], $GLOBALS['album']['new']))) {
            $_SESSION['mensaje'] = "Acción restringida. Usuario sin privilegios";
            header('location:' . URL . 'album');
        } else {
            // Saneamos los datos del formulario
            $titulo = filter_var($_POST['titulo'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $descripcion = filter_var($_POST['descripcion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $autor = filter_var($_POST['autor'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $fecha = filter_var($_POST['fecha'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $lugar = filter_var($_POST['lugar'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $categoria = filter_var($_POST['categoria'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $etiquetas = filter_var($_POST['etiquetas'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $carpeta = filter_var($_POST['carpeta'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

            // Instancamos album y cargamos los datos
            $album = new classAlbum(
                null,
                $titulo,
                $descripcion,
                $autor,
                $fecha,
                $lugar,
                $categoria,
                $etiquetas,
                null,
                null,
                $carpeta
            );

            // Validamos
            $errores = [];

            // Título
            //-> Campo obligatorio
            //-> MAX caracteres menor a 100
            if (empty($titulo)) {
                $errores['titulo'] = 'El campo es obligatorio';
            } else if (mb_strlen($titulo) > 100) {
                $errores['titulo'] = 'Máximo de caracteres permitidos 99';
            }

            // Descripción
            //-> Campo obligatorio
            if (empty($descripcion)) {
                $errores['descripcion'] = 'El campo es obligatorio';
            }

            // Autor
            //-> Campo obligatorio
            if (empty($autor)) {
                $errores['autor'] = 'El campo es obligatorio';
            }

            // Fecha
            //-> Campo obligatorio
            //-> Fecha Valida
            if (empty($fecha)) {
                $errores['fecha'] = 'El campo Fecha es obligatorio';
            } else if (!$this->model->validateFecha($fecha)) {
                $errores['fecha'] = 'La fecha no es válida';
            }

            // Lugar
            //-> Campo obligatorio
            if (empty($lugar)) {
                $errores['lugar'] = 'El campo es obligatorio';
            }

            // Categoría
            //-> Campo obligatorio
            if (empty($categoria)) {
                $errores['categoria'] = 'El campo es obligatorio';
            }

            // Carpeta
            //-> Campo obligatorio
            //-> Sin espacios en blanco
            if (empty($carpeta)) {
                $errores['carpeta'] = 'El campo es obligatorio';
            } else if (strpos($carpeta, ' ') !== false) {
                $errores['carpeta'] = 'No se permiten espacios en blanco';
            }

            // Si hay errores -> venimos de formulario no válido
            if (!empty($errores)) {
                // Cargamos la variables de sesión 
                $_SESSION['album'] = serialize($album);
                $_SESSION['error'] = 'Formulario no ha sido validado';
                $_SESSION['errores'] = $errores;
                // Redirigimos al formulario
                header('location:' . URL . 'album/new');
            } else {
                // Si no hay errores -> llamada a create()
                $this->model->create($album);

                // Debemos crear una carpeta en la ruta "imagenes" para almacenar las imágenes del album
                $carpeta = $album->carpeta;
                $rutaCarpeta = "imagenes/$carpeta";
                if (!file_exists($rutaCarpeta)) {
                    mkdir($rutaCarpeta, 0777, true);
                }
                $_SESSION['mensaje'] = "Álbum creado correctamente";
                header('location:' . URL . 'album');
            }
        }
    }

    // Controla acciones del formulario Editar
    function edit($param = [])
    {

        // Iniciar o continuar sesión
        session_start();

        if (!isset($_SESSION['id'])) {
            $_SESSION['mensaje'] = "El usuario debe autenticarse";
            header("location:" . URL . "login");

        } else if ((!in_array($_SESSION['id_rol'], $GLOBALS['album']['edit']))) {
            $_SESSION['mensaje'] = "Acción restringida. Usuario sin privilegios";
            header('location:' . URL . 'album');

        } else {

            $id = $param[0];

            // Asignamos propiedades a la vista
            $this->view->id = $id;
            $this->view->title = "Editar - Panel de control Albumes";
            $this->view->album = $this->model->getAlbum($id); // cargamos el album por id

            // Si hay errores -> venimos de un formulario no válido
            if (isset($_SESSION['error'])) {

                // Cargamos el error para el mensaje
                $this->view->error = $_SESSION['error'];

                // Autorelleno del formulario
                $this->view->album = unserialize($_SESSION['album']);

                // Cargamos los errores como propiedad de la vista
                $this->view->errores = $_SESSION['errores'];

                // Limpiamos las variables de sesión
                unset($_SESSION['error']);
                unset($_SESSION['album']);
                unset($_SESSION['errores']);
            }

            // Si no hay errores, cargamos la vista Editar
            $this->view->render('album/edit/index');
        }
    }

    // Control de datos del formulario Editar; Sanitizar y validar.
    // Si todo va bién -> carga los datos en update();
    // Si hay errores, -> carga el formulario Editar
    public function update($param = [])
    {

        // Iniciar o continuar sesión
        session_start();

        if (!isset($_SESSION['id'])) {
            $_SESSION['mensaje'] = "El usuario debe autenticarse";

            header("location:" . URL . "login");
        } else if ((!in_array($_SESSION['id_rol'], $GLOBALS['album']['edit']))) {
            $_SESSION['mensaje'] = "Acción restringida. Usuario sin privilegios";
            header('location:' . URL . 'album');
        } else {

            // Saneamos los  datos del formulario
            $titulo = filter_var($_POST['titulo'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS, 100);
            $descripcion = filter_var($_POST['descripcion'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $autor = filter_var($_POST['autor'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $fecha = filter_var($_POST['fecha'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $lugar = filter_var($_POST['lugar'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $categoria = filter_var($_POST['categoria'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $etiquetas = filter_var($_POST['etiquetas'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $num_fotos = filter_var($_POST['num_fotos'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $num_visitas = filter_var($_POST['num_visitas'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
            $carpeta = filter_var($_POST['carpeta'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

            // Instanciamos un album y cargamos los datos
            $album = new classAlbum(
                null,
                $titulo,
                $descripcion,
                $autor,
                $fecha,
                $lugar,
                $categoria,
                $etiquetas,
                $num_fotos,
                $num_visitas,
                $carpeta
            );

            // Capturamos el id de entrada
            $id = $param[0];

            // Cargamos el album original de la BBDD 
            $album_orig = $this->model->getAlbum($id);

            // Obtenemos la ruta de la carpeta original
            $rutaCarpetaOriginal = "imagenes/" . $album_orig->carpeta;

            // Obtenemos la ruta de la carpeta nueva
            $nuevaRutaCarpeta = "imagenes/" . $carpeta;

            // Validamos
            $errores = [];

            // Título 
            //-> Campo obligatorio 
            //-> MAX caracteres menor a 100
            if (empty($titulo)) {
                $errores['titulo'] = 'El campo Título es obligatorio';
            } else if (mb_strlen($titulo) > 100) {
                $errores['titulo'] = 'Máximo de caracteres permitidos 99';
            }

            // Descripción
            //-> Campo obligatorio 
            if (empty($descripcion)) {
                $errores['descripcion'] = 'El campo es obligatorio';
            }

            // Autor
            //-> Campo obligatorio 
            if (empty($autor)) {
                $errores['autor'] = 'El campo es obligatorio';
            }

            // Fecha
            //-> Campo obligatorio 
            //-> Validar fecha
            if (empty($fecha)) {
                $errores['fecha'] = 'El campo es obligatorio';
            } else if (!$this->model->validateFecha($fecha)) {
                $errores['fecha'] = 'La fecha no es válida, formato no correcto';
            }

            // Lugar
            //-> Campo obligatorio 
            if (empty($lugar)) {
                $errores['lugar'] = 'El campo es obligatorio';
            }

            // Categoría
            //-> Campo obligatorio 
            if (empty($categoria)) {
                $errores['categoria'] = 'El campo es obligatorio';
            }

            // Carpeta
            //-> Campo obligatorio
            //-> Espacios en blanco no permitidos en el nombre
            if (empty($carpeta)) {
                $errores['carpeta'] = 'El campo es obligatorio';
            } else if (strpos($carpeta, ' ') !== false) {
                $errores['carpeta'] = 'No se permiten espacios en blanco';
            }

            // Si hay errores -> venimos de formulario no válido
            if (!empty($errores)) {
                // Cargamos la variables de sesión 
                $_SESSION['album'] = serialize($album);
                $_SESSION['error'] = 'Formulario no ha sido validado';
                $_SESSION['errores'] = $errores;
                // Redirigimos al formulario
                header('location:' . URL . 'album/edit/' . $id);
            } else {
                // Renombramos la carpeta
                if (rename($rutaCarpetaOriginal, $nuevaRutaCarpeta)) {
                    // Actualizamos el valor de la carpeta en el objeto del álbum
                    $album->carpeta = $carpeta;
                } else {
                    // Si no se puede renombrar la carpeta, agregar un mensaje de error
                    $errores['carpeta'] = 'Error al renombrar la carpeta';
                }

                # Actualizar registro
                $this->model->update($album, $id, $rutaCarpetaOriginal);

                # Mensaje
                $_SESSION['mensaje'] = "Álbum actualizado correctamente";

                # Redirigimos al main de álbumes
                header('location:' . URL . 'album');
            }
        }
    }

    // Ordena los registros mostrado en main según un criterio preseleccionado
    public function order($param = [])
    {

        // Iniciar o continuar sesión
        session_start();

        if (!isset($_SESSION['id'])) {
            $_SESSION['mensaje'] = "El usuario debe autenticarse";

            header("location:" . URL . "login");
        } else if ((!in_array($_SESSION['id_rol'], $GLOBALS['album']['order']))) {
            $_SESSION['mensaje'] = "Acción restringida. Usuario sin privilegios";
            header('location:' . URL . 'album');
        } else {

            // Obtenemos el criterio enviado al argumento
            $criterio = $param[0];

            // Cargamos propiedad en la vista
            $this->view->title = "Ordenar - Panel Control Album";

            // order() retorna consulta SQL con cláusula ORDER BY = $criterio (int)
            $this->view->albumes = $this->model->order($criterio);

            // Cargamos el main 
            $this->view->render('album/main/index');
        }
    }

    // Filtra los resultados en el CRUD según una expresión dada
    public function filter($param = [])
    {

        // Iniciar o continuar sesión
        session_start();

        if (!isset($_SESSION['id'])) {
            $_SESSION['mensaje'] = "El usuario debe autenticarse";
            header("location:" . URL . "login");
        } else if ((!in_array($_SESSION['id_rol'], $GLOBALS['album']['filter']))) {
            $_SESSION['mensaje'] = "Acción restringida. Usuario sin privilegios";
            header('location:' . URL . 'album');
        } else {

            // Cargamos la expresión
            $expresion = $_GET['expresion'];

            // Cargamos propiedad en la vista
            $this->view->title = "Buscar - Panel de Albumes";

            // Llamada a filter() con la expresión como argumento
            $this->view->albumes = $this->model->filter($expresion);

            // Cargamos la vista main
            $this->view->render('album/main/index');
        }
    }

    // Sube ficheros al directiorio del album
    // Actualiza el número de fotos del album
    public function add($param = [])
    {

        // Iniciar o continuar sesión
        session_start();

        // Comprobar autenticación
        if (!isset($_SESSION['id'])) {
            $_SESSION['notify'] = "El usuario debe autenticarse";

            header("location:" . URL . "login");
        } else if ((!in_array($_SESSION['id_rol'], $GLOBALS['album']['add']))) {
            $_SESSION['mensaje'] = "Acción restringida. Usuario sin privilegios";
            header('location:' . URL . 'album');
        } else {

            // Si hay errores -> venimos de un formulario no válido
            if (isset($_SESSION['error'])) {

                // Cargamos el error para el mensaje
                $this->view->error = $_SESSION['error'];

                // Cargamos los errores como propiedad de la vista
                $this->view->errores = $_SESSION['errores'];

                // Limpiamos las variables de sesión
                unset($_SESSION['error']);
                unset($_SESSION['errores']);
            }

            // Instanciamos objeto de la clase Album con id de entrada
            $album = $this->model->getAlbum($param[0]);

            $this->model->subirFicheros($_FILES['archivos'], $album->carpeta);
            // Contamos todo el contenido de la carpeta
            $num_fotos = count(glob("imagenes/" . $album->carpeta . "/*"));
            // Actualizar el campo número de fotos
            $this->model->updateNumFotos($album->id, $num_fotos);

            header("location:" . URL . "album");
        }
    }

    // Elimina un album de la tabla
    public function delete($param = [])
    {

        // Iniciar o continuar sesión
        session_start();

        if (!isset($_SESSION['id'])) {
            $_SESSION['mensaje'] = "El usuario debe autenticarse";

            header("location:" . URL . "login");
        } else if ((!in_array($_SESSION['id_rol'], $GLOBALS['album']['delete']))) {
            $_SESSION['mensaje'] = "Acción restringida. Usuario sin privilegios";
            header('location:' . URL . 'album');
        } else {

            // Cargamos el id del album
            $id = $param[0];

            // Al borrar un albúm debemos eliminar su directirio y contenido
            // Obtenemos la ruta de la carpeta
            $album = $this->model->getAlbum($id);
            $carpeta = $album->carpeta;
            $rutaCarpeta = "imagenes/$carpeta";

            // Si la ruta es un directorio, llamamos al metodo de esta clase
            if (is_dir($rutaCarpeta)) {
                $this->EliminarCarpeta($rutaCarpeta);
            }

            // Una vez borrado el contenido, eliminamos el album
            $this->model->delete($id);

            $_SESSION['mensaje'] = 'album eliminado correctamente';

            // Volvemos al main
            header('location:' . URL . 'album');
        }
    }


    //Elimina el contenido de una carpeta y de todos sus hijos
    private function EliminarCarpeta($carpeta)
    {
        // Verificamos si la carpeta no existe, devuelve verdadero (ya ha sido eliminada)
        if (!file_exists($carpeta))
            return true;
    
        // Verificamos si la ruta no corresponde a una carpeta -> elimanos el archivo directamente
        if (!is_dir($carpeta))
            return unlink($carpeta);
    
        // Itera sobre los elementos de la carpeta
        foreach (scandir($carpeta) as $item) {
            // Ignora los elementos '.' y '..' que representan el directorio actual y el directorio padre
            if ($item == '.' || $item == '..')
                continue;
    
            // Eliminamos los elementos de la carpeta de forma recursiva
            if (!$this->EliminarCarpeta($carpeta . DIRECTORY_SEPARATOR . $item))
                return false;
        }
    
        // Eliminamos la propia carpeta
        return rmdir($carpeta);
    }
    
}
