<?php require_once __DIR__.'/functions.php'; require_login(); $settings = read_settings(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel - Aula Virtual</title>
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
      transition: all 0.3s ease;
    }
    
    .glass-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      border-color: rgba(255, 255, 255, 0.3);
    }
    
    .glass-card.locked {
      opacity: 0.7;
      cursor: not-allowed;
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
    
    .module-icon {
      font-size: 2.5rem;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 70px;
      height: 70px;
      border-radius: 16px;
      background: rgba(255, 255, 255, 0.1);
    }
  </style>
</head>
<body class="min-h-screen flex flex-col">
  <!-- Navbar -->
  <nav class="glass-nav py-4 px-6 flex justify-between items-center sticky top-0 z-10">
    <div class="flex items-center space-x-3">
      <img src="assets/logo.png" alt="Escudo Policía de Tucumán" width="75">
</span>
      <span class="font-bold"></span>
    </div>
    <div class="flex items-center space-x-4">
      <span class="bg-gray-800 px-3 py-1 rounded-full text-sm">
        <?= htmlspecialchars($_SESSION["user"]["name"]) ?> (<?= htmlspecialchars($_SESSION["user"]["role"]) ?>)
      </span>
      <?php if (is_admin()): ?>
        <a href="admin.php" class="btn-primary px-3 py-1 rounded-lg text-sm">
          Panel Admin
        </a>
      <?php endif; ?>
      <a href="logout.php" class="btn-outline px-3 py-1 rounded-lg text-sm">
        <i class="fas fa-sign-out-alt mr-1"></i> Salir
      </a>
    </div>
  </nav>

  <main class="flex-1 container mx-auto px-4 py-8">
    <div class="text-center mb-10">
      <h1 class="text-3xl font-bold mb-2">Bienvenido/a</h1>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-14 text-center drop-shadow-lg">
      Selecciona el módulo al que quieres acceder:
    </h2>
    </div>

    <div class="grid md:grid-cols-2 gap-6 max-w-4xl mx-auto">
      <!-- Módulo 1 -->
      <a href="modulo1.php" 
         class="cursor-pointer bg-white/10 border border-white/20 backdrop-blur-xl rounded-2xl p-6 flex flex-col items-center text-center shadow-lg hover:shadow-2xl hover:border-blue-400/40 transition transform hover:-translate-y-2 hover:scale-105">
        <img src="assets/modulo1.png" alt="Modulo1" width="200">
        <h2 class="text-lg font-semibold text-white mb-2">Modulo 1</h2>
      </a>
      </a>

      <!-- Módulo 2 -->
      <a href="modulo2.php" 
         class="cursor-pointer bg-white/10 border border-white/20 backdrop-blur-xl rounded-2xl p-6 flex flex-col items-center text-center shadow-lg hover:shadow-2xl hover:border-cyan-400/40 transition transform hover:-translate-y-2 hover:scale-105">
        <img src="assets/modulo2.png" alt="Modulo2" width="200">
        <h1 class="text-lg font-semibold text-white mb-2">Modulo 2</h1>
      </a>
    </div>
  </main>

        <?php if ($settings['module2_enabled']): ?>
          <a href="modulo2.php" class="text-blue-400">
            
          </a>
        <?php else: ?>
          <div class="text-gray-500 cursor-not-allowed">
            <i class="fas fa-lock"></i>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="text-center mt-10">
      <a href="index.php" class="inline-flex items-center text-blue-400 hover:text-blue-300">
        
      </a>
    </div>
  </main>

  <footer class="bg-gradient-to-r from-[#1e293b]/80 to-[#0f172a]/80 border-t border-white/10 py-6 backdrop-blur-md mt-auto">
    <div class="max-w-7xl mx-auto text-center">
      <p class="text-sm text-gray-400">
    <p>© 2025 Sistema Web de Gestión — Escuela de Suboficiales y Agentes "Agente Juan José Vides"</p>
  </footer>
</body>
</html>
