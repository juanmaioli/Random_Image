<?php
$ip = $_SERVER['REMOTE_ADDR'];
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
include('config.php');
// $categoria = $_GET['id'];
$conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);
mysqli_set_charset($conn,'utf8');

if (isset($_GET['id'])) {
  // Si se proporciona, asignar su valor a $categoria
  $categoria = $_GET['id'];
} else {
  // Si no se proporciona, asignar un valor predeterminado o mostrar un mensaje de error
  $categoria = null; // Puedes asignar el valor que desees como predeterminado
  // O mostrar un mensaje de error
  // header("HTTP/1.0 400 Bad Request");
  // echo "Error: Falta el parámetro 'id'";
}

$directorio = 'new_images/';
$directorioDestino = 'img/';

if($categoria == null){
  echo "NO SE SELECCIONO CATEGORÍA";
}else{
  // Obtener todos los archivos de imagen del directorio
  $archivos = glob($directorio . "*.{jpg,jpeg}", GLOB_BRACE);

  // Recorrer los archivos
  foreach ($archivos as $archivo) {
          $infoArchivo = pathinfo($archivo);
          $nombreArchivo = $infoArchivo['filename'];
          $extensionArchivo = $infoArchivo['extension'];

          $new_name = hash('sha256', $nombreArchivo ).'.'.$extensionArchivo;
          $archivoFinal = $directorioDestino.$new_name;
          $dimensiones = getimagesize($archivo);
          if (rename($archivo, $archivoFinal)) {
            echo "El archivo $nombreArchivo se movió correctamente a $directorioDestino <br>";
          } else {
              echo "No se pudo mover el archivo $nombreArchivo <br>";
          }

          if ($dimensiones) {
            $ancho = $dimensiones[0];
            $alto = $dimensiones[1];
            $sql = "INSERT INTO img (img_width,img_height,img_filename,img_expose,img_category) VALUES($ancho,$alto,'$new_name',0,'$categoria')";
            $result = $conn->query($sql);
        } else {
            echo "No se pudo obtener las dimensiones de la imagen.";
        }
          echo "<br>";
  }

}
$conn->close();
?>
<!-- 

img_category
landscape
pets
weapons -->