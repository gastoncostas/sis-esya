// Funcionalidades básicas para mejorar la UX
document.addEventListener('DOMContentLoaded', function() {
  // Confirmación para acciones importantes
  const confirmActions = document.querySelectorAll('form[onsubmit]');
  confirmActions.forEach(form => {
    form.addEventListener('submit', function(e) {
      const message = this.getAttribute('data-confirm') || '¿Estás seguro de realizar esta acción?';
      if (!confirm(message)) {
        e.preventDefault();
      }
    });
  });
  
  // Mejorar los tooltips nativos
  const tooltips = document.querySelectorAll('[title]');
  tooltips.forEach(el => {
    el.addEventListener('mouseenter', function() {
      this.setAttribute('data-tooltip', this.getAttribute('title'));
      this.removeAttribute('title');
    });
    
    el.addEventListener('mouseleave', function() {
      this.setAttribute('title', this.getAttribute('data-tooltip'));
      this.removeAttribute('data-tooltip');
    });
  });
  
  // Cargar dinámicamente los avatares de usuario
  const userPills = document.querySelectorAll('.pill');
  userPills.forEach(pill => {
    const text = pill.textContent;
    if (text.includes('Admin') || text.includes('Administrador')) {
      pill.style.background = '#E53E3E';
      pill.style.color = 'white';
    } else if (text.includes('Profesor')) {
      pill.style.background = '#3182CE';
      pill.style.color = 'white';
    } else if (text.includes('Alumno')) {
      pill.style.background = '#38A169';
      pill.style.color = 'white';
    }
  });
});