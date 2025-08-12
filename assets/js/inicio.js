// Funcionalidad específica para la página de inicio
document.addEventListener('DOMContentLoaded', function() {
    const comingSoonBtn = document.getElementById('comingSoonBtn');
    
    if (comingSoonBtn) {
        comingSoonBtn.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Esta funcionalidad estará disponible próximamente');
        });
    }
    
    // Efecto de carga progresiva para las tarjetas
    const cards = document.querySelectorAll('.nav-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.animation = `fadeInUp 0.5s ease-out ${index * 0.1}s forwards`;
    });
});

// Animación para las tarjetas
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