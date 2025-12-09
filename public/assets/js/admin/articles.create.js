$(function () {

    $('#createArticleForm').on('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        formData.set('description', tinymce.get('articleCreateEditor').getContent());

        // Tambahkan CSRF
        formData.append($('#csrfName').val(), $('#csrfValue').val());

        $.ajax({
            url: '/admin/articles/store',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {

                // Refresh CSRF
                if (res.csrf) {
                    $('#csrfName').val(res.csrf.name);
                    $('#csrfValue').val(res.csrf.value);
                }

                if (res.success) {
                    Swal.fire('Berhasil', res.message, 'success');
                    $('#tab-list').trigger('click');
                    $('#tableArticles').DataTable().ajax.reload(null, false);
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            },
            error: function (err) {
                Swal.fire('Error', 'Gagal menyimpan artikel.', 'error');
                console.error(err.responseText);
            }
        });
    });

});
