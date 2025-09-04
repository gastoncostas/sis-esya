<?php require_once __DIR__.'/functions.php'; require_role('admin');
$notice = $error = '';
$settings = read_settings();
$users = read_users();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  if ($action === 'toggle_module2') {
    $settings['module2_enabled'] = !$settings['module2_enabled'];
    write_settings($settings);
    $notice = 'Estado del Módulo 2 actualizado.';
  }
  if ($action === 'add_user') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $role = $_POST['role'] ?? 'alumno';
    $comision = $_POST['comision'] ?? '';
    if ($username && $password && $name && $comision) {
      if (isset($users[$username])) {
        $error = 'El usuario ya existe.';
      } else {
        // HASHEAR LA CONTRASEÑA - CORRECCIÓN APPLICADA
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        $users[$username] = [
          'password' => $hashed_password,  // Ahora guardamos el hash
          'role' => $role,
          'name' => $name,
          'comision' => $comision,
          'email' => $_POST['email'] ?? '',
          'telefono' => $_POST['telefono'] ?? '',
          'turno' => $_POST['turno'] ?? 'mañana',
          'fecha_registro' => date('Y-m-d H:i:s')
        ];
        write_users($users);
        $notice = 'Usuario creado.';
      }
    } else {
      $error = 'Completa todos los campos.';
    }
  }
  if ($action === 'delete_user') {
    $username = $_POST['username'] ?? '';
    if ($username && isset($users[$username])) {
      unset($users[$username]);
      write_users($users);
      $notice = 'Usuario eliminado.';
      if (isset($_SESSION['user']) && $_SESSION['user']['username']===$username) {
        session_destroy();
        header('Location: index.php?msg=Tu%20usuario%20fue%20eliminado'); exit;
      }
    } else {
      $error = 'Usuario no encontrado.';
    }
  }
}
$settings = read_settings();
$users = read_users();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Administrador</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #1a56db;
      --secondary: #0e2b5c;
      --accent: #ff3a00;
      --light: #f8fafc;
      --dark: #1e293b;
    }
    
    .bg-gradient-custom {
      background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
    }
    
    .alert-notice {
      background-color: #d1fae5;
      color: #065f46;
      border-left: 4px solid #10b981;
    }
    
    .alert-error {
      background-color: #fee2e2;
      color: #b91c1c;
      border-left: 4px solid #ef4444;
    }
  </style>
</head>
<body class="bg-gray-900 text-white flex h-screen overflow-hidden">

  <!-- SIDEBAR -->
  <aside id="sidebar" class="bg-gray-950 w-64 p-4 flex flex-col fixed inset-y-0 left-0 transform -translate-x-full md:translate-x-0 transition-transform duration-300 z-50">
    <h1 class="text-xl font-bold mb-6 bg-gradient-to-r from-cyan-400 to-violet-500 bg-clip-text text-transparent">
      ⚡Administrador 
    </h1>
    <nav class="flex flex-col space-y-2">
      <a href="#modulos" class="px-4 py-2 rounded-lg hover:bg-gray-800 module-tab active">⚙️ Módulos</a>
      <a href="#usuarios" class="px-4 py-2 rounded-lg hover:bg-gray-800 user-tab">👥 Usuarios</a>
      <a href="#material1" class="px-4 py-2 rounded-lg hover:bg-gray-800 material-tab">📚 Material Módulo 1</a>
      <a href="#material2" class="px-4 py-2 rounded-lg hover:bg-gray-800 material-tab">📚 Material Módulo 2</a>
      <a href="dashboard.php" class="px-4 py-2 rounded-lg hover:bg-gray-800">⬅️ Volver</a>
      <a href="logout.php" class="px-4 py-2 rounded-lg hover:bg-gray-800">🚪 Salir</a>
    </nav>
  </aside>

  <!-- CONTENIDO -->
  <div class="flex-1 flex flex-col w-full md:ml-64">
    <!-- HEADER -->
    <header class="bg-gray-950/80 backdrop-blur-lg border-b border-gray-800 px-4 py-4 flex items-center justify-between sticky top-0 z-40">
      <button id="menuBtn" class="md:hidden px-3 py-2 rounded-lg bg-gray-800 hover:bg-gray-700">
        ☰
      </button>
      <h2 class="text-lg font-semibold">Panel de Administración</h2>
      <span class="text-sm bg-gray-700 px-3 py-1 rounded-full"><?= htmlspecialchars($_SESSION["user"]["name"]) ?></span>
    </header>

    <!-- MAIN -->
    <main class="flex-1 p-4 overflow-y-auto space-y-6">
      
      <!-- Notificaciones -->
      <?php if ($notice): ?>
        <div class="alert-notice p-4 rounded-lg mb-4">
          <i class="fas fa-check-circle mr-2"></i> <?= htmlspecialchars($notice) ?>
        </div>
      <?php endif; ?>
      
      <?php if ($error): ?>
        <div class="alert-error p-4 rounded-lg mb-4">
          <i class="fas fa-exclamation-circle mr-2"></i> <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>
      
      <!-- SECCIÓN MÓDULOS -->
      <section id="modulos" class="content-section rounded-xl border border-gray-700 bg-gray-800/50 shadow-lg p-6">
        <h3 class="text-lg font-semibold mb-4">⚙️ Control de Módulos</h3>
        <div class="mb-4 p-4 bg-gray-700 rounded-lg">
          <p class="mb-2">Estado del Módulo 2: <strong><?= $settings['module2_enabled'] ? 'Habilitado' : 'Bloqueado' ?></strong></p>
          <form method="post" class="inline">
            <input type="hidden" name="action" value="toggle_module2">
            <button class="px-4 py-2 rounded-lg <?= $settings['module2_enabled'] ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' ?>">
              <?= $settings['module2_enabled'] ? 'Bloquear' : 'Habilitar' ?> Módulo 2
            </button>
          </form>
        </div>
      </section>

      <!-- SECCIÓN USUARIOS -->
      <section id="usuarios" class="content-section hidden rounded-xl border border-gray-700 bg-gray-800/50 shadow-lg p-6">
        <h3 class="text-lg font-semibold mb-4">👥 Gestión de Usuarios</h3>
        
        <!-- FORMULARIO CREAR USUARIO -->
        <div class="mb-6">
          <h4 class="font-medium mb-3">➕ Nuevo usuario</h4>
          <form method="post" class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
            <input type="hidden" name="action" value="add_user">
            <input type="text" name="username" placeholder="Usuario" required class="px-3 py-2 rounded-lg bg-gray-700 text-sm text-white focus:ring-2 focus:ring-cyan-500"/>
            <input type="text" name="name" placeholder="Nombre completo" required class="px-3 py-2 rounded-lg bg-gray-700 text-sm text-white focus:ring-2 focus:ring-cyan-500"/>
            <input type="password" name="password" placeholder="Contraseña" required class="px-3 py-2 rounded-lg bg-gray-700 text-sm text-white focus:ring-2 focus:ring-cyan-500"/>
            <input type="email" name="email" placeholder="Email" class="px-3 py-2 rounded-lg bg-gray-700 text-sm text-white focus:ring-2 focus:ring-cyan-500"/>
            <input type="tel" name="telefono" placeholder="Teléfono" class="px-3 py-2 rounded-lg bg-gray-700 text-sm text-white focus:ring-2 focus:ring-cyan-500"/>
            
            <select name="turno" class="px-3 py-2 rounded-lg bg-gray-700 text-sm text-white focus:ring-2 focus:ring-cyan-500">
              <option value="mañana">Mañana</option>
              <option value="tarde">Tarde</option>
            </select>
            
            <select name="comision" required class="px-3 py-2 rounded-lg bg-gray-700 text-sm text-white focus:ring-2 focus:ring-cyan-500">
              <option value="">Selecciona comisión</option>
              <option value="A">Comisión A</option>
              <option value="B">Comisión B</option>
              <option value="C">Comisión C</option>
              <option value="D">Comisión D</option>
              <option value="E">Comisión E</option>
              <option value="F">Comisión F</option>
            </select>
            
            <select name="role" class="px-3 py-2 rounded-lg bg-gray-700 text-sm text-white focus:ring-2 focus:ring-cyan-500">
              <option value="admin">Administrador</option>
              <option value="profesor">Profesor</option>
              <option value="alumno" selected>Alumno</option>
            </select>
            
            <div class="sm:col-span-2 lg:col-span-4">
              <button type="submit" class="w-full bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg shadow text-white">
                Crear usuario
              </button>
            </div>
          </form>
        </div>

        <!-- TABLA DE USUARIOS (solo en PC) -->
        <div class="hidden md:block">
          <h4 class="font-medium mb-3">📋 Tabla de Usuarios</h4>
          <div class="overflow-x-auto rounded-lg border border-gray-700">
            <table class="w-full text-sm min-w-[800px]">
              <thead class="bg-gray-900/80 text-gray-300 uppercase">
                <tr>
                  <th class="p-3">Usuario</th>
                  <th class="p-3">Nombre</th>
                  <th class="p-3">Email</th>
                  <th class="p-3">Teléfono</th>
                  <th class="p-3">Turno</th>
                  <th class="p-3">Comisión</th>
                  <th class="p-3">Rol</th>
                  <th class="p-3">Acciones</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-700">
                <?php foreach($users as $u=>$info): ?>
                <tr class="hover:bg-gray-700/40">
                  <td class="p-3"><?= htmlspecialchars($u) ?></td>
                  <td class="p-3"><?= htmlspecialchars($info['name']) ?></td>
                  <td class="p-3"><?= htmlspecialchars($info['email'] ?? '') ?></td>
                  <td class="p-3"><?= htmlspecialchars($info['telefono'] ?? '') ?></td>
                  <td class="p-3"><?= htmlspecialchars($info['turno'] ?? 'mañana') ?></td>
                  <td class="p-3"><?= htmlspecialchars($info['comision'] ?? '') ?></td>
                  <td class="p-3">
                    <span class="px-2 py-1 rounded-lg text-xs <?= 
                      $info['role'] === 'admin' ? 'bg-red-500' : 
                      ($info['role'] === 'profesor' ? 'bg-blue-500' : 'bg-green-500')
                    ?>">
                      <?= htmlspecialchars($info['role']) ?>
                    </span>
                  </td>
                  <td class="p-3">
                    <form method="post" onsubmit="return confirm('¿Eliminar usuario <?= htmlspecialchars($u) ?>?');">
                      <input type="hidden" name="action" value="delete_user">
                      <input type="hidden" name="username" value="<?= htmlspecialchars($u) ?>">
                      <button class="px-3 py-1 text-xs rounded-lg bg-red-600 hover:bg-red-700">Eliminar</button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- CARDS DE USUARIOS (solo en móvil) -->
        <div class="block md:hidden">
          <h4 class="font-medium mb-3">📱 Vista en Cards</h4>
          <div class="grid gap-4 sm:grid-cols-2">
            <?php foreach($users as $u=>$info): ?>
            <div class="p-4 rounded-xl bg-gray-800 border border-gray-700 shadow-md hover:shadow-xl transition">
              <h4 class="font-bold text-cyan-400"><?= htmlspecialchars($info['name']) ?></h4>
              <p class="text-sm text-gray-300">@<?= htmlspecialchars($u) ?></p>
              <p class="text-sm text-gray-400"><?= htmlspecialchars($info['email'] ?? '') ?></p>
              <p class="text-sm text-gray-400">📞 <?= htmlspecialchars($info['telefono'] ?? '') ?></p>
              <div class="flex justify-between items-center mt-3">
                <span class="text-xs bg-gray-900 px-2 py-1 rounded-lg"><?= htmlspecialchars($info['turno'] ?? 'mañana') ?> - <?= htmlspecialchars($info['comision'] ?? '') ?></span>
                <span class="text-xs px-2 py-1 rounded-lg <?= 
                  $info['role'] === 'admin' ? 'bg-red-500' : 
                  ($info['role'] === 'profesor' ? 'bg-blue-500' : 'bg-green-500')
                ?>"><?= htmlspecialchars($info['role']) ?></span>
              </div>
              <form method="post" onsubmit="return confirm('¿Eliminar usuario <?= htmlspecialchars($u) ?>?');" class="mt-3">
                <input type="hidden" name="action" value="delete_user">
                <input type="hidden" name="username" value="<?= htmlspecialchars($u) ?>">
                <button class="w-full px-3 py-1 text-xs rounded-lg bg-red-600 hover:bg-red-700">Eliminar</button>
              </form>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>

      <!-- SECCIÓN MATERIAL MÓDULO 1 -->
      <section id="material1" class="content-section hidden rounded-xl border border-gray-700 bg-gray-800/50 shadow-lg p-6">
        <h3 class="text-lg font-semibold mb-4">📚 Gestión de Material - Módulo 1</h3>
        <p class="text-sm text-gray-400 mb-4">Aquí puedes revisar y eliminar material del Módulo 1.</p>
        
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          <?php foreach (subjects_by_module(1) as $s): 
            $dir = uploads_path($s['slug'], 1);
            if (!file_exists($dir)) continue;
            $files = array_values(array_filter(scandir($dir), function($f){ return $f!=='.' && $f!=='..' && $f!=='meta.json'; }));
          ?>
          <div class="p-4 rounded-xl bg-gray-800 border border-gray-700">
            <strong class="text-cyan-400"><?= htmlspecialchars($s['name']) ?></strong>
            <?php if (empty($files)): ?>
              <p class="text-sm text-gray-400 mt-2">Sin archivos</p>
            <?php else: ?>
              <ul class="mt-3 space-y-2">
                <?php foreach ($files as $f): ?>
                  <li class="flex justify-between items-center p-2 bg-gray-700 rounded-lg">
                    <span class="text-sm truncate max-w-xs"><?= htmlspecialchars($f) ?></span>
                    <form action="subject.php?id=<?= urlencode($s['slug']) ?>&modulo=1" method="post" onsubmit="return confirm('¿Eliminar archivo?');">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="file" value="<?= htmlspecialchars($f) ?>">
                      <button class="px-2 py-1 text-xs rounded-lg bg-red-600 hover:bg-red-700">Eliminar</button>
                    </form>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- SECCIÓN MATERIAL MÓDULO 2 -->
      <section id="material2" class="content-section hidden rounded-xl border border-gray-700 bg-gray-800/50 shadow-lg p-6">
        <h3 class="text-lg font-semibold mb-4">📚 Gestión de Material - Módulo 2</h3>
        <p class="text-sm text-gray-400 mb-4">Aquí puedes revisar y eliminar material del Módulo 2.</p>
        
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          <?php foreach (subjects_by_module(2) as $s): 
            $dir = uploads_path($s['slug'], 2);
            if (!file_exists($dir)) continue;
            $files = array_values(array_filter(scandir($dir), function($f){ return $f!=='.' && $f!=='..' && $f!=='meta.json'; }));
          ?>
          <div class="p-4 rounded-xl bg-gray-800 border border-gray-700">
            <strong class="text-cyan-400"><?= htmlspecialchars($s['name']) ?></strong>
            <?php if (empty($files)): ?>
              <p class="text-sm text-gray-400 mt-2">Sin archivos</p>
            <?php else: ?>
              <ul class="mt-3 space-y-2">
                <?php foreach ($files as $f): ?>
                  <li class="flex justify-between items-center p-2 bg-gray-700 rounded-lg">
                    <span class="text-sm truncate max-w-xs"><?= htmlspecialchars($f) ?></span>
                    <form action="subject.php?id=<?= urlencode($s['slug']) ?>&modulo=2" method="post" onsubmit="return confirm('¿Eliminar archivo?');">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="file" value="<?= htmlspecialchars($f) ?>">
                      <button class="px-2 py-1 text-xs rounded-lg bg-red-600 hover:bg-red-700">Eliminar</button>
                    </form>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
      </section>

    </main>
  </div>

  <!-- SCRIPT -->
  <script>
    // TOGGLE SIDEBAR
    const sidebar = document.getElementById("sidebar");
    document.getElementById("menuBtn").addEventListener("click", () => {
      sidebar.classList.toggle("-translate-x-full");
    });

    // NAVEGACIÓN ENTRE SECCIONES
    document.addEventListener('DOMContentLoaded', function() {
      const tabs = document.querySelectorAll('.module-tab, .user-tab, .material-tab');
      const sections = document.querySelectorAll('.content-section');
      
      // Mostrar solo la sección activa
      function showSection(sectionId) {
        sections.forEach(section => {
          section.classList.add('hidden');
        });
        document.getElementById(sectionId).classList.remove('hidden');
        
        // Actualizar clases activas en pestañas
        tabs.forEach(tab => {
          tab.classList.remove('active', 'bg-gray-800');
        });
        
        const activeTab = document.querySelector(`[href="#${sectionId}"]`);
        if (activeTab) {
          activeTab.classList.add('active', 'bg-gray-800');
        }
        
        // Guardar en localStorage
        localStorage.setItem('activeAdminSection', sectionId);
      }
      
      // Configurar eventos de clic en pestañas
      tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
          e.preventDefault();
          const targetId = this.getAttribute('href').substring(1);
          showSection(targetId);
        });
      });
      
      // Cargar sección activa desde localStorage o por defecto
      const activeSection = localStorage.getItem('activeAdminSection') || 'modulos';
      showSection(activeSection);
      
      // Manejar hash en URL
      if (window.location.hash) {
        const hash = window.location.hash.substring(1);
        if (document.getElementById(hash)) {
          showSection(hash);
        }
      }
    });
  </script>
</body>
</html>