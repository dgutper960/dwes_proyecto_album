<?php

/*
    albumModel.php

    Modelo del  controlador album

    Definir los métodos de acceso a la base de datos
    
    - insert
    - update
    - select
    - delete
    - etc..
*/

class albumModel extends Model
{

    /*
        Extrae los detalles  de los albumes
    */
    public function get()
    {

        try {

            # comando sql
            $sql = "SELECT 
                       *
                    FROM
                        albumes
                    ORDER BY 
                        id
                    ";

            # conectamos con la base de datos

            $conexion = $this->db->connect();

            # ejecutamos mediante prepare
            $pdost = $conexion->prepare($sql);

            # establecemos  tipo fetch
            $pdost->setFetchMode(PDO::FETCH_OBJ);

            #  ejecutamos 
            $pdost->execute();

            # devuelvo objeto pdostatement
            return $pdost;
        } catch (PDOException $e) {

            include_once ('template/partials/errorDB.php');
            exit();
        }
    }

    /*
       Extrae un album por id
   */
    public function getAlbum($id)
    {
        try {
            $sql = " SELECT     
                        id,
                        titulo,
                        descripcion,
                        autor,
                        fecha,
                        lugar,
                        categoria,
                        etiquetas,
                        num_fotos,
                        num_visitas,
                        carpeta
                    FROM  
                        albumes  
                    WHERE
                        id = :id";

            $conexion = $this->db->connect();
            $pdoSt = $conexion->prepare($sql);
            $pdoSt->bindParam(":id", $id, PDO::PARAM_INT);
            $pdoSt->setFetchMode(PDO::FETCH_OBJ);
            $pdoSt->execute();
            return $pdoSt->fetch();
        } catch (PDOException $e) {
            require_once ("template/partials/errorDB.php");
            exit();
        }
    }


    /*
       Inserta un registro en la BBDD
   */
    public function create(classAlbum $album)
    {
        try {
            $sql = "INSERT INTO albumes (
                                titulo,
                                descripcion,
                                autor,
                                fecha,
                                lugar,
                                categoria,
                                etiquetas,
                                num_fotos,
                                num_visitas,
                                carpeta,
                                created_at
                            )
                            VALUES (
                                :titulo,
                                :descripcion,
                                :autor,
                                :fecha,
                                :lugar,
                                :categoria,
                                :etiquetas,
                                0,
                                0,
                                :carpeta,
                                NOW()
                            )
                    ";

            // Conexión con la BBDD
            $conexion = $this->db->connect();

            // Ejecutamos prepare
            $pdoSt = $conexion->prepare($sql);

            // Vinculamos los campos de la consulta con las propiedades del objeto
            $pdoSt->bindParam(':titulo', $album->titulo, PDO::PARAM_STR, 100);
            $pdoSt->bindParam(':descripcion', $album->descripcion, PDO::PARAM_STR);
            $pdoSt->bindParam(':autor', $album->autor, PDO::PARAM_STR);
            $pdoSt->bindParam(':fecha', $album->fecha, PDO::PARAM_STR);
            $pdoSt->bindParam(':lugar', $album->lugar, PDO::PARAM_STR);
            $pdoSt->bindParam(':categoria', $album->categoria, PDO::PARAM_STR);
            $pdoSt->bindParam(':etiquetas', $album->etiquetas, PDO::PARAM_STR);
            $pdoSt->bindParam(':carpeta', $album->carpeta, PDO::PARAM_STR);

            // Insertamos el registro
            $pdoSt->execute();

            // Creamos la carpeta con el nombre asignado por el usuario (usamos la ruta imagenes)
            mkdir('imagenes/' . $album->carpeta);
        } catch (PDOException $e) {
            include_once ('template/partials/errorDB.php');
            exit();
        }
    }

    /*
        Actualiza el cintenido de un album por id
    */
    public function update(classAlbum $album, $id, $carpetaOrig)
    {

        try {

            $sql = "UPDATE albumes
                    SET
                            titulo =        :titulo,
                            descripcion =   :descripcion,
                            autor =         :autor,
                            fecha =         :fecha,
                            lugar =         :lugar,
                            categoria =     :categoria,
                            etiquetas =     :etiquetas,
                            carpeta =       :carpeta
                    WHERE
                            id = :id
                    LIMIT 1
                    ";

            // Conexión ccon la BBDD
            $conexion = $this->db->connect();

            // Ejecutamos prepare
            $pdoSt = $conexion->prepare($sql);

            // Vinculamos los campos de la consulta con los parámetros
            $pdoSt->bindParam(':id', $id, PDO::PARAM_INT);
            $pdoSt->bindParam(':titulo', $album->titulo, PDO::PARAM_STR, 100);
            $pdoSt->bindParam(':descripcion', $album->descripcion, PDO::PARAM_STR);
            $pdoSt->bindParam(':autor', $album->autor, PDO::PARAM_STR, 50);
            $pdoSt->bindParam(':fecha', $album->fecha, PDO::PARAM_STR);
            $pdoSt->bindParam(':lugar', $album->lugar, PDO::PARAM_STR, 50);
            $pdoSt->bindParam(':categoria', $album->categoria, PDO::PARAM_STR, 50);
            $pdoSt->bindParam(':etiquetas', $album->etiquetas, PDO::PARAM_STR, 250);
            $pdoSt->bindParam(':carpeta', $album->carpeta, PDO::PARAM_STR, 50);

            // Cambiamos el nombre de la carpeta
            $rutaOrigen = "imagenes/" . $carpetaOrig;
            $rutaDest = "imagenes/" . $album->carpeta;
            rename($rutaOrigen, $rutaDest);

            // Ejecutamos la consulta
            $pdoSt->execute();
        } catch (PDOException $e) {
            include_once ('template/partials/errorDB.php');
            exit();
        }
    }

    /*
       Ordena los resultados según un criterio predefinido
   */
    public function order(int $criterio)
    {

        try {

            # comando sql
            $sql = "SELECT 
                        id,
                        titulo,
                        descripcion,
                        autor,
                        fecha,
                        categoria,
                        etiquetas,
                        num_fotos,
                        num_visitas,
                        carpeta
                    FROM
                        albumes
                    ORDER BY 
                        :criterio
                    ";

            # conectamos con la base de datos

            $conexion = $this->db->connect();

            # ejecutamos mediante prepare
            $pdostmt = $conexion->prepare($sql);

            $pdostmt->bindParam(':criterio', $criterio, PDO::PARAM_INT);

            # establecemos  tipo fetch
            $pdostmt->setFetchMode(PDO::FETCH_OBJ);

            #  ejecutamos 
            $pdostmt->execute();

            # devuelvo objeto pdostatement
            return $pdostmt;
        } catch (PDOException $e) {

            include_once ('template/partials/errorDB.php');
            exit();
        }
    }

    /*
        Filtra los resultados obtenidos según una expresión dada
    */
    public function filter($expresion)
    {
        try {
            $sql = "SELECT 
                    id,
                    titulo,
                    descripcion,
                    autor,
                    fecha,
                    categoria,
                    etiquetas,
                    num_fotos,
                    num_visitas,
                    carpeta
                FROM
                    albumes
                WHERE
                        CONCAT_WS(  ', ', 
                        id,
                        titulo,
                        descripcion,
                        autor,
                        fecha,
                        categoria,
                        etiquetas,
                        num_fotos,
                        num_visitas,
                        carpeta) 
                        like :expresion
    
                    ORDER BY 
                        albumes.id
                    ";

            # Conexión con la BBDD
            $conexion = $this->db->connect();

            $pdost = $conexion->prepare($sql);

            $pdost->bindValue(':expresion', '%' . $expresion . '%', PDO::PARAM_STR);
            $pdost->setFetchMode(PDO::FETCH_OBJ);
            $pdost->execute();
            return $pdost;
        } catch (PDOException $e) {

            include_once ('template/partials/errorDB.php');
            exit();
        }
    }

    /*
        Obtiene la carpeta de un album por id
    */
    public function obtenerCarpeta($id_album)
    {
        try {
            $sql = "SELECT 
                        carpeta
                    FROM 
                        albumes
                     WHERE
                        id = :id
                    ";

            # Conexión con la BBDD
            $conexion = $this->db->connect();

            $pdoSt = $conexion->prepare($sql);

            $pdoSt->bindParam(':id', $id_album, PDO::PARAM_INT);
            $pdoSt->setFetchMode(PDO::FETCH_OBJ);
            $pdoSt->execute();

            return $pdoSt->fetch();
        } catch (PDOException $e) {
            include_once ('template/partials/errorDB.php');
            exit();
        }
    }


    /*
        Elimina un registro de la tabla album
    */
    public function delete($id)
    {
        try {

            $sql = "DELETE FROM albumes WHERE id = :id limit 1";
            $conexion = $this->db->connect();
            $pdost = $conexion->prepare($sql);
            $pdost->bindParam(':id', $id, PDO::PARAM_INT);
            $pdost->execute();
        } catch (PDOException $e) {

            include_once ('template/partials/errorDB.php');
            exit();
        }
    }

    public function validateFecha($fecha)
    {
        if (date('Y-m-d', strtotime($fecha)) == $fecha) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Incrementa el número de visitas de un album
     *  Entrada -> id_album
     */
    public function sumNumVisitas($id)
    {
        try {
            $sql = "UPDATE albumes SET num_visitas = num_visitas + 1 WHERE id = :id";
            $conexion = $this->db->connect();
            $pdost = $conexion->prepare($sql);
            $pdost->bindParam(':id', $id, PDO::PARAM_INT);
            $pdost->execute();
        } catch (PDOException $e) {
            include_once ('template/partials/errorDB.php');
            exit();
        }
    }

    /**
     * Edita el num de fotos de un album 
     *  Entrada id_album, num fotos
     */
    public function updateNumFotos($id_album, $num_fotos)
    {
        try {
            $sql = "UPDATE albumes SET num_fotos = :num_fotos WHERE id = :id_album";
            $conexion = $this->db->connect();
            $pdost = $conexion->prepare($sql);
            $pdost->bindParam(':num_fotos', $num_fotos, PDO::PARAM_INT);
            $pdost->bindParam(':id_album', $id_album, PDO::PARAM_INT);
            $pdost->execute();
        } catch (PDOException $e) {
            include_once ('template/partials/errorDB.php');
            exit();
        }
    }

    /**
     * Sube ficheros a una carpeta dada
     */
    public function subirFicheros($ficheros, $carpeta)
    {

        $num = count($ficheros['tmp_name']);

        # genero array de error de fichero
        $FileUploadErrors = array(
            0 => 'No hay error, fichero subido con éxito.',
            1 => 'El fichero subido excede la directiva upload_max_filesize de php.ini.',
            2 => 'El fichero subido excede la directiva MAX_FILE_SIZE especificada en el formulario HTML.',
            3 => 'El fichero fue sólo parcialmente subido.',
            4 => 'No se subió ningún fichero.',
            6 => 'Falta la carpeta temporal.',
            7 => 'No se pudo escribir el fichero en el disco.',
            8 => 'Una extensión de PHP detuvo la subida de ficheros.',
        );

        $error = null;

        for ($i = 0; $i <= $num - 1 && is_null($error); $i++) {
            if ($ficheros['error'][$i] != UPLOAD_ERR_OK) {
                $error = $FileUploadErrors[$ficheros['error'][$i]];
            } else {
                $tamMaximo = 4194304;
                if ($ficheros['size'][$i] > $tamMaximo) {

                    $error = "Archivo excede tamaño maximo 4MB";
                }
                $info = new SplFileInfo($ficheros['name'][$i]);
                $tipos_permitidos = ['JPG', 'JPEG', 'GIF', 'PNG'];
                if (!in_array(strtoupper($info->getExtension()), $tipos_permitidos)) {
                    $error = "Archivo no permitido. Seleccione una imagen.";
                }
            }
        }

        if (is_null($error)) {
            for ($i = 0; $i <= $num - 1; $i++) {
                if (is_uploaded_file($ficheros['tmp_name'][$i])) {
                    move_uploaded_file($ficheros['tmp_name'][$i], "imagenes/" . $carpeta . "/" . $ficheros['name'][$i]);
                }
            }
            $_SESSION['mensaje'] = "Los archivos se han subido correctamente";
        } else {
            $_SESSION['error'] = $error;
        }
    }
}
