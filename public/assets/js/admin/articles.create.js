$(function () {

    $(document).on('submit', '#createArticleForm', function (e) {
        e.preventDefault();

        if (typeof tinymce !== 'undefined' && tinymce.get('articleCreateEditor')) {
            tinymce.triggerSave();
        }

        let formData = new FormData(this);
        if (typeof tinymce !== 'undefined' && tinymce.get('articleCreateEditor')) {
            formData.set('description', tinymce.get('articleCreateEditor').getContent());
        }

        // Tambahkan CSRF jika ada
        const csrfName = (window.CSRF && window.CSRF.name) 
            ? window.CSRF.name 
            : ($('meta[name="csrf-token-name"]').attr('content') || 'csrf_test_name');
        const csrfValue = (window.CSRF && window.CSRF.value) 
            ? window.CSRF.value 
            : ($('meta[name="csrf-token-value"]').attr('content') || '');

        if (csrfName && csrfValue) {
            formData.set(csrfName, csrfValue);
        }

        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true);

        $.ajax({
            url: '/admin/articles/store',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res, status, xhr) {
                submitBtn.prop('disabled', false);

                // Refresh CSRF
                let newToken = xhr.getResponseHeader('X-CSRF-TOKEN') || (res && (res.csrf_hash || (res.csrf && res.csrf.value)));
                if (newToken && typeof window.updateCsrfToken === 'function') {
                    window.updateCsrfToken(newToken);
                }

                if (res && res.success) {
                    Swal.fire('Berhasil', res.message || 'Artikel berhasil disimpan.', 'success').then(() => {
                        // Reset form & preview
                        const formEl = document.getElementById('createArticleForm');
                        if (formEl) formEl.reset();

                        $('#create_image_preview').attr('src', '');
                        $('#create_preview_wrapper').hide();

                        if (typeof tinymce !== 'undefined' && tinymce.get('articleCreateEditor')) {
                            tinymce.get('articleCreateEditor').setContent('');
                        }

                        $('#tab-list').trigger('click');
                        if ($.fn.DataTable.isDataTable('#tableArticles')) {
                            $('#tableArticles').DataTable().ajax.reload(null, false);
                        }
                    });
                } else {
                    Swal.fire('Gagal', (res && res.message) ? res.message : 'Gagal menyimpan artikel.', 'error');
                }
            },
            error: function (xhr) {
                submitBtn.prop('disabled', false);
                console.error('Create artikel error:', xhr);

                let newToken = xhr.getResponseHeader('X-CSRF-TOKEN');
                if (newToken && typeof window.updateCsrfToken === 'function') {
                    window.updateCsrfToken(newToken);
                }

                let msg = 'Gagal menyimpan artikel.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    msg = xhr.responseJSON.error;
                }
                Swal.fire('Error', msg, 'error');
            }
        });
    });

    // Realtime image preview saat memilih file gambar utama artikel baru
    $(document).on('change', '#create_image', function () {
        const file = this.files && this.files[0];
        const wrapper = $('#create_preview_wrapper');
        const img = $('#create_image_preview');

        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (e) {
                img.attr('src', e.target.result);
                wrapper.slideDown(200);
            };
            reader.readAsDataURL(file);
        } else {
            img.attr('src', '');
            wrapper.slideUp(200);
        }
    });

});
