<?php
include('config.php');

$conn = new mysqli($db_server, $db_user, $db_pass, $db_name, $db_serverport);
mysqli_set_charset($conn, 'utf8');

// Obtener categorías para el filtro
$sql_cat = "SELECT DISTINCT img_category FROM img ORDER BY img_category ASC";
$res_cat = $conn->query($sql_cat);
$categorias = [];
if ($res_cat) {
    while ($row = $res_cat->fetch_assoc()) {
        $categorias[] = $row['img_category'];
    }
}

// Filtrado por categoría
$categoria_actual = $_GET['cat'] ?? null;
$where = $categoria_actual ? "WHERE img_category = '" . $conn->real_escape_string($categoria_actual) . "'" : "";

// Configuración de paginación
$por_pagina = 24;
$pagina_actual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($pagina_actual < 1) $pagina_actual = 1;
$offset = ($pagina_actual - 1) * $por_pagina;

// Obtener total para paginación
$sql_total = "SELECT COUNT(*) as total FROM img $where";
$res_total = $conn->query($sql_total);
$total_filas = $res_total->fetch_assoc()['total'];
$total_paginas = ceil($total_filas / $por_pagina);

// Consulta de imágenes con LIMIT y OFFSET
$sql_img = "SELECT * FROM img $where ORDER BY img_id DESC LIMIT $por_pagina OFFSET $offset";
$res_img = $conn->query($sql_img);
?>
<!DOCTYPE html>
<html lang="es-AR" data-bs-theme="auto">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería de Imágenes - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: sans-serif; }
        .img-container {
            height: 200px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bs-tertiary-bg);
            border-radius: 8px 8px 0 0;
        }
        .img-container img {
            min-width: 100%;
            min-height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .card:hover img {
            transform: scale(1.05);
        }
        .card { border: none; transition: transform 0.2s; }
        .card:hover { transform: translateY(-5px); }
        .pagination .page-link { border: none; margin: 0 2px; border-radius: 8px; }
    </style>
</head>
<body class="bg-body-tertiary">

<nav class="navbar navbar-expand-lg bg-body shadow-sm sticky-top mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="gallery.php">🖼️ Galería</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo !$categoria_actual ? 'active fw-bold' : ''; ?>" href="gallery.php">Todas</a>
                </li>
                <?php foreach ($categorias as $cat): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $categoria_actual === $cat ? 'active fw-bold' : ''; ?>" 
                           href="gallery.php?cat=<?php echo urlencode($cat); ?>">
                            <?php echo htmlspecialchars($cat); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="d-flex gap-2">
                <a href="admin_load.php" class="btn btn-primary btn-sm">📥 Subir Imágenes</a>
                <a href="index.php" class="btn btn-outline-secondary btn-sm" target="_blank">🎲 Random</a>
            </div>
        </div>
    </div>
</nav>

<main class="container mb-5">
    <header class="mb-4 d-flex justify-content-between align-items-end">
        <div>
            <h1 class="h2 mb-0">🖼️ <?php echo $categoria_actual ? 'Categoría: ' . htmlspecialchars($categoria_actual) : 'Todas las imágenes'; ?></h1>
            <p class="text-muted mb-0">Total: <?php echo $total_filas; ?> imágenes encontradas.</p>
        </div>
        <?php if ($total_paginas > 1): ?>
            <nav aria-label="Paginación superior">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?php echo $pagina_actual <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link shadow-sm" href="?cat=<?php echo urlencode($categoria_actual); ?>&page=<?php echo $pagina_actual - 1; ?>">Anterior</a>
                    </li>
                    <li class="page-item disabled"><span class="page-link text-dark fw-bold"><?php echo $pagina_actual; ?> de <?php echo $total_paginas; ?></span></li>
                    <li class="page-item <?php echo $pagina_actual >= $total_paginas ? 'disabled' : ''; ?>">
                        <a class="page-link shadow-sm" href="?cat=<?php echo urlencode($categoria_actual); ?>&page=<?php echo $pagina_actual + 1; ?>">Siguiente</a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </header>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-5">
        <?php if ($res_img && $res_img->num_rows > 0): ?>
            <?php while ($img = $res_img->fetch_assoc()): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="img-container">
                            <img src="img/<?php echo $img['img_filename']; ?>" 
                                 alt="Imagen de <?php echo htmlspecialchars($img['img_category']); ?>" 
                                 loading="lazy">
                        </div>
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-primary-subtle text-primary-emphasis border">
                                    <?php echo htmlspecialchars($img['img_category']); ?>
                                </span>
                                <small class="text-muted">ID: <?php echo $img['img_id']; ?></small>
                            </div>
                            <ul class="list-unstyled small mb-0 text-muted">
                                <li>📏 <?php echo $img['img_width']; ?>x<?php echo $img['img_height']; ?> px</li>
                                <li>👁️ Visto: <?php echo $img['img_expose']; ?> veces</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent border-top-0 p-3 pt-0">
                            <a href="img/<?php echo $img['img_filename']; ?>" target="_blank" class="btn btn-light btn-sm w-100 border">
                                🔍 Ver original
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="display-1 text-muted mb-4">🏜️</div>
                <h3>No hay imágenes aquí</h3>
                <p class="text-muted">Carga algunas imágenes en la categoría "<?php echo htmlspecialchars($categoria_actual); ?>" para verlas aquí.</p>
                <a href="admin_load.php" class="btn btn-primary mt-3">Ir a cargar imágenes</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Paginación Inferior -->
    <?php if ($total_paginas > 1): ?>
        <nav aria-label="Navegación de páginas" class="d-flex justify-content-center mt-5">
            <ul class="pagination shadow-sm">
                <li class="page-item <?php echo $pagina_actual <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?cat=<?php echo urlencode($categoria_actual); ?>&page=<?php echo $pagina_actual - 1; ?>" aria-label="Anterior">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                
                <?php
                $rango = 2;
                for ($i = 1; $i <= $total_paginas; $i++): 
                    if ($i == 1 || $i == $total_paginas || ($i >= $pagina_actual - $rango && $i <= $pagina_actual + $rango)):
                ?>
                    <li class="page-item <?php echo $pagina_actual === $i ? 'active' : ''; ?>">
                        <a class="page-link" href="?cat=<?php echo urlencode($categoria_actual); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php 
                    elseif ($i == $pagina_actual - $rango - 1 || $i == $pagina_actual + $rango + 1):
                ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php 
                    endif;
                endfor; 
                ?>

                <li class="page-item <?php echo $pagina_actual >= $total_paginas ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?cat=<?php echo urlencode($categoria_actual); ?>&page=<?php echo $pagina_actual + 1; ?>" aria-label="Siguiente">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
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
