/**
 * Funcionalidad específica para la página Formación de Agentes
 */

document.addEventListener('DOMContentLoaded', function() {
    // Manejo del botón "En Desarrollo"
    const developmentBtn = document.getElementById('developmentBtn');
    if (developmentBtn) {
        developmentBtn.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Esta funcionalidad está actualmente en desarrollo y estará disponible próximamente');
        });
    }

    // Efectos hover para tarjetas
    const cards = document.querySelectorAll('.nav-card:not(.disabled)');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-0.5rem)';
            this.style.boxShadow = '0 0.5rem 1.25rem rgba(0, 0, 0, 0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 0.25rem 0.5rem rgba(0, 0, 0, 0.1)';
        });
    });

    // Animación de entrada
    const animateElements = () => {
        const elements = document.querySelectorAll('.welcome-section, .nav-card');
        elements.forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(1rem)';
            el.style.animation = `fadeInUp 0.5s ease-out ${index * 0.1}s forwards`;
        });
    };

    // Crear animación CSS dinámicamente
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(1rem);
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