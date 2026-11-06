/**
 * ============================================================
 *  FORM EDIT ARTIKEL (AJAX + FORM DATA + CSRF AUTO FIX)
 * ============================================================
 */
$(document).on("submit", "#editArticleForm", function (e) {
    e.preventDefault();

    const id = $("#edit_id").val();
    if (!id) {
        Swal.fire("Error", "ID artikel tidak valid.", "error");
        return;
    }

    if (typeof tinymce !== "undefined" && tinymce.get("articleEditEditor")) {
        tinymce.triggerSave();
    }

    // Ambil seluruh input form termasuk hidden CSRF + file
    let formData = new FormData(this);

    // Replace description dari TinyMCE
    if (typeof tinymce !== "undefined" && tinymce.get("articleEditEditor")) {
        formData.set("description", tinymce.get("articleEditEditor").getContent());
    }

    const csrfName = (window.CSRF && window.CSRF.name) 
        ? window.CSRF.name 
        : (document.querySelector('meta[name="csrf-token-name"]')?.content || 'csrf_test_name');
    const csrfValue = (window.CSRF && window.CSRF.value) 
        ? window.CSRF.value 
        : (document.querySelector('meta[name="csrf-token-value"]')?.content || '');

    if (csrfName && csrfValue) {
        formData.set(csrfName, csrfValue);
    }

    const submitBtn = $(this).find('button[type="submit"]');
    submitBtn.prop("disabled", true);

    $.ajax({
        url: "/admin/articles/update/" + id,
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,

        success: function (res, status, xhr) {
            submitBtn.prop("disabled", false);

            // Perbarui token dari response header CI4 atau payload
            let newToken = xhr.getResponseHeader("X-CSRF-TOKEN") || (res && (res.csrf_hash || (res.csrf && res.csrf.value)));
            if (newToken && typeof window.updateCsrfToken === "function") {
                window.updateCsrfToken(newToken);
            }

            Swal.fire("Berhasil", res.message || "Artikel berhasil diperbarui.", "success").then(() => {
                window.location.href = "/admin/articles";
            });
        },

        error: function (xhr) {
            submitBtn.prop("disabled", false);
            console.error("Update artikel error:", xhr);

            let newToken = xhr.getResponseHeader("X-CSRF-TOKEN");
            if (newToken && typeof window.updateCsrfToken === "function") {
                window.updateCsrfToken(newToken);
            }

            let msg = "Gagal update artikel";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.error) {
                msg = xhr.responseJSON.error;
            }

            Swal.fire("Error", msg, "error");
        }
    });
});


/**
 * ============================================================
 *  LOAD DATA EDIT ARTIKEL
 * ============================================================
 */
$(document).on("click", ".btnEditArticle", function () {

    const id = $(this).data("id");
    if (!id) return;

    $.get("/admin/articles/get/" + id, function (res) {

        if (!res.success) {
            Swal.fire("Error", res.message || "Data tidak ditemukan", "error");
            return;
        }

        const art = res.data;

        // Isi form input
        $("#edit_id").val(art.id);
        $("#edit_title").val(art.title);
        $("#edit_status").val(art.status);

        // Set konten TinyMCE
        if (typeof tinymce !== "undefined" && tinymce.get("articleEditEditor")) {
            tinymce.get("articleEditEditor").setContent(art.description || "");
        } else {
            $("#articleEditEditor").val(art.description || "");
        }

        // Preview gambar
        const initialImg = art.image_url || "/assets/images/image_not_available.jpg";
        $("#edit_image_preview").attr("src", initialImg).data("default-src", initialImg);
        $("#edit_image").val("");
        $("#edit_preview_note").html('<i class="fas fa-image me-1"></i> Gambar saat ini');

        // Tampilkan tab edit
        $("#tab-edit").removeClass("d-none");
        const tabTrigger = document.querySelector('#tab-edit');
        if (tabTrigger && typeof bootstrap !== "undefined" && bootstrap.Tab) {
            bootstrap.Tab.getOrCreateInstance(tabTrigger).show();
        } else {
            $("#tab-edit").trigger("click");
        }
    });
});

/**
 * ============================================================
 *  REALTIME PREVIEW GANTI GAMBAR ARTIKEL (EDIT)
 * ============================================================
 */
$(document).on("change", "#edit_image", function () {
    const file = this.files && this.files[0];
    const preview = $("#edit_image_preview");
    const note = $("#edit_preview_note");

    if (file && file.type.startsWith("image/")) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.attr("src", e.target.result);
            note.html('<i class="fas fa-eye me-1 text-primary"></i> <span class="text-primary font-weight-bold">Preview gambar baru terpilih</span> (klik Update untuk menyimpan)');
        };
        reader.readAsDataURL(file);
    } else {
        const defaultSrc = preview.data("default-src") || "/assets/images/image_not_available.jpg";
        preview.attr("src", defaultSrc);
        note.html('<i class="fas fa-image me-1"></i> Gambar saat ini');
    }
});
