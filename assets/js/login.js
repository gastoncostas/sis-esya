// Funcionalidad específica para la página de login
document.addEventListener('DOMContentLoaded', function() {
    // Efecto de enfoque en los campos del formulario
    const inputs = document.querySelectorAll('.form-group input');
    
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.01)';
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
    });
    
    // Animación de entrada
    const animateElements = () => {
        const container = document.querySelector('.login-container');
        if (container) {
            container.style.opacity = '0';
            container.style.transform = 'translateY(20px)';
            container.style.animation = 'fadeInUp 0.5s ease-out forwards';
        }
    };
    
    // Crear animación CSS dinámicamente
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    document.head.appendChild(style);
    
    animateElements();
});