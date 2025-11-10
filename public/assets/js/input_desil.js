$(document).ready(function () {
    // 🔘 Fungsi untuk membuka modal dengan data KK
    $(document).on('click', '.btnInputDesil', function () {
        const idKk = $(this).data('id');
        const noKk = $(this).data('nokk');
        const kepala = $(this).data('nama');
        const alamat = $(this).data('alamat');
        const desil = $(this).data('desil') || '';

        $('#id_kk').val(idKk);
        $('#no_kk').val(noKk);
        $('#kepala_keluarga').val(kepala);
        $('#alamat').val(alamat);
        $('#kategori_desil').val(desil);

        $('#modalInputDesil').modal('show');
    });
    
    
});
// 💾 Simpan data ke backend
$('#formInputDesil').on('submit', function (e) {
    e.preventDefault();

    const formData = $(this).serialize();

    $.ajax({
        url: '/dtsen-se/update-desil',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function (res) {
            console.log('Response JSON:', res);
            if (res && res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Kategori desil berhasil diperbarui.',
                    timer: 1500,
                    showConfirmButton: false
                });
                $('#modalInputDesil').modal('hide');
                $('#tableKeluarga').DataTable().ajax.reload(null, false);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: res.message || 'Tidak dapat memperbarui data.',
                });
            }
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', error, xhr.responseText);

            // cek apakah error berasal dari ekstensi
            if (error && error.includes('content_script.js')) {
                console.warn('⚠️ Peringatan ekstensi browser terdeteksi, diabaikan.');
                return; // abaikan error dari ekstensi
            }

            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan saat memproses data.',
            });
        }
    });
});