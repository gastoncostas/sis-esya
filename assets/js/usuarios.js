document.addEventListener('DOMContentLoaded', function() {
    // Confirmación antes de eliminar
    const deleteButtons = document.querySelectorAll('.btn-delete');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('¿Está seguro que desea eliminar este usuario? Esta acción no se puede deshacer.')) {
                e.preventDefault();
            }
        });
    });
    
    // Deshabilitar clic en usuarios protegidos
    document.querySelectorAll('.protected').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Este usuario no puede ser eliminado');
        });
    });
});