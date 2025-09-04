<?php 
require_once __DIR__.'/functions.php'; 

// Redirigir usuarios ya logueados
if (isset($_SESSION["user"])) { 
    header("Location: dashboard.php"); 
    exit; 
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // [Todo el código PHP permanece igual]
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de Usuario - Escuela de Suboficiales y Agentes</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .alert-error {
      background-color: #fee2e2;
      border: 1px solid #fecaca;
      color: #dc2626;
      padding: 0.75rem 1rem;
      border-radius: 0.5rem;
      margin-bottom: 1rem;
    }
  </style>
</head>

<body style="background: linear-gradient(135deg, #1b2b44, #243b55);" 
      class="flex flex-col min-h-screen">

  <!-- Navbar -->
  <nav class="bg-transparent shadow-md fixed w-full top-0 z-50 h-16 flex items-center">
    <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center w-full">
      <div class="flex items-center space-x-3">
        <!-- Logo si quieres -->
      </div>
      <div class="space-x-6">
        <a href="index.php" 
           class="text-gray-100 hover:text-blue-400 font-medium">Volver</a>
      </div>
    </div>
  </nav>

  <!-- Contenido con espacio arriba -->
  <main class="flex-1 flex items-center justify-center px-4 pt-20 pb-10">
    <div class="bg-white shadow-2xl rounded-2xl p-6 sm:p-8 w-full max-w-lg">
      
      <!-- Encabezado con logo centrado -->
      <div class="text-center mb-6 flex flex-col items-center justify-center">
        <img src="assets/logo.png" alt="Escudo Policía de Tucumán" width="150" class="mx-auto mb-4">
        <h1 class="text-3xl font-bold text-gray-700">Aula Virtual</h1>
        <p class="text-gray-500">Registrese para acceder</p>
      </div>

      <?php if ($error): ?>
        <div class="alert-error mb-4">
          <?= $error ?>
        </div>
      <?php endif; ?>

      <!-- [El resto del formulario permanece igual] -->
      <form method="post" class="space-y-5">
        <!-- Datos de acceso --> 
        <h2 class="text-lg font-semibold text-gray-700 border-b pb-2">Datos de acceso:</h2>
        
        <div> 
          <label class="block text-gray-700 font-medium">Nombre de usuario *</label>
          <input type="text" id="username" name="username" required 
                 placeholder="Ej: juan.perez" 
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                 pattern="[a-zA-Z0-9_]{4,20}" 
                 title="4-20 caracteres (letras, números o _)"
                 class="w-full mt-2 px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
        </div>
        
        <div> 
          <label class="block text-gray-700 font-medium">Contraseña *</label>
          <input type="password" id="password" name="password" required 
                 minlength="6" placeholder="Mínimo 6 caracteres" 
                 class="w-full mt-2 px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
        </div>
        
        <!-- Datos personales -->
        <h2 class="text-lg font-semibold text-gray-700 border-b pb-2">Datos personales</h2>
        
        <div> 
          <label class="block text-gray-700 font-medium">Nombres *</label> 
          <input type="text" id="nombres" name="nombres" required 
                 placeholder="Ej: Juan Carlos" 
                 value="<?= htmlspecialchars($_POST['nombres'] ?? '') ?>"
                 pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,50}"
                 class="w-full mt-2 px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"> 
        </div> 
        
        <div>
          <label class="block text-gray-700 font-medium">Apellidos *</label>
          <input type="text" id="apellidos" name="apellidos" required 
                 placeholder="Ej: Pérez González" 
                 value="<?= htmlspecialchars($_POST['apellidos'] ?? '') ?>"
                 pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,50}"
                 class="w-full mt-2 px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition">
        </div> 
        
        <div> 
          <label class="block text-gray-700 font-medium">Email *</label> 
          <input type="email" id="email" name="email" required 
                 placeholder="Ej: juan@gmail.com" 
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                 class="w-full mt-2 px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"> 
        </div>
        
        <div>
          <label class="block text-gray-700 font-medium">Teléfono celular</label>
          <input type="tel" id="telefono" name="telefono" 
                 placeholder="Ej: 1122334455" 
                 value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>"
                 pattern="[0-9]{10,15}"
                 class="w-full mt-2 px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"> 
        </div>
        
        <!-- Select Turno -->
        <div> 
          <label class="block text-gray-700 font-medium">Turno</label> 
          <select id="turno" name="turno" class="w-full mt-2 px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"> 
            <option value="mañana" <?= ($_POST['turno'] ?? 'mañana') === 'mañana' ? 'selected' : '' ?>>Mañana</option> 
            <option value="tarde" <?= ($_POST['turno'] ?? '') === 'tarde' ? 'selected' : '' ?>>Tarde</option> 
          </select>
        </div> 
        
        <!-- Select Comisión --> 
        <div> 
          <label class="block text-gray-700 font-medium">Comisión *</label> 
          <select id="comision" name="comision" required class="w-full mt-2 px-4 py-3 border rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none transition"> 
            <option value="">Selecciona una comisión</option> 
            <option value="A" <?= ($_POST['comision'] ?? '') === 'A' ? 'selected' : '' ?>>Comisión A</option> 
            <option value="B" <?= ($_POST['comision'] ?? '') === 'B' ? 'selected' : '' ?>>Comisión B</option> 
            <option value="C" <?= ($_POST['comision'] ?? '') === 'C' ? 'selected' : '' ?>>Comisión C</option> 
            <option value="D" <?= ($_POST['comision'] ?? '') === 'D' ? 'selected' : '' ?>>Comisión D</option>
            <option value="E" <?= ($_POST['comision'] ?? '') === 'E' ? 'selected' : '' ?>>Comisión E</option>
            <option value="F" <?= ($_POST['comision'] ?? '') === 'F' ? 'selected' : '' ?>>Comisión F</option>
          </select> 
        </div> 
        
        <!-- Botón --> 
        <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-semibold hover:bg-indigo-700 transition shadow-lg">Registrarse</button>
      </form> 
      
      <!-- Link a login -->
      <p class="mt-6 text-center text-gray-600">¿Ya tienes cuenta? <a href="index.php" class="text-indigo-600 hover:underline font-medium">Inicia sesión aquí</a></p>
    </div>
  </main>

  <script>
    // Validación básica del lado del cliente
    document.querySelector('form').addEventListener('submit', function(e) {
      let valid = true;
      const inputs = this.querySelectorAll('input[required], select[required]');
      
      inputs.forEach(input => {
        if (!input.value.trim()) {
          alert(`El campo ${input.previousElementSibling.textContent} es obligatorio`);
          valid = false;
          e.preventDefault();
          return;
        }
        
        if (input.pattern && !new RegExp(input.pattern).test(input.value)) {
          alert(input.title || `Formato incorrecto para ${input.previousElementSibling.textContent}`);
          valid = false;
          e.preventDefault();
          return;
        }
      });
      
      return valid;
    });
  </script>
</body>
</html>