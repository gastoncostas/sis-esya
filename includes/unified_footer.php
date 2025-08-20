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
                <?php else: ?>
                    <h3>Policía de Tucumán</h3>
                <?php endif; ?>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?= $current_year ?> Policía de Tucumán - Escuela de Suboficiales y Agentes "Agente Juan José Vides".</p>
        </div>
    </div>
</footer>

<?php if ($is_system_page): ?>
    <!-- Modal de información del sistema -->
    <div id="systemInfoModal" class="modal">
        <div class="modal-content">
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