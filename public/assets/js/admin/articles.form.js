/**
 * ============================================================
 *  FORM EDIT ARTIKEL (AJAX + FORM DATA + CSRF AUTO FIX)
 * ============================================================
 */
$(document).on("submit", "#editArticleForm", function (e) {
    e.preventDefault();

    const id = $("#edit_id").val();

    // Ambil seluruh input form termasuk hidden CSRF + file
    let formData = new FormData(this);

    // Replace description dari TinyMCE
    formData.set("description", tinymce.get("articleEditEditor").getContent());

    $.ajax({
        url: "/admin/articles/update/" + id,
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,

        success: function (res, status, xhr) {

            // Perbarui token dari response header CI4
            let newToken = xhr.getResponseHeader("X-CSRF-TOKEN");
            if (newToken) {
                $('input[name="<?= csrf_token() ?>"]').val(newToken);
                $('meta[name="csrf-token-value"]').attr("content", newToken);
            }

            Swal.fire("Berhasil", res.message, "success").then(() => {
                window.location.href = "/admin/articles";
            });
        },

        error: function (xhr) {
            console.error("Update artikel error:", xhr);

            let msg = "Gagal update artikel";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                msg = xhr.responseJSON.message;
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

    $.get("/admin/articles/get/" + id, function (res) {

        if (!res.success) {
            Swal.fire("Error", res.message, "error");
            return;
        }

        const art = res.data;

        // Isi form input
        $("#edit_id").val(art.id);
        $("#edit_title").val(art.title);
        $("#edit_status").val(art.status);

        // Set konten TinyMCE
        if (tinymce.get("articleEditEditor")) {
            tinymce.get("articleEditEditor").setContent(art.description || "");
        }

        // Preview gambar
        $("#edit_image_preview").attr(
            "src",
            art.image_url || "/assets/images/image_not_available.jpg"
        );

        // Tampilkan tab edit
        $("#tab-edit").removeClass("d-none").click();
    });
});
