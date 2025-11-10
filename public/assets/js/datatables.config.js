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
        { className: 'dtr-control text-center', orderable: false, targets: 0, responsivePriority: 1 },
        { targets: 1, responsivePriority: 2 }, // No
        { targets: 2, responsivePriority: 1 }, // NIK
        { targets: 3, responsivePriority: 1 }, // Nama
        { targets: 4, responsivePriority: 3 },
        { targets: 5, responsivePriority: 4 },
        { targets: 6, responsivePriority: 5 },
        { targets: 7, responsivePriority: 6 },
        { targets: -1, orderable: false, responsivePriority: 1 } // Kolom Aksi
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
