console.log("[pemeriksaan.js] VERSION REFACTORED v1.0");

// ============================================================
//  GLOBAL NAMESPACE
// ============================================================
const $ = window.jQuery;
let offcanvasInstance = null;

// ============================================================
// 1. SAFE OFFCANVAS HANDLER (GLOBAL)
// ============================================================
function offcanvasClose() {
    const el = document.getElementById("offcanvasMaster");
    if (!el) return console.warn("[Offcanvas] Element not found");

    if (!window.BS5 || !BS5.Offcanvas)
        return console.warn("[Offcanvas] BS5.Offcanvas not available");

    try {
        const inst = BS5.Offcanvas.getInstance(el) || new BS5.Offcanvas(el);
        inst.hide();
    } catch (err) {
        console.error("offcanvasClose() failed:", err);
    }
}

// ============================================================
// 2. OPEN OFFCANVAS (MODULAR)
// ============================================================
function openOffcanvas(title, url) {
    const el = document.getElementById("offcanvasMaster");
    const body = document.getElementById("offcanvasBody");
    const header = document.getElementById("offcanvasTitle");

    if (!window.BS5 || !BS5.Offcanvas) {
        alert("Bootstrap 5 gagal dimuat. Offcanvas tidak tersedia.");
        return;
    }

    offcanvasInstance = new BS5.Offcanvas(el, {
        backdrop: true,
        scroll: true
    });

    header.innerHTML = title;
    body.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary"></div>
            <div class="mt-2">Memuat data...</div>
        </div>
    `;

    offcanvasInstance.show();

    // Load content
    fetch(url)
        .then(r => r.text())
        .then(html => {
            body.innerHTML = html;

            // Jika ART table ada → initialize
            if (document.querySelector("#tableART")) {
                setTimeout(initART, 200);
            }
        })
        .catch(err => {
            body.innerHTML =
                `<div class="alert alert-danger">Gagal memuat: ${err}</div>`;
        });
}

// ============================================================
// 3. DATATABLE INIT (MODULAR)
// ============================================================
// function initART() {
//     if (!$.fn.DataTable) return;

//     if ($.fn.DataTable.isDataTable("#tableART")) {
//         $("#tableART").DataTable().destroy();
//     }

//     $("#tableART").DataTable({
//         responsive: true,
//         pageLength: 50,
//         lengthChange: false,
//         ordering: true,
//         autoWidth: false,
//         language: {
//             url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
//         }
//     });

//     console.log("[ART-Table] Initialized");
// }
function initART(options = {}) {
    // Jika DataTables belum tersedia
    if (!$.fn.DataTable) return;

    // Jika ada instance lama, destroy dulu dan hapus referensi
    try {
        if ($.fn.DataTable.isDataTable('#tableART')) {
            $('#tableART').DataTable().destroy();
            $('#tableART').empty(); // bersihkan DOM table body agar tidak duplikat header
        }
    } catch (e) {
        console.warn("initART(): gagal destroy instance lama", e);
    }

    // Default settings — sesuaikan serverSide true kalau endpoint server-side
    const defaultOptions = {
        responsive: true,
        pageLength: 50,
        lengthChange: true,
        lengthMenu: [[10,25,50,100], [10,25,50,100]],
        ordering: true,
        autoWidth: false,
        processing: true,
        serverSide: true, // <-- PENTING: sesuaikan dengan behavior servermu
        ajax: { url: "/dtsen/pemeriksaan/listART", type: "POST" },
        columns: [
            { data: "nik" },
            { data: "nama" },
            { data: "shdk_nama" },
            { data: "jenis_kelamin" },
            { data: "age" },
            { data: "pendidikan" },
            { data: "pekerjaan" },
            { data: "disabilitas" },
            { data: "status_hamil" },
            { data: "ibu_kandung" },
            {
                data: null,
                orderable: false,
                className: "text-center",
                render: r => `
                    <button class="btn btn-info btn-sm btnViewART" data-id="${r.id_art}">Detail</button>
                    <button class="btn btn-warning btn-sm btnEditART" data-id="${r.id_art}">Edit</button>
                    <button class="btn btn-danger btn-sm btnDeleteART" data-id="${r.id_art}">Hapus</button>
                `
            }
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
        }
    };

    const cfg = $.extend(true, {}, defaultOptions, options);

    // Assign result ke window.tableART supaya global reference selalu up-to-date
    window.tableART = $('#tableART').DataTable(cfg);

    // jika responsive: adjust setelah inisialisasi
    if (cfg.responsive) {
        window.tableART.columns.adjust().responsive.recalc();
    }

    console.log("[initART] DataTable ART inited. serverSide:", !!cfg.serverSide);
    return window.tableART;
}

// ============================================================
//  MAIN CODE ENTRY
// ============================================================
document.addEventListener("DOMContentLoaded", function () {

    // ============================================================
    // 4. BADGE HELPERS
    // ============================================================
    const badgeOK = () => `<span class="badge bg-success">✔</span>`;
    const badgeX = () => `<span class="badge bg-danger">✘</span>`;

    // ============================================================
    // 5. INIT DT TABLE-KK
    // ============================================================
    window.tableKK = $("#tableKK").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: { url: "/dtsen/pemeriksaan/listKK", type: "POST" },
        columns: [
            { data: "no_kk" },
            { data: "kepala_keluarga" },
            { data: "alamat" },
            { data: "art_count", className: "text-center" },
            { data: "has_foto_kk", render: d => d ? badgeOK() : badgeX(), className: 'text-center' },
            { data: "has_foto_rumah", render: d => d ? badgeOK() : badgeX(), className: 'text-center' },
            { data: "has_foto_rumah_dalam", render: d => d ? badgeOK() : badgeX(), className: 'text-center' },
            { data: "nama_program" },
            {
                data: null,
                orderable: false,
                className: "text-center",
                render: r => `
                    <button class="btn btn-info btn-sm btnViewKK" data-id="${r.id_kk}">Detail</button>
                    <button class="btn btn-warning btn-sm btnEditKK" data-id="${r.id_kk}">Edit</button>
                    <button class="btn btn-danger btn-sm btnDeleteKK" data-id="${r.id_kk}">Hapus</button>
                `
            }
        ]
    });

    // ============================================================
    // 6. INIT DT TABLE-ART
    // ============================================================
    initART();

    // ============================================================
    // 7. FILTERS
    // ============================================================
    $(document).on("keyup", "#filter_kk_search", () => tableKK.search($("#filter_kk_search").val()).draw());
    $(document).on("keyup", "#filter_art_search", () => tableART.search($("#filter_art_search").val()).draw());

    // ============================================================
    // 8. BUTTON RELOAD
    // ============================================================
    // $(document).on("click", "#btnReloadKK", () => tableKK.ajax.reload(null, false));
    $(document).on("click", "#btnReloadKK", function () {
        if (window.tableKK && typeof window.tableKK.ajax === "object") {
            window.tableKK.ajax.reload(null, false);
        } else {
            // fallback atau reinit
            console.warn("btnReloadKK: tableKK belum siap");
        }
    });

    // $(document).on("click", "#btnReloadART", () => tableART.ajax.reload(null, false));
    $(document).on("click", "#btnReloadART", function () {
        if (window.tableART && typeof window.tableART.ajax === "object") {
            window.tableART.ajax.reload(null, false);
        } else {
            console.warn("btnReloadART: tableART belum siap, memanggil initART()");
            initART();
        }
    });


    // ============================================================
    // 9. VIEW + EDIT EVENTS
    // ============================================================
    $(document).on("click", ".btnViewKK", e => openOffcanvas("Detail KK", `/dtsen/kk/detail/${$(e.target).data("id")}`));
    $(document).on("click", ".btnEditKK", e => openOffcanvas("Edit KK", `/dtsen/kk/edit/${$(e.target).data("id")}`));
    $(document).on("click", ".btnViewART", e => openOffcanvas("Detail ART", `/dtsen/art/detail/${$(e.target).data("id")}`));
    $(document).on("click", ".btnEditART", e => openOffcanvas("Edit ART", `/dtsen/art/edit/${$(e.target).data("id")}`));

    // ============================================================
    // 10. SUBMIT FORM (EDIT KK/ART)
    // ============================================================
    document.addEventListener("submit", async function (e) {
        if (!["formEditKK", "formEditART"].includes(e.target.id)) return;

        e.preventDefault();

        const form = e.target;
        const btn = form.querySelector("button[type=submit]");
        btn.disabled = true;

        const fd = new FormData(form);
        const id = fd.get("id_kk") || fd.get("id_art");
        const isKK = form.id === "formEditKK";

        const url = isKK ? `/dtsen/kk/update/${id}` : `/dtsen/art/update/${id}`;

        let json;
        try {
            json = await fetch(url, { method: "POST", body: fd }).then(r => r.json());
        } catch (err) {
            Swal.fire("Error", "Tidak dapat mengirim data.", "error");
            btn.disabled = false;
            return;
        }

        if (!json.success) {
            Swal.fire("Gagal", json.message, "error");
            btn.disabled = false;
            return;
        }

        Swal.fire({
            icon: "success",
            title: "Berhasil",
            text: json.message,
            timer: 1500,
            showConfirmButton: false
        })
        // .then(() => {
        //     offcanvasClose();
        //     if (isKK) tableKK.ajax.reload(null, false);
        //     else tableART.ajax.reload(null, false);
        // });
        .then(() => {
            offcanvasClose();
            // reload instance terbaru (initART() selalu menyimpan ke window.tableART)
            if (isKK) {
                if (window.tableKK && window.tableKK.ajax) window.tableKK.ajax.reload(null, false);
            } else {
                if (window.tableART && window.tableART.ajax) window.tableART.ajax.reload(null, false);
            }
        });

        btn.disabled = false;
    });

    // ============================================================
    // 11. DELETE KK / ART
    // ============================================================
    $(document).on("click", ".btnDeleteKK", function () {
        const id = $(this).data("id");

        Swal.fire({
            title: "Hapus KK?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Hapus"
        }).then(async r => {
            if (!r.isConfirmed) return;
            const res = await fetch(`/dtsen/kk/delete/${id}`, { method: "POST" })
                .then(r => r.json());
            Swal.fire(res.success ? "Berhasil" : "Gagal", res.message, res.success ? "success" : "error");
            if (res.success) tableKK.ajax.reload(null, false);
        });
    });

    $(document).on("click", ".btnDeleteART", function () {
        const id = $(this).data("id");

        Swal.fire({
            title: "Hapus ART?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Hapus"
        }).then(async r => {
            if (!r.isConfirmed) return;
            const res = await fetch(`/dtsen/art/delete/${id}`, { method: "POST" })
                .then(r => r.json());
            Swal.fire(res.success ? "Berhasil" : "Gagal", res.message, res.success ? "success" : "error");
            if (res.success) tableART.ajax.reload(null, false);
        });
    });

});

// ============================================================
// 12. DATATABLE ADJUST WHEN OFFCANVAS OPEN
// ============================================================
document.addEventListener("shown.bs.offcanvas", function () {
    if ($.fn.DataTable.isDataTable("#tableART")) {
        const table = $("#tableART").DataTable();
        table.columns.adjust().responsive.recalc();
    }
});
