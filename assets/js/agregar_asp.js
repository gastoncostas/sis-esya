// Validación del formulario
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    form.addEventListener('submit', function(e) {
        let valid = true;
        const dni = document.getElementById('dni').value.trim();
        const nombre = document.getElementById('nombre').value.trim();
        const apellido = document.getElementById('apellido').value.trim();
        
        // Validar DNI (solo números)
        if (!/^\d+$/.test(dni)) {
            alert('El DNI debe contener solo números');
            valid = false;
        }
        
        // Validar nombre y apellido (solo letras y espacios)
        if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(nombre)) {
            alert('El nombre solo puede contener letras y espacios');
            valid = false;
        }
        
        if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(apellido)) {
            alert('El apellido solo puede contener letras y espacios');
            valid = false;
        }
        
        if (!valid) {
            e.preventDefault();
        }
    });
    
    // Establecer fecha actual por defecto para fecha_ingreso
    const fechaIngreso = document.getElementById('fecha_ingreso');
    if (!fechaIngreso.value) {
        fechaIngreso.value = new Date().toISOString().split('T')[0];
    }
});
