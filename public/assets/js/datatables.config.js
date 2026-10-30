/**
 * 📊 DataTables Global Configuration for SINDEN (AdminLTE compatible)
 * ---------------------------------------------------------------
 * - Aktifkan responsive collapse (tombol +)
 * - Atur prioritas kolom otomatis
 * - Pastikan tidak ada scroll horizontal
 * - Kompatibel dengan Bootstrap 5 & AdminLTE
 */

/**
 * 🔢 Format angka gaya Indonesia: pemisah ribuan titik (.)
 * Contoh: 2102 → "2.102"
 */
window.formatRibuanID = function (num) {
    if (num === null || num === undefined || num === '') return '0';
    var n = Number(num);
    if (isNaN(n)) return String(num);
    return n.toLocaleString('id-ID', { maximumFractionDigits: 2 });
};

$.extend(true, $.fn.dataTable.defaults, {
    // 🔢 Paksa seluruh angka DataTables (info "Menampilkan 1 sampai 10 dari 2.102", tombol halaman, dll.)
    // memakai pemisah ribuan titik — tidak tergantung file bahasa (i18n) yang dimuat via language.url
    formatNumber: function (toFormat) {
        return window.formatRibuanID(toFormat);
    },
    responsive: {
        details: {
            type: 'column',
            target: 0,
            renderer: function (api, rowIdx, columns) {
                var data = $.map(columns, function (col) {
                    // 🚀 Sembunyikan kolom kontrol, kolom tanpa judul, atau kolom bertanda 'never' (seperti kolom pencarian tersembunyi)
                    if (col.columnIndex === 0 || !col.title || col.title.trim() === '' || (col.className && col.className.indexOf('never') !== -1)) {
                        return '';
                    }
                    // 🚀 Bersihkan class text-center agar di tampilan mobile (child row) tidak berantakan/rata tengah
                    return '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
                        '<td class="fw-bold text-muted small text-start align-top" style="width: 130px; white-space: nowrap;">' + col.title + ':</td> ' +
                        '<td class="text-start align-middle">' + col.data + '</td>' +
                        '</tr>';
                }).join('');

                return data ? $('<table class="table table-sm table-borderless mb-0 dtr-details" width="100%"/>').append(data) : false;
            }
        }
    },
    columnDefs: [
        { className: 'dtr-control text-center', orderable: false, targets: 0, responsivePriority: 1 }, // Tombol + (Collapse)
        { targets: 1, responsivePriority: 2 }, // No
        { targets: 2, responsivePriority: 1 }, // NIK / Kolom Utama 1
        // 🚀 HAPUS targets: 3 agar tidak error pada tabel 3-kolom (Konsolidasi)
        { targets: -1, orderable: false, responsivePriority: 1 } // Kolom Aksi (Selalu paling kanan)
    ],
    autoWidth: false,
    scrollX: false,
    paging: true,
    pageLength: 10,
    order: [],
    language: {
        decimal: ",",
        thousands: ".",
        emptyTable: "Belum ada data.",
        search: "Cari:",
        lengthMenu: "Tampilkan _MENU_ data",
        paginate: {
            previous: "<i class='fas fa-angle-left'></i>",
            next: "<i class='fas fa-angle-right'></i>"
        }
    },
    drawCallback: function(settings) {
        // Recalculate responsive layout setiap kali redraw
        this.api().columns.adjust().responsive.recalc();
    }
});

/* 🧩 AdminLTE friendly styling fix */
$(document).on('shown.bs.tab', 'a[data-bs-toggle="tab"]', function(e) {
    $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive.recalc();
});

/* ========================================================================
 * 🚀 GLOBAL SHORTCUT: PENCARIAN DATATABLES ('/' atau 'Ctrl + /')
 * ======================================================================== */
$(document).on('keydown', function(e) {
    // Pemicu: Tombol '/' (Slash) ATAU kombinasi 'Ctrl + /'
    if (e.key === '/' || (e.ctrlKey && e.key === '/')) {
        
        // 1. 🛡️ ANTI-BENTROK: Jangan bajak tombol jika operator sedang asyik mengetik di dalam form!
        // Kecuali mereka sengaja menekan kombinasi paksa 'Ctrl + /'
        if (['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName)) {
            if (!e.ctrlKey) return; // Biarkan mereka mengetik garis miring (/) secara normal
        }

        // 2. 🎯 LACAK KOLOM PENCARIAN DATATABLES
        // DataTables secara default menggunakan class .dataTables_filter untuk bungkus pencariannya
        let $dtSearch = $('.dataTables_filter input:visible');

        if ($dtSearch.length > 0) {
            e.preventDefault(); // Cegah karakter '/' ikut tertulis di dalam kotak pencarian

            // Fokus ke kolom pencarian pertama yang aktif di layar
            let $targetInput = $dtSearch.first();
            $targetInput.focus();
            
            // 3. ✨ EFEK VISUAL: Beri denyut glow biru agar mata operator langsung tertuju ke sana
            $targetInput.css({
                'transition': 'all 0.3s ease',
                'box-shadow': '0 0 12px rgba(13, 110, 253, 0.8)',
                'border-color': '#0d6efd'
            });

            // Matikan glow-nya perlahan setelah 0.8 detik
            setTimeout(() => {
                $targetInput.css({
                    'box-shadow': '',
                    'border-color': ''
                });
            }, 800);
        }
    }
});