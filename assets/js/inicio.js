document.addEventListener('DOMContentLoaded', function() {
    // Efecto hover mejorado para las tarjetas
    const navCards = document.querySelectorAll('.nav-card:not(.disabled)');

    navCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-15px) scale(1.02)';

            // Efecto en el icono
            const icon = this.querySelector('.card-icon');
            if (icon) {
                icon.style.transform = 'scale(1.1) rotate(5deg)';
            }
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';

            // Resetear icono
            const icon = this.querySelector('.card-icon');
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0deg)';
            }
        });
    });

    // Función para mostrar mensaje de "próximamente"
    window.showComingSoon = function() {
        const modal = document.createElement('div');
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        `;

        modal.innerHTML = `
            <div style="
                background: white;
                padding: 40px;
                border-radius: 15px;
                text-align: center;
                max-width: 400px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            ">
                <div style="font-size: 3rem; margin-bottom: 20px;">🚧</div>
                <h3 style="color: #2c3e50; margin-bottom: 15px;">Próximamente</h3>
                <p style="color: #7f8c8d; margin-bottom: 25px;">Esta funcionalidad estará disponible en una próxima actualización del sistema.</p>
                <button onclick="this.parentElement.parentElement.remove()" style="
                    background: #3498db;
                    color: white;
                    border: none;
                    padding: 12px 25px;
                    border-radius: 8px;
                    cursor: pointer;
                    font-weight: 600;
                ">Entendido</button>
            </div>
        `;

        document.body.appendChild(modal);
        modal.style.animation = 'fadeIn 0.3s ease';

        // Cerrar al hacer clic fuera
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.remove();
            }
        });
    };

    // Añadir animación de entrada escalonada
    const cards = document.querySelectorAll('.nav-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(50px)';

        setTimeout(() => {
            card.style.transition = 'all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 200 * (index + 1));
    });
});