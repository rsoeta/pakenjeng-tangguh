// assets/js/admin/articles.table.js
$(function () {

    // =============================
    // INIT DATATABLE (MAIN TABLE)
    // =============================
    const tableArticles = $('#tableArticles').DataTable({
        ajax: {
            url: '/admin/articles/data',
            type: 'GET',
            dataSrc: json => json.data || []
        },
        columns: [
            { data: 'no', width: '40px' },
            { 
                data: 'image', 
                width: '70px',
                render: img => img ? `<img src="${img}" class="img-thumbnail" style="height:55px;width:auto;">` : '-'
            },
            { data: 'title' },
            { 
                data: 'status', 
                width: '120px',
                render: s => `<span class="badge bg-${s === 'publish' ? 'success' : 'secondary'}">${s.toUpperCase()}</span>`
            },
            { 
                data: 'created_at', 
                width: '140px',
                render: d => d ? new Date(d).toLocaleString('id-ID') : '-' 
            },
            { data: 'actions', orderable: false, searchable: false, width: '120px' }
        ],
        createdRow: (row, data) => $(row).find('td').css('vertical-align','middle'),
        order: [[4, 'desc']],
        processing: true,
        responsive: true,
        language: { emptyTable: "Belum ada artikel." }
    });

    // =============================
    // RELOAD BUTTON
    // =============================
    $('#btnReloadArticles').on('click', function () {
        tableArticles.ajax.reload(null, false);
    });

    // =============================
    // EDIT HANDLER
    // =============================
    $(document).on('click', '.btnEditArticle', function () {
        const id = $(this).data('id');
        if (!id) return;

        $.getJSON('/admin/articles/get/' + id)
            .done(res => {
                if (!res.success) {
                    return Swal.fire('Gagal', res.message, 'error');
                }

                const art = res.data;

                $('#edit_id').val(art.id);
                $('#edit_title').val(art.title);
                $('#edit_status').val(art.status);

                if (tinymce.get('articleEditEditor')) {
                    tinymce.get('articleEditEditor').setContent(art.description ?? '');
                }

                $('a[href="#pane-edit"]').tab('show');
            })
            .fail(() => Swal.fire('Error', 'Tidak dapat mengambil data artikel.', 'error'));
    });

    // =============================
    // DELETE HANDLER
    // =============================
    $(document).on('click', '.btnDeleteArticle', function () {
        const id = $(this).data('id');
        if (!id) return;

        Swal.fire({
            title: 'Hapus Artikel?',
            text: 'Data akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then(res => {
            if (!res.isConfirmed) return;

            $.post('/admin/articles/delete/' + id)
                .done(r => {
                    if (r.success) {
                        Swal.fire('Terhapus', r.message, 'success');
                        tableArticles.ajax.reload(null, false);
                    } else {
                        Swal.fire('Gagal', r.message, 'error');
                    }
                })
                .fail(() => Swal.fire('Error', 'Gagal menghapus artikel.', 'error'));
        });
    });

});
