/* ============================================================
   GLOBAL DEADLINE HANDLER
   ============================================================ */

// Jadikan global
window.updateButtonState = function () {

    const btn = document.getElementById('btnTambahUsulan');
    if (!btn) return;

    fetch('/usulan-bansos/check-deadline')
        .then(r => r.json())
        .then(json => {
            const now = new Date(json.now || new Date());
            const start = json.start ? new Date(json.start) : null;
            const end = json.end ? new Date(json.end) : null;

            if (start && now < start) {
                btn.disabled = true;
                btn.title = "Pengajuan belum dibuka";
                return;
            }

            if (end && now > end) {
                btn.disabled = true;
                btn.title = "Masa pengajuan telah berakhir";
                return;
            }

            if (json.allowed) {
                btn.disabled = false;
                btn.title = "";
            } else {
                btn.disabled = true;
            }
        })
        .catch(err => {
            console.error("updateButtonState error:", err);
            btn.disabled = true;
        });
};

// -------------------------
// Animate Digit
// -------------------------
window.animateDigit = function (id, newValue) {
    const el = document.getElementById(id);
    if (!el) return;

    if (el.innerText !== String(newValue)) {
        el.innerText = newValue;
        el.classList.add('animate');
        setTimeout(() => el.classList.remove('animate'), 160);
    }
};


// -------------------------
// Start Countdown
// -------------------------
window.startHeaderCountdown = function (endTimeString) {

    const end = new Date(endTimeString);

    const timer = setInterval(() => {

        const now = new Date();
        let diff = Math.floor((end - now) / 1000);

        if (diff <= 0) {
            clearInterval(timer);
            window.updateButtonState(); // <= NOW SAFE
            return;
        }

        const days = Math.floor(diff / 86400);
        diff %= 86400;

        const hours = Math.floor(diff / 3600);
        diff %= 3600;

        const minutes = Math.floor(diff / 60);
        const seconds = diff % 60;

        animateDigit("cdDays", days);
        animateDigit("cdHours", hours.toString().padStart(2, '0'));
        animateDigit("cdMinutes", minutes.toString().padStart(2, '0'));
        animateDigit("cdSeconds", seconds.toString().padStart(2, '0'));

    }, 1000);
};


// Jalankan countdown saat load file
fetch('/usulan-bansos/check-deadline')
    .then(r => r.json())
    .then(json => {
        if (json.end) startHeaderCountdown(json.end);
        updateButtonState();
    });
