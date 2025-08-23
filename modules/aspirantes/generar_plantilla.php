<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header("Location: ../../login.php");
    exit();
}

// Datos de ejemplo de Tucumán
$ejemplos = [
    [
        'DNI', 'Apellido', 'Nombre', 'FechaNacimiento', 'LugarNacimiento', 
        'Domicilio', 'Telefono', 'Email', 'EstadoCivil', 'NivelEducativo', 
        'Comision', 'FechaIngreso', 'Observaciones'
    ],
    [
        '40123456', 'Gómez', 'Juan Carlos', '2000-03-15', 
        'San Miguel de Tucumán, Tucumán', 'Av. Soldati 1234', '3815123456', 
        'juan.gomez@email.com', 'soltero', 'secundario', 'A', '2025-02-01', ''
    ],
    [
        '38987654', 'Rodríguez', 'María Elena', '1999-07-22',
        'Yerba Buena, Tucumán', 'Calle Lamadrid 567', '3814987654',
        'maria.rodriguez@email.com', 'soltero', 'terciario', 'B', '2025-02-01', 'Becado'
    ],
    [
        '42111222', 'Pérez', 'Carlos Alberto', '2001-11-30',
        'Tafí Viejo, Tucumán', 'Ruta 301 Km 5', '3815112233',
        'carlos.perez@email.com', 'soltero', 'secundario', 'C', '2025-02-01', ''
    ],
    [
        '40333444', 'López', 'Ana María', '2002-05-08',
        'Lules, Tucumán', 'Sarmiento 890', '3814334455',
        'ana.lopez@email.com', 'soltero', 'secundario', 'D', '2025-02-01', 'Transporte propio'
    ],
    [
        '39555666', 'Martínez', 'Roberto José', '1998-12-17',
        'Monteros, Tucumán', 'Belgrano 234', '3815556677',
        'roberto.martinez@email.com', 'casado', 'universitario', 'E', '2025-02-01', 'Trabaja medio tiempo'
    ],
    [
        '42777888', 'Fernández', 'Laura Beatriz', '2003-09-03',
        'Concepción, Tucumán', '25 de Mayo 456', '3815778899',
        'laura.fernandez@email.com', 'soltero', 'secundario', 'F', '2025-02-01', ''
    ],
    [
        '41000111', 'García', 'Diego Alejandro', '2000-01-25',
        'Banda del Río Salí, Tucumán', 'San Martín 789', '3815001112',
        'diego.garcia@email.com', 'soltero', 'terciario', 'A', '2025-02-01', 'Deportista'
    ],
    [
        '39222333', 'Sánchez', 'Sofía Isabel', '1999-06-14',
        'Alderetes, Tucumán', 'Mitre 321', '3815223344',
        'sofia.sanchez@email.com', 'soltero', 'secundario', 'B', '2025-02-01', ''
    ],
    [
        '41888555', 'Torres', 'Miguel Ángel', '2002-08-19',
        'Aguilares, Tucumán', '9 de Julio 654', '3815885566',
        'miguel.torres@email.com', 'soltero', 'secundario', 'C', '2025-02-01', 'Hijo de personal'
    ],
    [
        '40666777', 'Romero', 'Carolina Andrea', '2001-02-28',
        'Famaillá, Tucumán', 'Av. Alem 987', '3815667788',
        'carolina.romero@email.com', 'soltero', 'terciario', 'D', '2025-02-01', ''
    ]
];

// Configurar headers para descarga
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="plantilla_aspirantes_tucuman.csv"');
header('Cache-Control: max-age=0');

// Crear output
$output = fopen('php://output', 'w');

// Escribir BOM para UTF-8 (opcional, ayuda con Excel)
fwrite($output, "\xEF\xBB\xBF");

// Escribir datos
foreach ($ejemplos as $fila) {
    fputcsv($output, $fila);
}

fclose($output);
exit;