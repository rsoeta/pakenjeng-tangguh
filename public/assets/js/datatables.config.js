/**
 * 📊 DataTables Global Configuration for SINDEN (AdminLTE compatible)
 * ---------------------------------------------------------------
 * - Aktifkan responsive collapse (tombol +)
 * - Atur prioritas kolom otomatis
 * - Pastikan tidak ada scroll horizontal
 * - Kompatibel dengan Bootstrap 5 & AdminLTE
 */

$.extend(true, $.fn.dataTable.defaults, {
    responsive: {
        details: {
            type: 'column',
            target: 0,
            renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                tableClass: 'table table-sm table-borderless mb-0'
            })
        }
    },
    columnDefs: [
        { className: 'dtr-control text-center', orderable: false, targets: 0, responsivePriority: 1 }, // Tombol + (Collapse)
        { targets: 1, responsivePriority: 2 }, // No
        { targets: 2, responsivePriority: 1 }, // NIK / Kolom Utama 1
        { targets: 3, responsivePriority: 1 }, // Nama / Kolom Utama 2
        // 🚀 HAPUS target 4, 5, 6, 7 agar tidak memicu error di tabel yang jumlah kolomnya sedikit
        { targets: -1, orderable: false, responsivePriority: 1 } // Kolom Aksi (Selalu paling kanan)
    ],
    autoWidth: false,
    scrollX: false,
    paging: true,
    pageLength: 5,
    order: [],
    language: {
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