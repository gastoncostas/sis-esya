<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header("Location: ../../login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$error = '';
$success = '';
$archivo_subido = '';

// Función para leer archivo CSV
function leerCSV($ruta_archivo) {
    $datos = [];
    if (($handle = fopen($ruta_archivo, "r")) !== FALSE) {
        while (($fila = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $datos[] = $fila;
        }
        fclose($handle);
    }
    return $datos;
}

// Función para convertir fecha de Excel a MySQL
function excelAFecha($fecha_excel) {
    if (is_numeric($fecha_excel)) {
        // Fecha de Excel (días desde 1900-01-01)
        $timestamp = ($fecha_excel - 25569) * 86400;
        return date('Y-m-d', $timestamp);
    }
    // Intentar parsear como fecha normal
    $timestamp = strtotime($fecha_excel);
    if ($timestamp !== false) {
        return date('Y-m-d', $timestamp);
    }
    return null;
}

// Procesar formulario de importación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_csv'])) {
    $archivo = $_FILES['archivo_csv'];
    
    // Validar archivo
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        $error = 'Error al subir el archivo. Código: ' . $archivo['error'];
    } elseif ($archivo['type'] !== 'text/csv' && pathinfo($archivo['name'], PATHINFO_EXTENSION) !== 'csv') {
        $error = 'Solo se permiten archivos CSV';
    } elseif ($archivo['size'] > 10 * 1024 * 1024) {
        $error = 'El archivo no puede ser mayor a 10MB';
    } else {
        // Mover archivo temporal
        $nombre_archivo = uniqid() . '_' . $archivo['name'];
        $ruta_archivo = '../../uploads/' . $nombre_archivo;
        
        if (!is_dir('../../uploads')) {
            mkdir('../../uploads', 0755, true);
        }
        
        if (move_uploaded_file($archivo['tmp_name'], $ruta_archivo)) {
            $archivo_subido = $nombre_archivo;
            
            // Leer archivo CSV
            $filas = leerCSV($ruta_archivo);
            
            if (empty($filas)) {
                $error = 'El archivo CSV está vacío o no se pudo leer';
            } else {
                // Eliminar encabezados (primera fila)
                $encabezados = array_shift($filas);
                
                $registros_procesados = 0;
                $registros_importados = 0;
                $errores = [];
                
                // Procesar cada fila
                foreach ($filas as $indice => $fila) {
                    $registros_procesados++;
                    
                    // Validar datos mínimos
                    if (empty($fila[0]) || empty($fila[1]) || empty($fila[2])) {
                        $errores[] = "Fila " . ($indice + 2) . ": DNI, Apellido y Nombre son obligatorios";
                        continue;
                    }
                    
                    $dni = trim($fila[0]);
                    $apellido = trim($fila[1]);
                    $nombre = trim($fila[2]);
                    $fecha_nacimiento = !empty($fila[3]) ? excelAFecha($fila[3]) : null;
                    $lugar_nacimiento = !empty($fila[4]) ? trim($fila[4]) : null;
                    $domicilio = !empty($fila[5]) ? trim($fila[5]) : null;
                    $telefono = !empty($fila[6]) ? trim($fila[6]) : null;
                    $email = !empty($fila[7]) ? trim($fila[7]) : null;
                    $estado_civil = !empty($fila[8]) ? trim($fila[8]) : null;
                    $nivel_educativo = !empty($fila[9]) ? trim($fila[9]) : null;
                    $comision = !empty($fila[10]) ? strtoupper(trim($fila[10])) : 'A';
                    $fecha_ingreso = !empty($fila[11]) ? excelAFecha($fila[11]) : date('Y-m-d');
                    $observaciones = !empty($fila[12]) ? trim($fila[12]) : null;
                    
                    // Validar comisión
                    if (!in_array($comision, ['A', 'B', 'C', 'D', 'E', 'F'])) {
                        $comision = 'A';
                    }
                    
                    // Verificar si el DNI ya existe
                    $stmt_check = $conn->prepare("SELECT id FROM aspirantes WHERE dni = ?");
                    $stmt_check->bind_param("s", $dni);
                    $stmt_check->execute();
                    $stmt_check->store_result();
                    
                    if ($stmt_check->num_rows > 0) {
                        $errores[] = "Fila " . ($indice + 2) . ": El DNI $dni ya existe en el sistema";
                        $stmt_check->close();
                        continue;
                    }
                    $stmt_check->close();
                    
                    // Insertar nuevo aspirante
                    $stmt = $conn->prepare("INSERT INTO aspirantes (dni, apellido, nombre, fecha_nacimiento, lugar_nacimiento, domicilio, telefono, email, estado_civil, nivel_educativo, comision, fecha_ingreso, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    
                    $stmt->bind_param("sssssssssssss", 
                        $dni, $apellido, $nombre, $fecha_nacimiento, $lugar_nacimiento, 
                        $domicilio, $telefono, $email, $estado_civil, $nivel_educativo, 
                        $comision, $fecha_ingreso, $observaciones
                    );
                    
                    if ($stmt->execute()) {
                        $registros_importados++;
                    } else {
                        $errores[] = "Fila " . ($indice + 2) . ": Error al insertar - " . $stmt->error;
                    }
                    
                    $stmt->close();
                }
                
                // Mostrar resultados
                if ($registros_importados > 0) {
                    $success = "Importación completada. $registros_importados de $registros_procesados registros importados correctamente.";
                    
                    if (!empty($errores)) {
                        $error .= "Errores encontrados:<br>" . implode("<br>", array_slice($errores, 0, 10));
                        if (count($errores) > 10) {
                            $error .= "<br>... y " . (count($errores) - 10) . " errores más";
                        }
                    }
                } else {
                    $error = "No se importó ningún registro. Errores:<br>" . implode("<br>", $errores);
                }
            }
            
            // Eliminar archivo temporal
            unlink($ruta_archivo);
            
        } else {
            $error = 'Error al mover el archivo subido';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Importar Aspirantes</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/detalle_asp.css">
    <link rel="stylesheet" href="../../assets/css/unified_header_footer.css">
    <link rel="stylesheet" href="../../assets/css/importar_excel_asp.css">
</head>

<body>
    <?php include '../../includes/unified_header.php'; ?>

    <div class="container">
        <div class="back-link">
            <a href="index.php">← Volver al listado</a>
        </div>

        <h1>Importar Aspirantes desde CSV</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Instrucciones -->
        <div class="instructions-card">
            <div class="instructions-header">
                <div class="instructions-icon">📋</div>
                <h2 class="instructions-title">Instrucciones de Importación</h2>
            </div>

            <div class="instruction-grid">
                <div class="instruction-group">
                    <div class="instruction-label">
                        <span class="step-number">1</span>
                        Descarga la Plantilla
                    </div>
                    <div class="instruction-value">Descargue la plantilla de ejemplo con el formato correcto</div>
                </div>

                <div class="instruction-group">
                    <div class="instruction-label">
                        <span class="step-number">2</span>
                        Complete los Datos
                    </div>
                    <div class="instruction-value">Llene la plantilla con la información de los aspirantes</div>
                </div>

                <div class="instruction-group">
                    <div class="instruction-label">
                        <span class="step-number">3</span>
                        Guarde como CSV
                    </div>
                    <div class="instruction-value">Exporte el archivo como CSV (delimitado por comas)</div>
                </div>

                <div class="instruction-group">
                    <div class="instruction-label">
                        <span class="step-number">4</span>
                        Suba el Archivo
                    </div>
                    <div class="instruction-value">Seleccione y cargue su archivo CSV completado</div>
                </div>

                <div class="instruction-group">
                    <div class="instruction-label">
                        <span class="step-number">5</span>
                        Revise Resultados
                    </div>
                    <div class="instruction-value">Verifique el reporte de importación y errores</div>
                </div>
            </div>
        </div>

        <!-- Plantilla de Descarga -->
        <div class="template-card">
            <div class="detail-header">
                <h2 class="detail-title">📊 Plantilla de Ejemplo</h2>
            </div>

            <div class="detail-grid">
                <div class="detail-group">
                    <div class="detail-label">Formato de Plantilla</div>
                    <div class="detail-value">CSV con datos de ejemplo de Tucumán</div>
                </div>

                <div class="detail-group">
                    <div class="detail-label">Campos Incluidos</div>
                    <div class="detail-value">Todos los campos necesarios para aspirantes</div>
                </div>

                <div class="detail-group">
                    <div class="detail-label">Datos de Ejemplo</div>
                    <div class="detail-value">Información realista para guiar el llenado</div>
                </div>
            </div>

            <div class="action-buttons">
                <a href="generar_plantilla.php" class="btn-download">⬇️ Descargar Plantilla CSV</a>
            </div>
        </div>

        <!-- Formulario de Subida -->
        <div class="upload-card">
            <div class="detail-header">
                <h2 class="detail-title">📤 Subir Archivo CSV</h2>
            </div>

            <form method="POST" action="" enctype="multipart/form-data">
                <div class="upload-box">
                    <div class="upload-icon">📄</div>
                    <h3>Seleccione el archivo CSV con los datos de los aspirantes</h3>
                    
                    <div class="form-group">
                        <input type="file" name="archivo_csv" accept=".csv" required>
                    </div>
                    
                    <p class="text-muted">Formato aceptado: .csv (delimitado por comas, Máx. 10MB)</p>
                </div>

                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary">🚀 Iniciar Importación</button>
                    <a href="index.php" class="btn btn-cancel">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <?php include '../../includes/unified_footer.php'; ?>
</body>

</html>