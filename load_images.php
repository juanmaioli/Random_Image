<?php
$ip = $_SERVER['REMOTE_ADDR'];
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
include('config.php');
$conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);
mysqli_set_charset($conn,'utf8');

if (isset($_GET['id'])) {
  $categoria = $_GET['id'];
} else {
  $categoria = null;
}

$directorio = 'new_images/';
$directorioDestino = 'img/';
?>
<!DOCTYPE html>
<html lang="es-AR" data-bs-theme="auto">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesando Imágenes - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary">
<main class="container py-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h3 mb-4">⚙️ Procesando Categoría: <?php echo htmlspecialchars($categoria ?? 'Ninguna'); ?></h1>
            <div class="results">
<?php
if($categoria == null){
  echo '<div class="alert alert-danger">⚠️ NO SE SELECCIONO CATEGORÍA</div>';
}else{
  $archivos = glob($directorio . "*.{jpg,jpeg}", GLOB_BRACE);

  if (empty($archivos)) {
      echo '<div class="alert alert-warning">ℹ️ No se encontraron archivos para procesar.</div>';
  }

  foreach ($archivos as $archivo) {
          $infoArchivo = pathinfo($archivo);
          $nombreArchivo = $infoArchivo['filename'];
          $extensionArchivo = $infoArchivo['extension'];

          $new_name = hash('sha256', $nombreArchivo ).'.'.$extensionArchivo;
          $archivoFinal = $directorioDestino.$new_name;
          $dimensiones = getimagesize($archivo);
          
          echo '<div class="mb-2 p-2 border-bottom">';
          if (rename($archivo, $archivoFinal)) {
            echo "✅ El archivo <strong>$nombreArchivo</strong> se movió correctamente.<br>";
          } else {
              echo "❌ No se pudo mover el archivo <strong>$nombreArchivo</strong>.<br>";
          }

          if ($dimensiones) {
            $ancho = $dimensiones[0];
            $alto = $dimensiones[1];
            $sql = "INSERT INTO img (img_width,img_height,img_filename,img_expose,img_category) VALUES($ancho,$alto,'$new_name',0,'$categoria')";
            if ($conn->query($sql)) {
                echo "💾 Registro guardado en BD ($ancho x $alto).";
            } else {
                echo "⚠️ Error al guardar en BD: " . $conn->error;
            }
        } else {
            echo "⚠️ No se pudo obtener las dimensiones de la imagen.";
        }
          echo "</div>";
  }

}
$conn->close();
?>
            </div>
            <div class="mt-4">
                <a href="admin_load.php" class="btn btn-secondary">🔙 Volver al panel</a>
            </div>
        </div>
    </div>
</main>
<script>
    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.setAttribute('data-bs-theme', 'dark')
    }
</script>
</body>
</html>
<!-- 

img_category
landscape
pets
weapons -->