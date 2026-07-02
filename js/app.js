/* =========================
   APP JS - Animaciones y mejoras
   ========================= */

document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // 1. AUTO-DISMISS ALERTAS con animación
    // ==========================================
    document.querySelectorAll('.alert-dismissible').forEach(function (alert) {
        setTimeout(function () {
            alert.classList.add('alert-hiding');
            setTimeout(function () {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 400);
        }, 4000);
    });

    // ==========================================
    // 2. ANIMACION FILAS al hacer hover en tabla
    // ==========================================
    document.querySelectorAll('table tbody tr').forEach(function (row) {
        row.addEventListener('mouseenter', function () {
            this.style.transition = 'background .15s ease, transform .2s ease';
            this.style.transform = 'scale(1.005)';
        });
        row.addEventListener('mouseleave', function () {
            this.style.transform = 'scale(1)';
        });
    });

    // ==========================================
    // 3. TOOLTIPS automaticos en botones
    // ==========================================
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (el) {
        new bootstrap.Tooltip(el);
    });

    // ==========================================
    // 4. ANIMACION de carga en sidebar
    // ==========================================
    document.querySelectorAll('.sidebar a').forEach(function (link, index) {
        link.style.opacity = '0';
        link.style.transform = 'translateX(-10px)';
        setTimeout(function () {
            link.style.transition = 'opacity .3s ease, transform .3s ease';
            link.style.opacity = '1';
            link.style.transform = 'translateX(0)';
        }, 100 + (index * 40));
    });

    // ==========================================
    // 5. TITULOS de páginas con fade-in
    // ==========================================
    document.querySelectorAll('h3').forEach(function (h3) {
        h3.style.transition = 'opacity .4s ease, transform .4s ease';
        h3.style.opacity = '0';
        h3.style.transform = 'translateY(-10px)';
        setTimeout(function () {
            h3.style.opacity = '1';
            h3.style.transform = 'translateY(0)';
        }, 50);
    });

    // ==========================================
    // 6. CONTADOR ANIMADO en tarjetas de reportes
    // ==========================================
    document.querySelectorAll('.card h2').forEach(function (counter) {
        var target = parseInt(counter.textContent.trim()) || 0;
        if (target === 0) return;
        var current = 0;
        var step = Math.max(1, Math.floor(target / 30));
        var interval = setInterval(function () {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(interval);
            }
            counter.textContent = current;
        }, 40);
    });

    // ==========================================
    // 7. CONFIRMACION antes de eliminar (extra)
    // ==========================================
    document.querySelectorAll('form[action*="eliminar"]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            var btn = this.querySelector('button[type="submit"]');
            if (btn && btn.dataset.confirmed !== 'true') {
                e.preventDefault();
                if (confirm('¿Estás seguro de realizar esta acción?')) {
                    btn.dataset.confirmed = 'true';
                    this.submit();
                }
            }
        });
    });

});
