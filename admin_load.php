<?php
include('config.php');

$conn = new mysqli($db_server, $db_user,$db_pass,$db_name,$db_serverport);
mysqli_set_charset($conn,'utf8');

// Obtener categorías existentes
$sql_cat = "SELECT DISTINCT img_category FROM img ORDER BY img_category ASC";
$res_cat = $conn->query($sql_cat);
$categorias_existentes = [];
if ($res_cat) {
    while ($row = $res_cat->fetch_assoc()) {
        $categorias_existentes[] = $row['img_category'];
    }
}

$directorio = 'new_images/';
$archivos = glob($directorio . "*.{jpg,jpeg}", GLOB_BRACE);
?>
<!DOCTYPE html>
<html lang="es-AR" data-bs-theme="auto">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carga de Imágenes - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: sans-serif; }
        .card { border: none; }
        .category-badge { cursor: pointer; }
    </style>
</head>
<body class="bg-body-tertiary">

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="h3 mb-4">📥 Carga de Imágenes</h1>
                    
                    <form action="load_images.php" method="GET">
                        <div class="mb-4">
                            <label for="id" class="form-label fw-bold">Categoría para la carga</label>
                            <input type="text" class="form-control form-control-lg" id="id" name="id" list="category-list" placeholder="Seleccioná o escribí una categoría..." required aria-label="Seleccione o ingrese la categoría para las imágenes">
                            <datalist id="category-list">
                                <?php foreach ($categorias_existentes as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat); ?>">
                                <?php endforeach; ?>
                            </datalist>
                            
                            <?php if (!empty($categorias_existentes)): ?>
                                <div class="mt-2">
                                    <small class="text-muted d-block mb-1">Accesos rápidos:</small>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php foreach ($categorias_existentes as $cat): ?>
                                            <span class="badge bg-secondary-subtle text-secondary-emphasis border category-badge" onclick="document.getElementById('id').value='<?php echo addslashes($cat); ?>'">
                                                <?php echo htmlspecialchars($cat); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <h2 class="h5 mb-3 d-flex justify-content-between align-items-center">
                                📁 Archivos en <code>new_images/</code>
                                <span class="badge bg-primary rounded-pill small"><?php echo count($archivos); ?></span>
                            </h2>
                            
                            <?php if (empty($archivos)): ?>
                                <div class="alert alert-info border-0 shadow-sm" role="alert">
                                    📭 No hay archivos .jpg o .jpeg pendientes de procesar.
                                </div>
                            <?php else: ?>
                                <ul class="list-group list-group-flush border rounded mb-4 overflow-auto" style="max-height: 300px;" aria-label="Lista de archivos pendientes">
                                    <?php foreach ($archivos as $archivo): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="text-truncate me-2">🖼️ <?php echo basename($archivo); ?></span>
                                            <span class="badge text-bg-light border small">
                                                <?php echo round(filesize($archivo) / 1024, 2); ?> KB
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">
                                    🚀 Cargar Imágenes
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
            
            <nav class="text-center d-flex justify-content-center gap-3" aria-label="Navegación secundaria">
                <a href="gallery.php" class="btn btn-link text-decoration-none">🖼️ Ver Galería</a>
                <a href="index.php" class="btn btn-link text-decoration-none">🎲 Imagen aleatoria</a>
            </nav>
        </div>
    </div>
</main>
<?php $conn->close(); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        document.documentElement.setAttribute('data-bs-theme', 'dark')
    }
</script>
</body>
</html>
