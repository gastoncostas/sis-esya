<?php
session_start();

function base_path() {
  return __DIR__;
}

function data_path($file) {
  return __DIR__ . '/data/' . $file;
}

function uploads_path($subject, $module = 1) {
  $path = __DIR__ . '/uploads/modulo' . $module . '/' . $subject;
  if (!file_exists($path)) {
    mkdir($path, 0777, true);
  }
  return $path;
}

function load_json($path) {
  if (!file_exists($path)) return [];
  $json = file_get_contents($path);
  $data = json_decode($json, true);
  return $data ? $data : [];
}

function save_json($path, $data) {
  $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
  return file_put_contents($path, $json) !== false;
}

function subjects() {
  return [
    // MÓDULO 1
    ["slug"=>"modulo-inicial-basico-normativas-nacionales-y-provinciales","name"=>"Módulo Inicial Básico. normativas Nacionales y Provinciales","icon"=>"🧭", "modulo"=>1],
    ["slug"=>"concepto-de-doctrina","name"=>"Concepto de Doctrina","icon"=>"📜", "modulo"=>1],
    ["slug"=>"ceremonial-protocolo-y-procedimiento-policial","name"=>"Ceremonial, Protocolo y Procedimiento Policial","icon"=>"🎖️", "modulo"=>1],
    ["slug"=>"uso-racional-de-los-dispositivos-de-coercion","name"=>"Uso racional de los dispositivos de coerción","icon"=>"🛡️", "modulo"=>1],
    ["slug"=>"enfoque-sociologico-contemporaneo","name"=>"Enfoque sociológico contemporanero","icon"=>"👥", "modulo"=>1],
    ["slug"=>"primeros-auxilios","name"=>"Primeros Auxilios","icon"=>"⛑️", "modulo"=>1],
    ["slug"=>"defensa-policial-aplicada-a-la-labor-del-agente-de-policia","name"=>"Defensa Policial aplicada a la labor del Agente de Policía","icon"=>"🥋", "modulo"=>1],
    ["slug"=>"seguridad-vial","name"=>"Seguridad Vial","icon"=>"🚦", "modulo"=>1],
    ["slug"=>"salud-y-calidad-de-vida-la-aptitud-fisica-del-agente-de-policia","name"=>"La aptitud física del Agente de Policía","icon"=>"🏃", "modulo"=>1],
    ["slug"=>"herramientas-para-detener-y-superar-la-escala-de-violencia","name"=>"Herramientas para detener y superar la escala de violencia","icon"=>"⚖️", "modulo"=>1],
    
    // MÓDULO 2
    ["slug"=>"modulo-inicial-basico-normativas-nacionales-y-provinciales","name"=>"Módulo inicial básico Normativas nacionales y provinciales","icon"=>"⚖️", "modulo"=>2],
    ["slug"=>"presencia-del-delito","name"=>"Presencia del delito","icon"=>"🔍", "modulo"=>2],
    ["slug"=>"policia-comunitaria","name"=>"Policia comunitaria","icon"=>"👮‍♂️", "modulo"=>2],
    ["slug"=>"investigacion-cientifica-del-delito","name"=>"Investigacion cientifica del delito","icon"=>"🕵️", "modulo"=>2],
    ["slug"=>"defensa-personal-aplicada-a-la-labor-del-agente-de-policia","name"=>"Defensa policial aplicada a la labor del agente de policia","icon"=>"🥋", "modulo"=>2],
    ["slug"=>"ceremonial-protocolo-y-procedimiento-policial","name"=>"Ceremonial, Protocolo y procedimiento policial","icon"=>"🚨", "modulo"=>2],
    ["slug"=>"uso-racional-de-los-dispositivos-de-coercion","name"=>"Uso racional de los dispositivos de coercion","icon"=>"🛡️", "modulo"=>2],
    ["slug"=>"teoria-y-abordaje-del-conflicto-en-la-investigacion-del-agente","name"=>"Teoria y abordaje del conflicto en la investigacion del agente","icon"=>"🧠 ", "modulo"=>2],
    ["slug"=>"salud-y-calidad-de-vida-la-aptitud-fisica-del-agente-de-policia","name"=>"Salud y calidad de vida, la aptitud fisica del agente de policia","icon"=>"🏋️", "modulo"=>2],
  ];
}

function subject_by_slug($slug) {
  foreach (subjects() as $s) {
    if ($s["slug"] === $slug) return $s;
  }
  return null;
}

function subjects_by_module($module) {
  return array_filter(subjects(), function($s) use ($module) {
    return $s["modulo"] == $module;
  });
}

function require_login() {
  if (!isset($_SESSION["user"])) {
    header("Location: index.php?msg=Inicia%20sesión");
    exit;
  }
}

function require_role($roles) {
  require_login();
  $u = $_SESSION["user"];
  if (is_string($roles)) $roles = [$roles];
  if (!in_array($u["role"], $roles)) {
    header("Location: dashboard.php?error=No%20tienes%20permiso%20para%20esa%20acción");
    exit;
  }
}

function current_user() {
  return isset($_SESSION["user"]) ? $_SESSION["user"] : null;
}

function is_admin() { $u = current_user(); return $u && $u["role"] === "admin"; }
function is_profesor() { $u = current_user(); return $u && $u["role"] === "profesor"; }
function is_alumno() { $u = current_user(); return $u && $u["role"] === "alumno"; }

function read_settings() {
  $default_settings = ['module2_enabled' => false];
  $settings = load_json(data_path('settings.json'));
  return array_merge($default_settings, $settings);
}

function write_settings($s) {
  save_json(data_path('settings.json'), $s);
}

function read_users() {
  // Usuarios demo por defecto (solo se usan si no existe users.json)
  $default_users = [
    'admin' => [
      'password' => password_hash('Admin1234', PASSWORD_BCRYPT),
      'role' => 'admin',
      'name' => 'Administrador Principal',
      'email' => 'admin@escuela.com',
      'telefono' => '1122334455',
      'turno' => 'mañana',
      'comision' => 'A',
      'fecha_registro' => date('Y-m-d H:i:s')
    ],
    'profesor' => [
      'password' => password_hash('Prof1234', PASSWORD_BCRYPT),
      'role' => 'profesor',
      'name' => 'Profesor Ejemplo',
      'email' => 'profesor@escuela.com',
      'telefono' => '1144556677',
      'turno' => 'tarde',
      'comision' => 'B',
      'fecha_registro' => date('Y-m-d H:i:s')
    ],
    'alumno' => [
      'password' => password_hash('Alumno1234', PASSWORD_BCRYPT),
      'role' => 'alumno',
      'name' => 'Alumno Ejemplo',
      'email' => 'alumno@escuela.com',
      'telefono' => '1155667788',
      'turno' => 'mañana',
      'comision' => 'C',
      'fecha_registro' => date('Y-m-d H:i:s')
    ]
  ];

  $users = load_json(data_path('users.json'));
  
  // Si no hay usuarios guardados, usar los por defecto
  if (empty($users)) {
    return $default_users;
  }
  
  // Combinar usuarios existentes con los por defecto (los por defecto tienen prioridad)
  $users = array_merge($users, $default_users);
  
  // Para compatibilidad con usuarios existentes
  foreach ($users as &$user) {
    if (!isset($user['email'])) $user['email'] = '';
    if (!isset($user['telefono'])) $user['telefono'] = '';
    if (!isset($user['turno'])) $user['turno'] = 'mañana';
    if (!isset($user['comision'])) $user['comision'] = '';
    if (!isset($user['fecha_registro'])) $user['fecha_registro'] = date('Y-m-d H:i:s');
    // Mantener compatibilidad con name si no está definido
    if (!isset($user['name']) && isset($user['nombres']) && isset($user['apellidos'])) {
      $user['name'] = $user['nombres'] . ' ' . $user['apellidos'];
    }
  }
  
  return $users;
}

function write_users($u) {
  return save_json(data_path('users.json'), $u);
}

function human_filesize($bytes, $decimals = 2) {
  $size = array('B','KB','MB','GB','TB','PB');
  $factor = floor((strlen($bytes) - 1) / 3);
  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}

// Agregar esta función en functions.php
function get_file_icon($extension) {
    $extension = strtolower($extension);
    $icons = [
        'pdf' => 'file-pdf',
        'doc' => 'file-word',
        'docx' => 'file-word',
        'xls' => 'file-excel',
        'xlsx' => 'file-excel',
        'ppt' => 'file-powerpoint',
        'pptx' => 'file-powerpoint',
        'jpg' => 'file-image',
        'jpeg' => 'file-image',
        'png' => 'file-image',
        'gif' => 'file-image',
        'zip' => 'file-archive',
        'rar' => 'file-archive',
        'txt' => 'file-alt',
        'mp4' => 'file-video',
        'mov' => 'file-video',
        'avi' => 'file-video'
    ];
    
    return $icons[$extension] ?? 'file';
}

// Función para obtener la URL base
function base_url() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    return $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
}
?>