// ========== main.js - LaporLingkungan Jogja ==========

document.addEventListener("DOMContentLoaded", function () {

    // --- AUTO-HIDE NOTIFICATION ---
    const alerts = document.querySelectorAll("#formAlert, .auto-hide-alert");
    alerts.forEach(alert => {
        if (alert.innerText.trim() !== "") {
            setTimeout(() => { alert.style.opacity = "0"; }, 3100);
            setTimeout(() => { alert.innerText = ""; }, 3600);
        }
    });

    // --- PROFILE DROPDOWN (Desktop & Mobile) ---
    document.querySelectorAll('.profile-menu').forEach(menu => {
        menu.addEventListener('click', function (e) {
            let drop = menu.querySelector('.dropdown-content');
            if (drop) {
                drop.style.display = (drop.style.display === "block") ? "none" : "block";
                e.stopPropagation();
            }
        });
    });
    document.addEventListener('click', function () {
        document.querySelectorAll('.dropdown-content').forEach(dc => dc.style.display = "none");
    });

    // --- FILE LAMPIRAN PREVIEW NAMA ---
    const lampiranInput = document.getElementById('lampiran');
    if (lampiranInput) {
        lampiranInput.addEventListener('change', function () {
            const fileLabel = document.getElementById('lampiranLabel');
            if (fileLabel && lampiranInput.files.length > 0) {
                fileLabel.innerText = "File: " + lampiranInput.files[0].name;
            }
        });
    }

    // --- FORM VALIDATION (LOGIN, REGISTER, PENGADUAN) ---
    const forms = document.querySelectorAll('.form-card form, .pengaduan-form form');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            let valid = true;
            const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
            inputs.forEach(input => {
                if (input.value.trim() === "") {
                    input.style.borderColor = "#ff7f21";
                    valid = false;
                } else {
                    input.style.borderColor = "#b6bfe2";
                }
            });
            if (!valid) {
                e.preventDefault();
                const alert = document.getElementById("formAlert");
                if (alert) {
                    alert.classList.add("shake");
                    setTimeout(() => alert.classList.remove("shake"), 400);
                }
            }
        });
    });

    // --- LOGOUT CONFIRMATION (for button with id logoutBtn) ---
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function (e) {
            if (!confirm("Yakin ingin logout?")) {
                e.preventDefault();
            }
        });
    }

    // --- FAQ TOGGLE / ACCORDION ---
    document.querySelectorAll('.faq-question').forEach(function (btn) {
        btn.addEventListener('click', function () {
            let answer = this.nextElementSibling;
            let isOpen = answer.style.display === 'block';
            // Tutup semua jawaban lain (jika mau satu saja yang terbuka)
            document.querySelectorAll('.faq-answer').forEach(function (ans) { ans.style.display = 'none'; });
            // Toggle jawaban ini
            answer.style.display = isOpen ? 'none' : 'block';
        });
    });

    // --- SMOOTH SCROLL untuk link anchor (#section) ---
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    // --- TOMBOL COPY KODE PENGADUAN (jika ada) ---
    document.querySelectorAll('.copy-kode-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const kode = btn.dataset.kode || btn.innerText;
            navigator.clipboard.writeText(kode);
            btn.innerText = "✔ Disalin!";
            setTimeout(() => { btn.innerText = kode; }, 1400);
        });
    });

});
