<?php require_once __DIR__ . '/functions.php';
require_login();
$subs = subjects_by_module(1); ?>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Módulo de Materias</title>
  <link rel="icon" type="image/png" href="/assets/logo.png">
  <script src="https://cdn.tailwindcss.com"></script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Módulo 1 - Materias</title>
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

    .subject-card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 16px;
      padding: 1.5rem;
      text-decoration: none;
      color: inherit;
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
    }

    .subject-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      border-color: rgba(255, 255, 255, 0.3);
      text-decoration: none;
      color: inherit;
    }

    .subject-icon {
      font-size: 2.5rem;
      margin-bottom: 1rem;
    }

    .btn-outline {
      background: transparent;
      border: 1px solid var(--primary);
      color: var(--primary);
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
    }

    .btn-outline:hover {
      background: var(--primary);
      color: white;
    }
  </style>
</head>

<body class="min-h-screen flex flex-col">
  <!-- Navbar -->
  <nav class="glass-nav py-4 px-6 flex justify-between items-center sticky top-0 z-10">
    <div>
      <a href="dashboard.php" class="btn-outline px-3 py-1 rounded-lg text-sm">
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
    <div class="text-center mb-10">
      <h1 class="text-3xl font-bold mb-2">Materias del Módulo 1</h1>
      <p class="text-gray-300">Selecciona una materia para acceder a sus contenidos</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5 max-w-6xl mx-auto">
      <?php foreach ($subs as $s): ?>
        <a href="subject.php?id=<?= urlencode($s['slug']) ?>&modulo=1" class="subject-card">
          <div class="subject-icon"><?= $s['icon'] ?></div>
          <h3 class="text-lg font-semibold"><?= htmlspecialchars($s['name']) ?></h3>
        </a>
      <?php endforeach; ?>
    </div>

    <div class="text-center mt-10">
      <a href="dashboard.php" class="inline-flex items-center text-blue-400 hover:text-blue-300">

      </a>
    </div>
  </main>

  <footer class="bg-gray-900/50 py-4 text-center text-gray-400 text-sm mt-10">
    <p>© 2025 Sistema Web de Gestión — Escuela de Suboficiales y Agentes "Agente Juan José Vides"</p>
  </footer>
</body>

</html>