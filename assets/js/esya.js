// Funcionalidad específica para la página ESyA
document.addEventListener('DOMContentLoaded', function() {
    // Efectos hover para tarjetas
    const cards = document.querySelectorAll('.division-card');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 8px 20px rgba(0, 0, 0, 0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
        });
    });
    
    // Animación de entrada
    const animateElements = () => {
        const elements = document.querySelectorAll('.welcome-section, .division-card');
        
        elements.forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.animation = `fadeInUp 0.5s ease-out ${index * 0.1}s forwards`;
        });
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