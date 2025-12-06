console.log("[pemeriksaan] OFFCANVAS VERSION loaded");

document.addEventListener("DOMContentLoaded", function () {

    const $ = window.jQuery;

    // ============================================================
    // 1. BADGE HELPERS
    // ============================================================
    const badgeOK = () => `<span class="badge bg-success">✔</span>`;
    const badgeX  = () => `<span class="badge bg-danger">✘</span>`;

    // ============================================================
    // 2. INIT DATATABLES
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

    window.tableART = $("#tableART").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
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
        ]
    });

    // ============================================================
    // 3. SEARCH FILTERS
    // ============================================================
    $(document).on("keyup", "#filter_kk_search", function () {
        tableKK.search(this.value).draw();
    });

    $(document).on("keyup", "#filter_art_search", function () {
        tableART.search(this.value).draw();
    });

    // ============================================================
    // 4. BUTTON RELOAD
    // ============================================================
    $(document).on("click", "#btnReloadKK", () => tableKK.ajax.reload(null, false));
    $(document).on("click", "#btnReloadART", () => tableART.ajax.reload(null, false));

    // // ============================================================
    // // 5. OFFCANVAS HANDLER
    // // ============================================================
    // const offcanvasEl   = document.getElementById("offcanvasMaster");
    // const offcanvasBody = document.getElementById("offcanvasBody");
    // const offcanvasTitle= document.getElementById("offcanvasTitle");
    // const offcanvas     = new BS5.Offcanvas(offcanvasEl, { scroll: true, backdrop: true });

    // function openOffcanvas(title, url) {
    //     offcanvasTitle.innerHTML = title;
    //     offcanvasBody.innerHTML  = "Memuat...";

    //     offcanvas.show();

    //     fetch(url)
    //         .then(r => r.text())
    //         .then(html => offcanvasBody.innerHTML = html)
    //         .catch(() => offcanvasBody.innerHTML = "Gagal memuat.");
    // }

    // ============================================================
    // OFFCANVAS HANDLER — MIX BOOTSTRAP4 + BOOTSTRAP5
    // ============================================================

    const offcanvasEl    = document.getElementById("offcanvasMaster");
    const offcanvasBody  = document.getElementById("offcanvasBody");
    const offcanvasTitle = document.getElementById("offcanvasTitle");

    // Buat instance Offcanvas dari Bootstrap 5 (namespace BS5)
    let offcanvas = null;

    document.addEventListener("DOMContentLoaded", () => {
        if (typeof BS5 !== "undefined") {
            offcanvas = new BS5.Offcanvas(offcanvasEl, {
                scroll: true,
                backdrop: true
            });
            console.log("[OFFCANVAS] Bootstrap 5 aktif");
        } else {
            console.error("[OFFCANVAS] Bootstrap 5 tidak ditemukan!");
        }
    });

    function openOffcanvas(title, url) {

        const offcanvas = new BS5.Offcanvas(offcanvasEl, { backdrop: true, scroll: true });

        offcanvasTitle.innerHTML = title;
        offcanvasBody.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary"></div>
                <div class="mt-2">Memuat data...</div>
            </div>
        `;

        offcanvas.show();

        fetch(url)
            .then(r => r.text())
            .then(html => {
                offcanvasBody.innerHTML = html;

                if (document.querySelector("#tableART")) {
                    initART(); // Load DataTable ketika tabel sudah ada
                }
            })
            .catch(err => {
                offcanvasBody.innerHTML =
                    `<div class="alert alert-danger">Gagal memuat: ${err}</div>`;
            });
    }


    // ============================================================
    // 6. VIEW / EDIT EVENTS
    // ============================================================
    $(document).on("click", ".btnViewKK", function () {
        openOffcanvas("Detail KK", `/dtsen/kk/detail/${$(this).data("id")}`);
    });

    $(document).on("click", ".btnEditKK", function () {
        openOffcanvas("Edit KK", `/dtsen/kk/edit/${$(this).data("id")}`);
    });

    $(document).on("click", ".btnViewART", function () {
        openOffcanvas("Detail ART", `/dtsen/art/detail/${$(this).data("id")}`);
    });

    $(document).on("click", ".btnEditART", function () {
        openOffcanvas("Edit ART", `/dtsen/art/edit/${$(this).data("id")}`);
    });

    // ============================================================
    // 7. SUBMIT KK / ART UPDATE (OFFCANVAS FORM)
    // ============================================================
    document.addEventListener("submit", async function (e) {
        if (!["formEditKK", "formEditART"].includes(e.target.id)) return;
        e.preventDefault();

        const form = e.target;
        const btn  = form.querySelector("button[type=submit]");
        btn.disabled = true;

        const fd = new FormData(form);
        const id = fd.get("id_kk") || fd.get("id_art");
        const isKK = form.id === "formEditKK";
        const url  = isKK
            ? `/dtsen/kk/update/${id}`
            : `/dtsen/art/update/${id}`;

        let json;
        try {
            json = await fetch(url, { method: "POST", body: fd }).then(r => r.json());
        } catch {
            Swal.fire("Error", "Tidak dapat mengirim data.", "error");
            btn.disabled = false;
            return;
        }

        if (!json.success) {
            Swal.fire("Gagal", json.message, "error");
            btn.disabled = false;
            return;
        }

        offcanvas.hide();

        Swal.fire("Berhasil", json.message, "success").then(() => {
            if (isKK) tableKK.ajax.reload(null, false);
            else      tableART.ajax.reload(null, false);
        });

        btn.disabled = false;
    });

    // ============================================================
    // 8. DELETE KK / ART
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

            const res = await fetch(`/dtsen/kk/delete/${id}`, { method: "POST" }).then(r => r.json());

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

            const res = await fetch(`/dtsen/art/delete/${id}`, { method: "POST" }).then(r => r.json());

            Swal.fire(res.success ? "Berhasil" : "Gagal", res.message, res.success ? "success" : "error");
            if (res.success) tableART.ajax.reload(null, false);
        });
    });

});

document.addEventListener('shown.bs.offcanvas', function () {
    const table = $('#tableART').DataTable();
    table.columns.adjust().responsive.recalc();
});

function initART() {
    if (!$.fn.DataTable) return;

    if ($.fn.DataTable.isDataTable('#tableART')) {
        $('#tableART').DataTable().destroy();
    }

    return $('#tableART').DataTable({
        responsive: true,
        pageLength: 50,
        lengthChange: false,
        ordering: true,
        autoWidth: false,
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
        }
    });
}
