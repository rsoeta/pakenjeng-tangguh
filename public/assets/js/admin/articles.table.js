// assets/js/admin/articles.table.js

$(function () {
    const table = $('#tableArticles').DataTable({
        ajax: {
            url: '/admin/articles/data',
            type: 'GET',
            dataSrc: res => res.data || []
        },
        columns: [
            { data: 'no', width: '40px' },
            { data: 'image', width: '70px' },
            { data: 'title' },
            { data: 'status', width: '120px' },
            { data: 'created_at', width: '130px' },
            { data: 'actions', width: '135px', orderable:false }
        ],
        responsive: true,
        processing: true
    });

    // reload event
    $('#btnReloadArticles').on('click', () =>{
        table.ajax.reload(null,false);
    });

    // delete
    $(document).on("click", ".btnDeleteArticle", function () {
        const id = $(this).data("id");

        Swal.fire({
            title: "Hapus Artikel?",
            text: "Artikel akan dihapus permanen.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Hapus",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (!result.isConfirmed) return;

            const csrfName = (window.CSRF && window.CSRF.name) 
                ? window.CSRF.name 
                : ($('meta[name="csrf-token-name"]').attr("content") || 'csrf_test_name');
            const csrfValue = (window.CSRF && window.CSRF.value) 
                ? window.CSRF.value 
                : ($('meta[name="csrf-token-value"]').attr("content") || '');

            let payload = {};
            payload[csrfName] = csrfValue;

            $.ajax({
                url: "/admin/articles/delete/" + id,
                type: "POST",
                data: payload,
                success: function (res, status, xhr) {

                    // refresh token baru
                    let newToken = xhr.getResponseHeader("X-CSRF-TOKEN") || (res && res.csrf_hash);
                    if (newToken && typeof window.updateCsrfToken === "function") {
                        window.updateCsrfToken(newToken);
                    }

                    if (res.success) {
                        Swal.fire("Terhapus", res.message, "success");
                        $("#tableArticles").DataTable().ajax.reload(null, false);
                    } else {
                        Swal.fire("Gagal", res.message, "error");
                    }
                },
                error: function (xhr) {
                    console.error("Delete error:", xhr);
                    let newToken = xhr.getResponseHeader("X-CSRF-TOKEN");
                    if (newToken && typeof window.updateCsrfToken === "function") {
                        window.updateCsrfToken(newToken);
                    }
                    Swal.fire("Error", "Gagal menghapus artikel.", "error");
                }
            });
        });
    });


});
