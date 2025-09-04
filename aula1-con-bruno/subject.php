<?php require_once __DIR__.'/functions.php'; require_login();
$slug = $_GET['id'] ?? ''; 
$modulo = $_GET['modulo'] ?? 1; // Obtener el módulo desde la URL
$subject = subject_by_slug($slug);
if (!$subject) { 
    // Redirigir al módulo correspondiente
    $redirect = $modulo == 2 ? 'modulo2.php' : 'modulo1.php';
    header('Location: ' . $redirect . '?error=Materia%20no%20encontrada'); 
    exit; 
}

// Crear directorio de la materia si no existe (con el módulo correcto)
$dir = uploads_path($slug, $modulo);
if (!file_exists($dir)) {
    if (!mkdir($dir, 0777, true)) {
        die('Error al crear el directorio de la materia');
    }
}

// Crear archivo meta.json si no existe
$metaPath = $dir . '/meta.json';
if (!file_exists($metaPath)) {
    if (!file_put_contents($metaPath, '[]')) {
        die('Error al crear el archivo meta.json');
    }
}

$meta = load_json($metaPath);

// handle upload
$notice = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action']==='upload') {
    if (is_alumno()) {
        $error = 'No tienes permiso para subir archivos.';
    } else {
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $error = 'Error al subir el archivo. Código: ' . $_FILES['file']['error'];
        } else {
            $max = is_profesor() ? 60*1024*1024 : 512*1024*1024; // 60MB para profesores, 512MB para admin
            if ($_FILES['file']['size'] > $max) {
                $error = 'El archivo excede el límite permitido (' . human_filesize($max) . ').';
            } else {
                $name = basename($_FILES['file']['name']);
                $safe = preg_replace('/[^A-Za-z0-9._-]/','_', $name);
                $target = $dir . '/' . $safe;
                
                // Verificar si el archivo ya existe
                if (file_exists($target)) {
                    $error = 'Ya existe un archivo con ese nombre.';
                } elseif (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
                    $meta[] = [
                        "file"=>$safe,
                        "uploader"=>$_SESSION['user']['username'],
                        "role"=>$_SESSION['user']['role'],
                        "time"=>date('c'),
                        "size"=>$_FILES['file']['size']
                    ];
                    save_json($metaPath, $meta);
                    $notice = 'Archivo subido correctamente.';
                } else {
                    $error = 'No se pudo guardar el archivo en el servidor. Verifica los permisos.';
                }
            }
        }
    }
}

// handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action']==='delete') {
    $file = $_POST['file'] ?? '';
    $allowed = false;
    foreach ($meta as $i=>$m) {
        if ($m['file'] === $file) {
            if (is_admin() || (is_profesor() && $m['uploader'] === $_SESSION['user']['username'])) {
                $allowed = true;
                $path = $dir . '/' . $file;
                if (file_exists($path)) {
                    if (unlink($path)) {
                        array_splice($meta, $i, 1);
                        save_json($metaPath, $meta);
                        $notice = 'Archivo eliminado.';
                    } else {
                        $error = 'No se pudo eliminar el archivo.';
                    }
                } else {
                    $error = 'El archivo no existe.';
                }
            }
            break;
        }
    }
    if (!$allowed) $error = 'No tienes permiso para eliminar este archivo.';
}

$files = array_values(array_diff(scandir($dir), ['.', '..', 'meta.json']));
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($subject['name']) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    :root {
      --primary: #1a56db;
      --secondary: #0e2b5c;
      --accent: #ff3a00;
      --light: #f8fafc;
      --dark: #1e293b;
    }
    
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0b0f1a 100%);
      min-height: 100vh;
      color: #f1f5f9;
    }
    
    .glass-nav {
      background: rgba(0, 0, 0, 0.3);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .glass-card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 16px;
    }
    
    .file-card {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      transition: all 0.3s ease;
    }
    
    .file-card:hover {
      background: rgba(255, 255, 255, 0.1);
      border-color: rgba(255, 255, 255, 0.2);
      transform: translateY(-2px);
    }
    
    .btn-primary {
      background: var(--primary);
      transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
      background: var(--secondary);
      transform: translateY(-2px);
    }
    
    .btn-outline {
      background: transparent;
      border: 1px solid var(--primary);
      color: var(--primary);
      transition: all 0.3s ease;
    }
    
    .btn-outline:hover {
      background: var(--primary);
      color: white;
    }
    
    .btn-danger {
      background: #dc3545;
      transition: all 0.3s ease;
    }
    
    .btn-danger:hover {
      background: #bd2130;
      transform: translateY(-2px);
    }
    
    .alert-error {
      background: rgba(220, 53, 69, 0.2);
      color: #f8d7da;
      border: 1px solid rgba(220, 53, 69, 0.3);
    }
    
    .alert-info {
      background: rgba(13, 110, 253, 0.2);
      color: #cfe2ff;
      border: 1px solid rgba(13, 110, 253, 0.3);
    }
    
    .form-input {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
      transition: all 0.3s ease;
    }
    
    .form-input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(26, 86, 219, 0.2);
    }
    
    /* Modal Styles */
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.7);
      backdrop-filter: blur(5px);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
    }
    
    .modal-overlay.active {
      opacity: 1;
      visibility: visible;
    }
    
    .modal-container {
      background: rgba(30, 41, 59, 0.95);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 16px;
      width: 90%;
      max-width: 500px;
      padding: 2rem;
      transform: translateY(20px);
      transition: transform 0.3s ease;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }
    
    .modal-overlay.active .modal-container {
      transform: translateY(0);
    }
    
    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .modal-close {
      background: transparent;
      border: none;
      color: rgba(255, 255, 255, 0.7);
      font-size: 1.5rem;
      cursor: pointer;
      transition: color 0.3s ease;
    }
    
    .modal-close:hover {
      color: white;
    }
    
    .file-upload-area {
      border: 2px dashed rgba(255, 255, 255, 0.2);
      border-radius: 12px;
      padding: 2rem;
      text-align: center;
      transition: all 0.3s ease;
      margin-bottom: 1.5rem;
      position: relative;
    }
    
    .file-upload-area:hover, .file-upload-area.dragover {
      border-color: var(--primary);
      background: rgba(26, 86, 219, 0.1);
    }
    
    .file-upload-icon {
      font-size: 3rem;
      color: rgba(255, 255, 255, 0.5);
      margin-bottom: 1rem;
    }
    
    .file-input {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      opacity: 0;
      cursor: pointer;
    }
    
    .file-info {
      margin-top: 1rem;
      padding: 0.75rem;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 8px;
      text-align: left;
      display: none;
    }
  </style>
</head>
<body class="min-h-screen flex flex-col">
  <!-- Navbar -->
  <nav class="glass-nav py-4 px-6 flex justify-between items-center sticky top-0 z-10">
    <div>
      <a href="<?= $modulo == 2 ? 'modulo2.php' : 'modulo1.php' ?>" class="btn-outline px-3 py-1 rounded-lg text-sm">
        <i class="fas fa-arrow-left mr-1"></i> Atrás
      </a>
    </div>
    <div class="flex items-center space-x-4">
      <span class="bg-gray-800 px-3 py-1 rounded-full text-sm">
        <?= htmlspecialchars($_SESSION["user"]["name"]) ?> (<?= htmlspecialchars($_SESSION["user"]["role"]) ?>)
      </span>
      <a href="logout.php" class="btn-outline px-3 py-1 rounded-lg text-sm">
        <i class="fas fa-sign-out-alt mr-1"></i> Salir
      </a>
    </div>
  </nav>

  <main class="flex-1 container mx-auto px-4 py-8">
    <!-- Header Materia -->
    <div class="glass-card p-6 mb-6 flex items-center space-x-6">
      <div class="text-4xl p-4 rounded-xl bg-blue-500/20"><?= $subject['icon'] ?></div>
      <div class="flex-1">
        <h1 class="text-2xl font-bold mb-2"><?= htmlspecialchars($subject['name']) ?></h1>
        <p class="text-gray-300">Módulo <?= $modulo ?> - Aquí encontrarás el material de estudio, guías y recursos de la materia.</p>
        <div class="mt-3">
          <span class="bg-blue-500/20 text-blue-300 px-3 py-1 rounded-full text-sm">
            <i class="fas fa-file mr-1"></i> <?= count($files) ?> archivos disponibles
          </span>
        </div>
      </div>
    </div>

    <?php if ($notice): ?>
      <div class="alert-info p-4 rounded-lg mb-6">
        <i class="fas fa-info-circle mr-2"></i> <?= htmlspecialchars($notice) ?>
      </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
      <div class="alert-error p-4 rounded-lg mb-6">
        <i class="fas fa-exclamation-circle mr-2"></i> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <?php if (!is_alumno()): ?>
    <!-- Botón para abrir modal de subida -->
    <div class="glass-card p-6 mb-6 text-center">
      <h2 class="text-xl font-semibold mb-4 flex items-center justify-center">
        <i class="fas fa-cloud-upload-alt mr-2"></i> Gestión de Material
      </h2>
      <button onclick="openUploadModal()" class="btn-primary px-6 py-3 rounded-lg text-lg">
        <i class="fas fa-plus mr-2"></i> Subir Nuevo Material
      </button>
      <p class="text-gray-400 text-sm mt-3">
        Límite: <?= is_profesor() ? '60MB' : '512MB (administración)' ?>
      </p>
    </div>
    <?php endif; ?>

    <!-- Material disponible -->
    <div class="glass-card p-6">
      <h2 class="text-xl font-semibold mb-4 flex items-center">
        <i class="fas fa-book mr-2"></i> Material disponible
      </h2>
      
      <?php if (empty($files)): ?>
        <div class="text-center py-10">
          <i class="fas fa-folder-open text-4xl text-gray-500 mb-3"></i>
          <p class="text-gray-400">Aún no hay archivos cargados para esta materia.</p>
        </div>
      <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <?php foreach ($files as $f): 
            $metaItem = null;
            foreach ($meta as $m) { if ($m['file']===$f) { $metaItem = $m; break; } }
            $size = $metaItem['size'] ?? filesize($dir . '/' . $f);
            $extension = pathinfo($f, PATHINFO_EXTENSION);
            $file_icon = get_file_icon($extension);
          ?>
          <div class="file-card p-4">
            <div class="flex items-start space-x-3">
              <div class="text-2xl text-blue-400 mt-1">
                <i class="fas fa-<?= $file_icon ?>"></i>
              </div>
              <div class="flex-1 min-w-0">
                <h3 class="font-medium truncate" title="<?= htmlspecialchars($f) ?>">
                  <?= htmlspecialchars($f) ?>
                </h3>
                <p class="text-gray-400 text-sm"><?= human_filesize($size) ?></p>
                
                <?php if ($metaItem): ?>
                <div class="mt-2 text-xs text-gray-500">
                  <div>
                    <i class="fas fa-user mr-1"></i>
                    <?= htmlspecialchars($metaItem['uploader']) ?> (<?= htmlspecialchars($metaItem['role']) ?>)
                  </div>
                  <div>
                    <i class="fas fa-clock mr-1"></i>
                    <?= date('d/m/Y H:i', strtotime($metaItem['time'])) ?>
                  </div>
                </div>
                <?php endif; ?>
              </div>
            </div>
            
            <div class="flex justify-between items-center mt-4">
              <a href="<?= 'uploads/modulo' . $modulo . '/' . $slug . '/' . rawurlencode($f) ?>" 
                 download
                 class="btn-outline px-3 py-1 rounded text-sm">
                <i class="fas fa-download mr-1"></i> Descargar
              </a>
              
              <?php if (is_admin() || (is_profesor() && $metaItem && $metaItem['uploader'] === $_SESSION['user']['username'])): ?>
                <form method="post" onsubmit="return confirm('¿Eliminar archivo?');">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="file" value="<?= htmlspecialchars($f) ?>">
                  <button class="btn-danger px-3 py-1 rounded text-sm">
                    <i class="fas fa-trash mr-1"></i> Eliminar
                  </button>
                </form>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="text-center mt-8">
      <a href="<?= $modulo == 2 ? 'modulo2.php' : 'modulo1.php' ?>" class="btn-outline px-4 py-2 rounded-lg inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Volver al módulo
      </a>
    </div>
  </main>

  <!-- Modal para subir archivos -->
  <div id="uploadModal" class="modal-overlay">
    <div class="modal-container">
      <div class="modal-header">
        <h2 class="text-xl font-semibold">
          <i class="fas fa-cloud-upload-alt mr-2"></i> Subir Material
        </h2>
        <button class="modal-close" onclick="closeUploadModal()">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <form id="uploadForm" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="upload">
        
        <div class="file-upload-area" id="dropArea">
          <div class="file-upload-icon">
            <i class="fas fa-cloud-upload-alt"></i>
          </div>
          <p class="font-medium">Haz clic o arrastra un archivo aquí</p>
          <p class="text-gray-400 text-sm mt-1">Formatos permitidos: documentos, imágenes, PDFs, etc.</p>
          <input type="file" name="file" id="fileInput" class="file-input" required>
        </div>
        
        <div id="fileInfo" class="file-info">
          <p class="font-medium" id="fileName"></p>
          <p class="text-sm text-gray-400" id="fileSize"></p>
        </div>
        
        <div class="flex justify-end space-x-3 mt-6">
          <button type="button" onclick="closeUploadModal()" class="btn-outline px-4 py-2 rounded-lg">
            Cancelar
          </button>
          <button type="submit" class="btn-primary px-4 py-2 rounded-lg">
            <i class="fas fa-upload mr-2"></i> Subir Archivo
          </button>
        </div>
      </form>
    </div>
  </div>

  <footer class="bg-gray-900/50 py-4 text-center text-gray-400 text-sm mt-10">
    <p>© 2025 Sistema Web de Gestión — Escuela de Suboficiales y Agentes "Agente Juan José Vides"</p>
  </footer>

  <script>
    // Funciones para controlar el modal
    function openUploadModal() {
      document.getElementById('uploadModal').classList.add('active');
      document.body.style.overflow = 'hidden';
    }
    
    function closeUploadModal() {
      document.getElementById('uploadModal').classList.remove('active');
      document.body.style.overflow = 'auto';
      // Resetear el formulario
      document.getElementById('uploadForm').reset();
      document.getElementById('fileInfo').style.display = 'none';
    }
    
    // Cerrar modal al hacer clic fuera del contenido
    document.getElementById('uploadModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeUploadModal();
      }
    });
    
    // Manejar la selección de archivos
    const fileInput = document.getElementById('fileInput');
    const dropArea = document.getElementById('dropArea');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    
    fileInput.addEventListener('change', function() {
      if (this.files.length > 0) {
        showFileInfo(this.files[0]);
      }
    });
    
    // Drag and drop
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
      dropArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
      e.preventDefault();
      e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
      dropArea.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
      dropArea.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight() {
      dropArea.classList.add('dragover');
    }
    
    function unhighlight() {
      dropArea.classList.remove('dragover');
    }
    
    dropArea.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
      const dt = e.dataTransfer;
      const files = dt.files;
      
      if (files.length > 0) {
        fileInput.files = files;
        showFileInfo(files[0]);
      }
    }
    
    function showFileInfo(file) {
      fileName.textContent = file.name;
      fileSize.textContent = formatFileSize(file.size);
      fileInfo.style.display = 'block';
    }
    
    function formatFileSize(bytes) {
      if (bytes === 0) return '0 Bytes';
      
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
  </script>
</body>
</html>