$(document).ready(function () {

    // 🟢 Tombol tambah usulan → buka modal
    $('#btnTambahUsulan').on('click', function () {
        console.log("🟢 Tombol Tambah Usulan diklik");
        // 🧹 Reset form setiap kali modal dibuka
        $('#modalUsulanBansos').on('show.bs.modal', function() {
            console.log("🧹 Reset form modal sebelum ditampilkan");
            $('#formUsulanBansos')[0].reset();
            $('#nik_peserta').val(null).trigger('change'); // reset select2
            $('#program_bansos').val('').trigger('change');
            $('#kategori_desil').val('');
        });

        $('#modalUsulanBansos').modal('show');
    });

    // 🧩 Inisialisasi Select2 untuk NIK Peserta
    $('#nik_peserta').select2({
        dropdownParent: $('#modalUsulanBansos'),
        theme: 'bootstrap-5',
        placeholder: 'Ketik NIK atau Nama (min 3 huruf)',
        minimumInputLength: 3,
        width: '100%',
        ajax: {
            url: '/usulan-bansos/search-art',
            dataType: 'json',
            delay: 300,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                // ⛑️ Pastikan ambil dari data.results
                return {
                    results: data.results || []
                };
            },
            cache: true
        },
        templateResult: function (item) {
            if (!item.id) return item.text;

            const nama = item.nama ? item.nama.toUpperCase() : '';
            const sub = item.shdk_nama ? ` (${item.shdk_nama})` : '';
            const wilayah = (item.rw || item.rt) ? ` — RW ${item.rw} / RT ${item.rt}` : '';

            return $(`<div>${item.id} — <strong>${nama}</strong>${sub}<br><small class="text-muted">${wilayah}</small></div>`);
        },
        templateSelection: function (item) {
            if (!item.id) return item.text;
            return `${item.id} - ${item.nama ? item.nama.toUpperCase() : item.text}`;
        },
        escapeMarkup: function (m) { return m; }
    });

    // 🔄 Saat user pilih individu
$('#nik_peserta').on('select2:select', function (e) {
    const data = e.params.data;
    console.log("👤 Individu dipilih:", data);

    const nik = data.id;
    const shdk = parseInt(data.shdk || 0);

    $('#hidden_nik').val(nik);
    $('#hidden_shdk').val(shdk);

    // 🔹 Filter Program Bansos
    const programSelect = $('#program_bansos');
    programSelect.val('').trigger('change');
    programSelect.find('option').show();

    if (shdk !== 1 && shdk !== 3) {
        // selain KK & Istri, tampilkan hanya PBI
        programSelect.find('option').each(function () {
            if ($(this).text().toUpperCase().indexOf('PBI') === -1 && $(this).val() !== '') {
                $(this).hide();
            }
        });
        // pilih otomatis PBI jika tersedia
        const pbiOption = programSelect.find('option').filter(function() {
            return $(this).text().toUpperCase().includes('PBI');
        }).first();
        if (pbiOption.length) programSelect.val(pbiOption.val()).trigger('change');
    }

    // 🧭 Cek desil otomatis
    $.getJSON('/usulan-bansos/check-desil', { nik: nik })
        .done(function (res) {
            if (res.success) {
                const desil = res.kategori_desil;
                $('#kategori_desil').val(desil ?? '');

                if (desil === null || desil === '' || typeof desil === 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Layak',
                        text: 'Kategori desil belum tersedia untuk data ini.'
                    });
                } else if (parseInt(desil) <= 5) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Layak Diusulkan',
                        text: 'Kategori desil ' + desil + ' memenuhi syarat.'
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Layak',
                        text: 'Kategori desil ' + desil + ' di atas batas kelayakan.'
                    });
                }

            } else {
                $('#kategori_desil').val('');
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memeriksa Desil',
                    text: res.message || 'Tidak dapat memuat data desil.'
                });
            }
        })
        .fail(() => {
            Swal.fire({
                icon: 'error',
                title: 'Kesalahan Server',
                text: 'Tidak dapat terhubung ke server untuk memeriksa desil.'
            });
        });
});


    // 💾 Submit form
    $('#formUsulanBansos').on('submit', function (e) {
    e.preventDefault();

    const form = $(this);
    const btn = form.find('button[type="submit"]');
    btn.prop('disabled', true);

    $.ajax({
        url: '/usulan-bansos/save',
        method: 'POST',
        data: form.serialize(),
        dataType: 'json',
        success: function (res) {
            btn.prop('disabled', false);
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message || 'Usulan bansos berhasil disimpan.',
                    timer: 2000,
                    showConfirmButton: false
                });

                $('#modalUsulanBansos').modal('hide');
                $('#formUsulanBansos')[0].reset();

                // 🔄 refresh datatable tanpa reload halaman
                $(document).trigger('usulanBansosSaved');
                // 🔄 refresh datatable tanpa reload halaman
                if ($.fn.DataTable.isDataTable('#tableUsulanBansosDraft')) {
                    $('#tableUsulanBansosDraft').DataTable().ajax.reload(null, false);
                }
                if ($.fn.DataTable.isDataTable('#tableUsulanBansosVerified')) {
                    $('#tableUsulanBansosVerified').DataTable().ajax.reload(null, false);
                }
            } else {
                Swal.fire('Gagal', res.message || 'Gagal menyimpan usulan.', 'error');
            }
        },
        error: function (xhr) {
            btn.prop('disabled', false);
            Swal.fire('Kesalahan Server', 'Tidak dapat menyimpan data usulan.', 'error');
            console.error(xhr.responseText);
        }
    });
});

    // 🗑️ Hapus data usulan
    $(document).on('click', '.btnHapusUsulan', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Yakin hapus data ini?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/usulan-bansos/delete/' + id,
                    type: 'DELETE',
                    dataType: 'json',
                    success: function (res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: res.message
                            });
                            $('#tableUsulanBansos').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message
                            });
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan Server',
                            text: xhr.responseText || 'Gagal menghapus data.'
                        });
                    }
                });
            }
        });
    });

    // =====================================================
// ✅ Event klik tombol Verifikasi (ADMIN saja)
// =====================================================
$(document).on('click', '.btnVerifikasiUsulan', function () {
    const id = $(this).data('id');

    Swal.fire({
        title: 'Verifikasi Usulan?',
        text: 'Pastikan data ini sudah benar sebelum diverifikasi.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Verifikasi!',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33'
    }).then(result => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/usulan-bansos/verifikasi/${id}`,
                type: 'POST',
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message || 'Usulan berhasil diverifikasi.',
                            timer: 1800,
                            showConfirmButton: false
                        });

                        // 🔄 Refresh kedua tabel
                        if ($.fn.DataTable.isDataTable('#tableUsulanBansosDraft')) {
                            $('#tableUsulanBansosDraft').DataTable().ajax.reload(null, false);
                        }
                        if ($.fn.DataTable.isDataTable('#tableUsulanBansosVerified')) {
                            $('#tableUsulanBansosVerified').DataTable().ajax.reload(null, false);
                        }
                    } else {
                        Swal.fire('Gagal', res.message || 'Gagal memverifikasi usulan.', 'error');
                    }
                },
                error: function (xhr) {
                    Swal.fire('Error', 'Terjadi kesalahan koneksi ke server.', 'error');
                    console.error(xhr.responseText);
                }
            });
        }
    });
});

});
