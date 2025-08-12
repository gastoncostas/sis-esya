<?php
// Obtener el año actual
$current_year = date('Y');

// Determinar información contextual
$is_system_page = false;
if (class_exists('Auth')) {
    try {
        $auth = new Auth();
        $is_system_page = $auth->isLoggedIn();
    } catch (Exception $e) {
        $is_system_page = false;
    }
}


$quick_links = [
    'Inicio' => defined('BASE_URL') ? BASE_URL . 'inicio.php' : '/inicio.php',
    'Formación de Agentes' => defined('BASE_URL') ? BASE_URL . 'formacion_agentes.php' : '/formacion_agentes.php'
];

if ($is_system_page) {
    $quick_links['Dashboard'] = defined('BASE_URL') ? BASE_URL . 'dashboard.php' : '/dashboard.php';
    $quick_links['Aspirantes'] = defined('BASE_URL') ? BASE_URL . 'modules/aspirantes/' : '/modules/aspirantes/';
    $quick_links['Asistencia'] = defined('BASE_URL') ? BASE_URL . 'modules/asistencia/' : '/modules/asistencia/';
}
?>

<footer>
    <div class="footer-container">
        <?php if ($is_system_page): ?>
            <!-- Footer extendido para páginas del sistema -->
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Sistema ESyA</h3>
                    <p>Sistema integral de gestión para la Escuela de Suboficiales y Agentes de la Policía de Tucumán.</p>
                </div>
            </div>
        <?php else: ?>
            <!-- Footer simplificado para páginas de navegación -->
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Policía de Tucumán</h3>
                </div>
            </div>
        <?php endif; ?>

        <div class="footer-bottom">
            <p>&copy; <?php echo $current_year; ?> Policía de Tucumán - Escuela de Suboficiales y Agentes. Todos los derechos reservados.</p>
            <?php if ($is_system_page): ?>
            <?php else: ?>
                <p>Jefatura de Educación y Capacitación - Formando el futuro de la seguridad</p>
            <?php endif; ?>
        </div>
    </div>
</footer>

<?php if ($is_system_page): ?>
    <!-- Modal de información del sistema (solo para páginas del sistema) -->
    <div id="systemInfoModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
        <div style="background: white; padding: 30px; border-radius: 10px; max-width: 500px; width: 90%; max-height: 80%; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="color: #2c3e50; margin: 0;">Información del Sistema</h3>
                <button onclick="closeSystemInfo()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #7f8c8d;">&times;</button>
            </div>

            <div style="line-height: 1.6; color: #555;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <p><strong>Sistema:</strong><br><?php echo defined('APP_NAME') ? htmlspecialchars(APP_NAME) : 'Sistema ESyA'; ?></p>
                        <p><strong>Versión:</strong><br>2.0</p>
                        <p><strong>Base de Datos:</strong><br><?php echo defined('DB_NAME') ? htmlspecialchars(DB_NAME) : 'MySQL'; ?></p>
                    </div>
                    <div>
                        <p><strong>Servidor:</strong><br><?php echo htmlspecialchars($_SERVER['SERVER_NAME'] ?? 'localhost'); ?></p>
                        <p><strong>PHP:</strong><br><?php echo phpversion(); ?></p>
                        <p><strong>Estado:</strong><br><span style="color: #27ae60;">✓ Operativo</span></p>
                    </div>
                </div>

                <div style="padding: 15px; background: #f8f9fa; border-radius: 8px; margin-bottom: 20px;">
                    <p><strong>Información de Sesión:</strong></p>
                    <?php if (isset($_SESSION['temp_auth'])): ?>
                        <p>Usuario: <?php echo htmlspecialchars($_SESSION['temp_auth']['username'] ?? 'N/A'); ?></p>
                        <p>Rol: <?php echo htmlspecialchars($_SESSION['temp_auth']['rol'] ?? 'N/A'); ?></p>
                        <p>Sesión iniciada: <?php echo date('d/m/Y H:i:s'); ?></p>
                    <?php else: ?>
                        <p>No hay sesión activa</p>
                    <?php endif; ?>
                </div>

                <div style="font-size: 0.9rem; color: #7f8c8d; text-align: center;">
                    <p>Última actualización: Agosto <?php echo $current_year; ?></p>
                    <p>Desarrollado para la Policía de Tucumán</p>
                </div>
            </div>

            <div style="text-align: right; margin-top: 20px;">
                <button onclick="closeSystemInfo()" style="background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; transition: background-color 0.3s;">Cerrar</button>
            </div>
        </div>
    </div>
<?php endif; ?>

<style>
    /* Estilos adicionales para el footer */
    .footer-content {
        animation: fadeInUp 0.6s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Mejoras para enlaces del footer */
    .footer-section a {
        position: relative;
        display: inline-block;
        transition: color 0.3s ease;
    }

    .footer-section a::after {
        content: '';
        position: absolute;
        width: 0;
        height: 1px;
        bottom: -2px;
        left: 0;
        background-color: #3498db;
        transition: width 0.3s ease;
    }

    .footer-section a:hover::after {
        width: 100%;
    }

    /* Indicador de estado del sistema */
    .system-status {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #27ae60;
        animation: pulse 2s infinite;
        margin-right: 5px;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
            transform: scale(1);
        }

        50% {
            opacity: 0.5;
            transform: scale(1.1);
        }
    }

    /* Estilos del modal mejorados */
    #systemInfoModal {
        backdrop-filter: blur(5px);
        animation: modalFadeIn 0.3s ease;
    }

    #systemInfoModal>div {
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    #systemInfoModal button:hover {
        background-color: #2980b9 !important;
        transform: translateY(-1px);
    }

    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: scale(0.9);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Responsive específico del footer */
    @media (max-width: 768px) {
        .footer-content {
            grid-template-columns: 1fr;
            text-align: center;
            gap: 25px;
        }

        .footer-section h3 {
            font-size: 1rem;
        }

        .footer-section p,
        .footer-section ul {
            font-size: 0.85rem;
        }

        .footer-bottom p {
            font-size: 0.8rem;
            line-height: 1.4;
        }

        #systemInfoModal>div {
            margin: 20px;
            padding: 20px;
        }

        #systemInfoModal div[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important;
            gap: 15px !important;
        }
    }

    @media (max-width: 480px) {
        .footer-container {
            padding: 20px 15px;
        }

        .footer-section {
            margin-bottom: 20px;
        }

        .footer-bottom {
            padding-top: 15px;
        }

        #systemInfoModal>div {
            width: 95%;
            max-height: 90%;
        }
    }

    /* Modo oscuro para el modal */
    @media (prefers-color-scheme: dark) {
        #systemInfoModal>div {
            background: #2c3e50;
            color: white;
        }

        #systemInfoModal h3 {
            color: #3498db;
        }

        #systemInfoModal div[style*="background: #f8f9fa"] {
            background: #34495e !important;
        }
    }

    /* Mejoras de accesibilidad */
    .footer-section a:focus,
    #systemInfoModal button:focus {
        outline: 2px solid #3498db;
        outline-offset: 2px;
    }

    /* Hover effects mejorados */
    .footer-section li {
        transition: transform 0.2s ease;
    }

    .footer-section li:hover {
        transform: translateX(5px);
    }

    /* Estados de los enlaces */
    .footer-section a[onclick="return false;"] {
        cursor: not-allowed;
        position: relative;
    }

    .footer-section a[onclick="return false;"]:hover::after {
        width: 0;
    }

    /* Animación para el footer bottom */
    .footer-bottom {
        animation: fadeIn 1s ease-out 0.5s both;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }
</style>

<script>
    // Funciones para el modal de información del sistema
    function showSystemInfo() {
        const modal = document.getElementById('systemInfoModal');
        if (modal) {
            modal.style.display = 'flex';
            modal.style.animation = 'modalFadeIn 0.3s ease';
            document.body.style.overflow = 'hidden';

            // Focus en el botón de cerrar para accesibilidad
            const closeBtn = modal.querySelector('button');
            if (closeBtn) {
                closeBtn.focus();
            }
        }
    }

    function closeSystemInfo() {
        const modal = document.getElementById('systemInfoModal');
        if (modal) {
            modal.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }, 300);
        }
    }

    // Inicialización del footer
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('systemInfoModal');

        if (modal) {
            // Cerrar modal al hacer clic fuera
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeSystemInfo();
                }
            });

            // Cerrar modal con Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal && modal.style.display === 'flex') {
                    closeSystemInfo();
                }
            });
        }

        // Animación de entrada para los elementos del footer
        const footerSections = document.querySelectorAll('.footer-section');
        footerSections.forEach((section, index) => {
            section.style.animationDelay = `${index * 0.1}s`;
        });

        // Efecto parallax sutil en el footer
        window.addEventListener('scroll', function() {
            const footer = document.querySelector('footer');
            if (footer) {
                const scrolled = window.pageYOffset;
                const parallax = scrolled * 0.1;
                footer.style.transform = `translateY(${parallax}px)`;
            }
        });

        // Detectar si estamos en una página del sistema para funcionalidades adicionales
        const isSystemPage = <?php echo $is_system_page ? 'true' : 'false'; ?>;

        if (isSystemPage) {
            // Auto-actualizar el estado del sistema cada 30 segundos
            setInterval(function() {
                const statusIndicator = document.querySelector('.system-status');
                if (statusIndicator) {
                    // Simular verificación de estado
                    statusIndicator.style.backgroundColor = '#27ae60';
                    statusIndicator.style.animation = 'pulse 2s infinite';
                }
            }, 30000);

            // Mostrar información contextual en consola
            console.log('Sistema ESyA v2.0 - Policía de Tucumán');
            console.log('Estado: Operativo ✓');
            console.log('Desarrollado para la gestión académica interna');
        }
    });

    // Animaciones CSS adicionales
    const style = document.createElement('style');
    style.textContent = `
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
    
    /* Efecto de hover para todo el footer */
    footer:hover .footer-content {
        transform: translateY(-2px);
    }
    
    /* Animación de carga para el footer */
    footer {
        opacity: 0;
        animation: footerSlideUp 0.8s ease-out 0.3s forwards;
    }
    
    @keyframes footerSlideUp {
        from {
            opacity: 0;
            transform: translateY(50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
    document.head.appendChild(style);
</script>