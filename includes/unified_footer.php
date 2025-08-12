<?php
// Obtener el año actual
$current_year = date('Y');

// Determinar si es página del sistema
$is_system_page = false;
if (class_exists('Auth')) {
    try {
        $is_system_page = (new Auth())->isLoggedIn();
    } catch (Exception $e) {
        // No hacer nada, mantener false
    }
}
?>

<footer>
    <div class="footer-container">
        <div class="footer-content">
            <div class="footer-section">
                <?php if ($is_system_page): ?>
                    <h3>Sistema ESyA</h3>
                    <p>Sistema integral de gestión para la Escuela de Suboficiales y Agentes.</p>
                <?php else: ?>
                    <h3>Policía de Tucumán</h3>
                <?php endif; ?>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= $current_year ?> Policía de Tucumán - Escuela de Suboficiales y Agentes. Todos los derechos reservados.</p>
            <?php if (!$is_system_page): ?>
                <p>Jefatura de Educación y Capacitación - Formando el futuro de la seguridad</p>
            <?php endif; ?>
        </div>
    </div>
</footer>

<?php if ($is_system_page): ?>
    <!-- Modal de información del sistema -->
    <div id="systemInfoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Información del Sistema</h3>
                <button onclick="closeSystemInfo()" class="modal-close">&times;</button>
            </div>

            <div class="modal-body">
                <div class="system-info-grid">
                    <div>
                        <p><strong>Sistema:</strong><br><?= htmlspecialchars(defined('APP_NAME') ? APP_NAME : 'Sistema ESyA') ?></p>
                        <p><strong>Versión:</strong><br>2.0</p>
                        <p><strong>Base de Datos:</strong><br><?= htmlspecialchars(defined('DB_NAME') ? DB_NAME : 'MySQL') ?></p>
                    </div>
                    <div>
                        <p><strong>Servidor:</strong><br><?= htmlspecialchars($_SERVER['SERVER_NAME'] ?? 'localhost') ?></p>
                        <p><strong>PHP:</strong><br><?= phpversion() ?></p>
                        <p><strong>Estado:</strong><br><span class="status-active">✓ Operativo</span></p>
                    </div>
                </div>

                <div class="session-info">
                    <p><strong>Información de Sesión:</strong></p>
                    <?php if (isset($_SESSION['temp_auth'])): ?>
                        <p>Usuario: <?= htmlspecialchars($_SESSION['temp_auth']['username'] ?? 'N/A') ?></p>
                        <p>Rol: <?= htmlspecialchars($_SESSION['temp_auth']['rol'] ?? 'N/A') ?></p>
                        <p>Sesión iniciada: <?= date('d/m/Y H:i:s') ?></p>
                    <?php else: ?>
                        <p>No hay sesión activa</p>
                    <?php endif; ?>
                </div>

                <div class="modal-footer">
                    <p>Última actualización: Agosto <?= $current_year ?></p>
                    <p>Desarrollado para la Policía de Tucumán</p>
                </div>
            </div>

            <div class="modal-actions">
                <button onclick="closeSystemInfo()" class="btn-close">Cerrar</button>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    // Funciones para el modal
    function showSystemInfo() {
        const modal = document.getElementById('systemInfoModal');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            modal.querySelector('button')?.focus();
        }
    }

    function closeSystemInfo() {
        const modal = document.getElementById('systemInfoModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }

    // Eventos del modal
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('systemInfoModal');
        
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) closeSystemInfo();
            });
            
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.style.display === 'flex') {
                    closeSystemInfo();
                }
            });
        }
    });
</script>