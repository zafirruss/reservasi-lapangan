// File: assets/js/main.js
// Deskripsi: Skrip JavaScript utama untuk interaktivitas dan animasi.

document.addEventListener('DOMContentLoaded', function() {
    
    // Efek fade-in untuk elemen dengan class .fade-in (dinonaktifkan sementara untuk debugging flickering)
    /*
    const fadeElements = document.querySelectorAll('.card, .auth-card, .table-container, .form-container, .stat-card');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1
    });

    fadeElements.forEach(el => {
        // Inisialisasi style untuk animasi
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
        observer.observe(el);
    });
    */

    // Konfirmasi sebelum submit form berbahaya
    const confirmationForms = document.querySelectorAll('form[onsubmit*="confirm"]');
    confirmationForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const message = this.getAttribute('onsubmit').match(/confirm\('([^']*)'\)/)[1];
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

});
