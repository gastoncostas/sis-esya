<?php
require_once __DIR__ . '/functions.php';
if (isset($_SESSION["user"])) {
  header("Location: dashboard.php");
  exit;
}
$err = isset($_GET['error']) ? $_GET['error'] : '';
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Aula Virtual - Escuela de Suboficiales y Agentes</title>
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

    .glass-card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 16px;
    }

    .btn-primary {
      background: var(--primary);
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      background: var(--secondary);
      transform: translateY(-2px);
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
  </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
  <div class="w-full max-w-4xl">
    <div class="glass-card p-8 shadow-2xl">
      <div class="grid md:grid-cols-2 gap-8 items-center">
        <!-- Brand Section -->
        <div class="max-w-lg mx-auto bg-gray-900 rounded-2xl shadow-lg p-8 text-center">
          <!-- Logo centrado -->
          <div class="flex justify-center mb-4">
            <img src="assets/logo.png" alt="Escudo Policía de Tucumán" class="w-32 md:w-40">
          </div>

          <!-- Títulos -->
          <h1 class="text-3xl font-bold mb-2 text-white">Escuela de Suboficiales y Agentes</h1>
          <h2 class="text-xl text-blue-300 mb-6">"Agente Juan José Vides"</h2>
          <p class="text-gray-300 mb-6">Sistema de Aula Virtual para formación policial</p>

          <!-- Secciones centradas con animaciones -->
          <div class="space-y-6">
            <div class="flex flex-col items-center transition transform hover:scale-105">
              <div class="text-3xl text-blue-400 mb-2 transition-colors duration-300 hover:text-blue-200">
                <i class="fas fa-book-open"></i>
              </div>
              <div>
                <h5 class="font-semibold text-white">Material de estudio</h5>
                <p class="text-sm text-gray-300">Accede a todos los recursos educativos</p>
              </div>
            </div>

            <div class="flex flex-col items-center transition transform hover:scale-105">
              <div class="text-3xl text-blue-400 mb-2 transition-colors duration-300 hover:text-blue-200">
                <i class="fas fa-chalkboard-teacher"></i>
              </div>
              <div>
                <h5 class="font-semibold text-white">Módulos especializados</h5>
                <p class="text-sm text-gray-300">Continua formacion de los cursantes</p>
              </div>
            </div>

            <div class="flex flex-col items-center transition transform hover:scale-105">
              <div class="text-3xl text-blue-400 mb-2 transition-colors duration-300 hover:text-blue-200">
                <i class="fas fa-file-download"></i>
              </div>
              <div>
                <h5 class="font-semibold text-white">Descargas organizadas</h5>
                <p class="text-sm text-gray-300">Encuentra fácilmente lo que necesitas</p>
              </div>
            </div>
          </div>
        </div>
        <!-- Login Section -->
        <div>
          <div class="text-center mb-6">
            <h2 class="text-2xl font-bold">Aula Virtual</h2>
            <p class="text-gray-300">Ingresa a tu cuenta para continuar</p>
          </div>

          <?php if ($err): ?>
            <div class="alert-error p-3 rounded-lg mb-4">
              <i class="fas fa-exclamation-circle mr-2"></i> <?= htmlspecialchars($err) ?>
            </div>
          <?php endif; ?>

          <?php if ($msg): ?>
            <div class="alert-info p-3 rounded-lg mb-4">
              <i class="fas fa-info-circle mr-2"></i> <?= htmlspecialchars($msg) ?>
            </div>
          <?php endif; ?>

          <form method="post" action="login.php" class="space-y-4">
            <div>
              <label for="username" class="block text-gray-200 mb-2">Usuario</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <i class="fas fa-user text-gray-400"></i>
                </div>
                <input type="text" id="username" name="username" required
                  class="form-input pl-10 pr-4 py-3 rounded-lg w-full"
                  placeholder="Ingresa tu usuario">
              </div>
            </div>

            <div>
              <label for="password" class="block text-gray-200 mb-2">Contraseña</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <i class="fas fa-lock text-gray-400"></i>
                </div>
                <input type="password" id="password" name="password" required
                  class="form-input pl-10 pr-4 py-3 rounded-lg w-full"
                  placeholder="Ingresa tu contraseña">
              </div>
            </div>

            <button type="submit" class="btn-primary w-full py-3 rounded-lg font-semibold">
              <i class="fas fa-sign-in-alt mr-2"></i> Ingresar
            </button>

            <div class="text-center pt-4">
              <p class="text-gray-300">¿No tienes cuenta? <a href="register.php" class="text-blue-400 hover:underline">Regístrate aquí</a></p>
            </div>
          </form>

          <div class="mt-6 pt-4 border-t border-gray-700">
            <p class="text-center text-gray-400 text-sm mb-2">Usuarios de demostración:</p>
            <div class="flex justify-center gap-2 flex-wrap">
              <span class="bg-gray-800 text-xs px-2 py-1 rounded">admin / Admin1234</span>
              <span class="bg-gray-800 text-xs px-2 py-1 rounded">profesor / Prof1234</span>
              <span class="bg-gray-800 text-xs px-2 py-1 rounded">alumno / Alumno1234</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>