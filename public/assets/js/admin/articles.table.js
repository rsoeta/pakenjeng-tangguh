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
            { data: 'actions', width: '120px', orderable:false }
        ],
        responsive: true,
        processing: true
    });

    // reload event
    $('#btnReloadArticles').on('click', () =>{
        table.ajax.reload(null,false);
    });

    // open edit form
    $(document).on('click', '.btnEditArticle', function () {
        const id = $(this).data('id');

        $.getJSON('/admin/articles/get/' + id)
            .done(res => {
                if (!res.success) {
                    Swal.fire('Error', res.message, 'error');
                    return;
                }

                const a = res.data;

                $('#edit_id').val(a.id);
                $('#edit_title').val(a.title);
                $('#edit_status').val(a.status);
                tinymce.get('articleEditEditor').setContent(a.description || '');

                $('#tab-edit').removeClass('d-none');
                $('a[href="#pane-edit"]').tab('show');
            });
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

            const csrfName  = $('meta[name="csrf-token-name"]').attr("content");
            const csrfValue = $('meta[name="csrf-token-value"]').attr("content");

            let payload = {};
            payload[csrfName] = csrfValue;

            $.ajax({
                url: "/admin/articles/delete/" + id,
                type: "POST",
                data: payload,
                success: function (res, status, xhr) {

                    // refresh token baru
                    let newToken = xhr.getResponseHeader("X-CSRF-TOKEN");
                    if (newToken) {
                        $('meta[name="csrf-token-value"]').attr("content", newToken);
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
                    Swal.fire("Error", "Gagal menghapus artikel.", "error");
                }
            });
        });
    });


});
