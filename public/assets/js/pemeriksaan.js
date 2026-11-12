(function () {
    'use strict';

    const baseUrl = (window.baseUrl || '').replace(/\/$/, '');
    const state = {
        status: 'active',
        selectedKk: null,
        artOffset: 0,
        kkTotal: 0,
        artTotal: 0,
        kkSearch: '',
        artSearch: '',
        missingPhoto: false,
        lookupCache: {},
        selectedKks: new Set(),
        selectedArts: new Set()
    };
    const kkPageSize = 5;
    const artPageSize = 25;
    let kkTable;
    const workspaceSwal = window.Swal.mixin({
        customClass: {
            popup: 'pemeriksaan-swal-popup'
        },
        buttonsStyling: true
    });

    const el = (id) => document.getElementById(id);
    const escapeHtml = (value) => String(value == null ? '' : value)
        .replace(/[&<>"']/g, (char) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        })[char]);
    const endpoint = (path) => `${baseUrl}/dtsen/pemeriksaan/${path}`;
    const entityEndpoint = (entity, path) => `${baseUrl}/dtsen/${entity}/${path}`;

    function showSuccess(message) {
        return workspaceSwal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: message,
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
    }

    function showMessage(message, type) {
        if (type === 'success') return showSuccess(message);

        const icons = {
            error: 'error',
            warning: 'warning',
            info: 'info',
            question: 'question'
        };
        return workspaceSwal.fire({
            icon: icons[type] || 'info',
            text: message,
            confirmButtonText: 'Tutup'
        });
    }

    async function confirmArchive(message) {
        const result = await workspaceSwal.fire({
            icon: 'question',
            title: 'Konfirmasi pengarsipan',
            text: message,
            showCancelButton: true,
            confirmButtonText: 'Ya, arsipkan',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            focusCancel: true
        });
        return result.isConfirmed;
    }

    async function readJson(response) {
        let body;
        try {
            body = await response.json();
        } catch (error) {
            throw new Error('Respons server tidak dapat dibaca.');
        }
        if (!response.ok || body.success === false) {
            throw new Error(body.message || `Permintaan gagal (${response.status}).`);
        }
        return body;
    }

    async function getJson(url) {
        const response = await fetch(url, {
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        return readJson(response);
    }

    async function refreshCsrf() {
        try {
            const response = await fetch(endpoint('workspaceStats'), {
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const body = await readJson(response);
            if (body.csrfHash) {
                window.csrfHash = body.csrfHash;
                document.querySelectorAll(`input[name="${window.csrfName}"]`).forEach((input) => {
                    input.value = body.csrfHash;
                });
            }
        } catch (error) {
            console.warn('Tidak dapat memperbarui token keamanan:', error.message);
        }
    }

    async function postForm(url, formData) {
        if (window.csrfName && window.csrfHash && !formData.has(window.csrfName)) {
            formData.append(window.csrfName, window.csrfHash);
        }
        try {
            const response = await fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            return await readJson(response);
        } finally {
            await refreshCsrf();
        }
    }

    async function postAction(url, values) {
        const formData = new FormData();
        Object.keys(values).forEach((key) => formData.append(key, values[key]));
        return postForm(url, formData);
    }

    function setLoading(kind, loading) {
        const loadingElement = el(`${kind}ListLoading`);
        if (loadingElement) loadingElement.classList.toggle('d-none', !loading);
    }

    async function loadStats() {
        const result = await getJson(endpoint('workspaceStats'));
        const data = result.data || {};
        el('stat_total_kk').textContent = Number(data.total_kk || 0).toLocaleString('id-ID');
        el('stat_total_art').textContent = Number(data.total_art || 0).toLocaleString('id-ID');
        el('stat_archived_kk').textContent = Number(data.archived_kk || 0).toLocaleString('id-ID');
        el('stat_archived_art').textContent = Number(data.archived_art || 0).toLocaleString('id-ID');
        if (result.csrfHash) {
            window.csrfHash = result.csrfHash;
            document.querySelectorAll(`input[name="${window.csrfName}"]`).forEach((input) => {
                input.value = result.csrfHash;
            });
        }
    }

    function renderKkItem(row) {
        const isArchived = Boolean(row.deleted_at);
        const cannotRestore = state.status === 'archive' && !isArchived;
        const artCount = state.status === 'archive'
            ? Number(isArchived ? row.art_count : row.archived_art_count)
            : Number(row.active_art_count);
        const noKk = row.no_kk || '';
        const photoBadge = row.foto_kk
            ? '<span class="badge badge-light text-success"><i class="fas fa-image"></i> Foto</span>'
            : '<span class="badge badge-light text-warning"><i class="far fa-image"></i> Tanpa foto</span>';
        const statusBadge = isArchived
            ? '<span class="badge badge-warning">KK diarsipkan</span>'
            : (state.status === 'archive' ? '<span class="badge badge-info">ART diarsipkan</span>' : '<span class="badge badge-success">Aktif</span>');
        return `<span class="workspace-kk-title">${escapeHtml(row.kepala_keluarga || 'Tanpa nama')}</span>
            <span class="workspace-kk-meta"><span class="workspace-kk-number">${escapeHtml(noKk || 'No. KK tidak tersedia')}${noKk ? ` <button type="button" class="btn btn-link btn-sm p-0 js-copy-no-kk" data-no-kk="${escapeHtml(noKk)}" title="Salin nomor KK" aria-label="Salin nomor KK ${escapeHtml(noKk)}"><i class="far fa-copy"></i></button>` : ''}</span> · ${artCount} ART</span>
            <span class="workspace-kk-meta">${escapeHtml(row.alamat || 'Alamat belum diisi')}</span>
            <span class="workspace-kk-meta">${statusBadge} ${photoBadge}</span>`;
    }

    async function copyNoKk(value) {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(value);
            return;
        }

        const input = document.createElement('textarea');
        input.value = value;
        input.setAttribute('readonly', '');
        input.style.position = 'fixed';
        input.style.opacity = '0';
        document.body.appendChild(input);
        let copied = false;
        try {
            input.select();
            copied = document.execCommand('copy');
        } finally {
            input.remove();
        }
        if (!copied) throw new Error('Nomor KK tidak dapat disalin.');
    }

    function renderKkActions(row) {
        const isArchived = Boolean(row.deleted_at);
        const cannotRestore = state.status === 'archive' && !isArchived;
        const action = isArchived
            ? `<button type="button" class="btn btn-link btn-sm p-0 text-primary js-kk-archive" data-id="${Number(row.id_kk)}" data-action="restore" title="Pulihkan KK"><i class="fas fa-undo"></i></button>`
            : `<button type="button" class="btn btn-link btn-sm p-0 text-danger js-kk-archive" data-id="${Number(row.id_kk)}" data-action="archive" title="Arsipkan KK"><i class="fas fa-archive"></i></button>`;
        return `<div class="workspace-kk-actions">
                <input type="checkbox" class="js-select-kk" data-id="${Number(row.id_kk)}" ${state.selectedKks.has(Number(row.id_kk)) ? 'checked' : ''} ${cannotRestore ? 'disabled' : ''} aria-label="Pilih KK ${escapeHtml(row.no_kk)}">
                ${!isArchived ? `<button type="button" class="btn btn-link btn-sm p-0 text-primary js-edit-kk" data-id="${Number(row.id_kk)}" title="Edit KK"><i class="fas fa-pen"></i></button>` : ''}${action}
            </div>`;
    }

    function initKKTable() {
        kkTable = $('#kkTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: false,
            pageLength: kkPageSize,
            lengthChange: false,
            searching: true,
            order: [[1, 'asc']],
            dom: 't<"d-flex justify-content-center mt-2"p>',
            language: {
                processing: 'Memuat keluarga...',
                emptyTable: '',
                zeroRecords: 'Tidak ada keluarga pada filter ini.',
                paginate: {
                    previous: '<i class="fas fa-angle-left"></i>',
                    next: '<i class="fas fa-angle-right"></i>'
                }
            },
            ajax: {
                url: endpoint('listKK'),
                data: (request) => {
                    request.status = state.status;
                    if (state.missingPhoto && state.status === 'active') request.missing_photo = '1';
                },
                dataSrc: (result) => {
                    const rows = result.data || [];
                    state.kkTotal = Number(result.recordsFiltered || 0);
                    el('kkListEmpty').classList.toggle('d-none', rows.length > 0);
                    if (rows.length === 0) {
                        el('kkListEmpty').innerHTML = '<i class="far fa-folder-open"></i><span>Tidak ada keluarga pada filter ini.</span>';
                    }
                    return rows;
                },
                beforeSend: () => {
                    setLoading('kk', true);
                    el('kkListEmpty').classList.add('d-none');
                },
                complete: () => setLoading('kk', false),
                error: (xhr) => {
                    const message = xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : 'Daftar keluarga gagal dimuat.';
                    el('kkListEmpty').innerHTML = `<i class="fas fa-exclamation-circle text-danger"></i><span>${escapeHtml(message)}</span>`;
                    el('kkListEmpty').classList.remove('d-none');
                    showMessage(message, 'error');
                }
            },
            columns: [
                {
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    searchable: false,
                    render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1
                },
                {
                    data: null,
                    render: (data, type, row) => renderKkItem(row)
                },
                {
                    data: null,
                    className: 'text-right',
                    orderable: false,
                    searchable: false,
                    render: (data, type, row) => renderKkActions(row)
                }
            ],
            createdRow: (row, data) => {
                row.classList.add('workspace-kk-item');
                row.tabIndex = 0;
                row.dataset.id = Number(data.id_kk);
                row.dataset.noKk = data.no_kk || '';
                row.dataset.kkArchived = data.deleted_at ? '1' : '0';
                if (state.selectedKk && Number(state.selectedKk.id_kk) === Number(data.id_kk)) {
                    row.classList.add('is-selected');
                }
            },
            drawCallback: function () {
                const info = this.api().page.info();
                const firstRow = info.recordsDisplay ? info.start + 1 : 0;
                el('kkListSummary').textContent = `${firstRow}–${info.end} dari ${info.recordsDisplay.toLocaleString('id-ID')} keluarga`;
            }
        });
    }

    function loadKK(reset, clearSelection) {
        if (clearSelection) {
            state.selectedKks.clear();
            updateBulkToolbar('kk');
        }
        if (!kkTable) {
            return Promise.resolve();
        }
        kkTable.search(state.kkSearch);
        return new Promise((resolve) => {
            kkTable.ajax.reload(() => resolve(), reset !== false);
        });
    }

    function clearSelectedKk() {
        state.selectedKk = null;
        state.artOffset = 0;
        state.selectedArts.clear();
        el('panelARTTitle').textContent = 'Pilih keluarga untuk melihat anggota';
        el('panelKKMeta').textContent = 'Daftar anggota tampil sesuai status aktif atau arsip.';
        el('workspaceARTSearch').disabled = true;
        el('btnExportART').disabled = true;
        el('btnLoadMoreART').disabled = true;
        el('checkAllART').checked = false;
        el('checkAllART').disabled = true;
        el('artList').innerHTML = '';
        el('artListEmpty').classList.remove('d-none');
        el('artListEmpty').innerHTML = '<i class="fas fa-mouse-pointer"></i><span>Pilih satu keluarga di panel kiri.</span>';
        el('artListSummary').textContent = 'Belum ada data dipilih.';
        updateBulkToolbar('art');
    }

    function renderArtRow(row) {
        const isKkArchived = Boolean(state.selectedKk && state.selectedKk.deleted_at);
        const isArtArchived = Boolean(row.deleted_at);
        const archived = isArtArchived || isKkArchived;
        const selection = !isKkArchived
            ? `<input type="checkbox" class="js-select-art" data-id="${Number(row.id_art)}" ${state.selectedArts.has(Number(row.id_art)) ? 'checked' : ''} aria-label="Pilih ART ${escapeHtml(row.nama)}">`
            : '';
        const status = isKkArchived
            ? '<span class="badge badge-warning">Arsip KK</span>'
            : (isArtArchived ? '<span class="badge badge-warning">Diarsipkan</span>' : '<span class="badge badge-success">Aktif</span>');
        let actions = '<span class="text-muted">—</span>';
        if (!isKkArchived && isArtArchived) {
            actions = `<button type="button" class="btn btn-sm btn-outline-primary js-art-archive" data-id="${Number(row.id_art)}" data-action="restore" title="Pulihkan ART"><i class="fas fa-undo"></i></button>`;
        } else if (!archived) {
            actions = `<div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-primary js-edit-art" data-id="${Number(row.id_art)}" title="Edit"><i class="fas fa-pen"></i></button>
                <button type="button" class="btn btn-outline-secondary js-art-archive" data-id="${Number(row.id_art)}" data-action="archive" title="Arsipkan"><i class="fas fa-archive"></i></button>
            </div>`;
        }
        const age = row.age === null || row.age === undefined ? '—' : `${escapeHtml(row.age)} th`;
        return `<tr>
            <td class="text-center">${selection}</td>
            <td><span class="workspace-art-name">${escapeHtml(row.nama || 'Tanpa nama')}</span><span class="workspace-art-sub">${escapeHtml(row.nik || 'NIK belum diisi')}</span></td>
            <td>${escapeHtml(row.shdk_nama || row.shdk_value || '—')}</td>
            <td>${escapeHtml(row.jenis_kelamin || '—')} <span class="text-muted">/ ${age}</span></td>
            <td>${escapeHtml(row.pekerjaan_nama || row.pekerjaan || '—')}</td>
            <td>${status}</td>
            <td class="text-right">${actions}</td>
        </tr>`;
    }

    async function loadART(reset) {
        if (!state.selectedKk) return;
        if (reset) {
            state.artOffset = 0;
            state.selectedArts.clear();
            el('artList').innerHTML = '';
            el('checkAllART').checked = false;
            updateBulkToolbar('art');
        }
        setLoading('art', true);
        el('artListEmpty').classList.add('d-none');
        const params = new URLSearchParams({
            id_kk: String(state.selectedKk.id_kk),
            status: state.status,
            start: String(state.artOffset),
            length: String(artPageSize),
            'search[value]': state.artSearch
        });

        try {
            const result = await getJson(`${endpoint('listART')}?${params.toString()}`);
            const rows = result.data || [];
            state.artTotal = Number(result.recordsFiltered || 0);
            el('artList').insertAdjacentHTML('beforeend', rows.map(renderArtRow).join(''));
            el('panelARTTitle').textContent = `Anggota keluarga ${state.selectedKk.no_kk} · ${state.artTotal.toLocaleString('id-ID')} ART`;
            if (reset && rows.length === 0) {
                el('artListEmpty').classList.remove('d-none');
                el('artListEmpty').innerHTML = '<i class="far fa-folder-open"></i><span>Tidak ada ART pada filter ini.</span>';
            }
            state.artOffset += rows.length;
            el('checkAllART').disabled = rows.length === 0 || Boolean(state.selectedKk.deleted_at);
            el('artListSummary').textContent = `${state.artTotal.toLocaleString('id-ID')} ART`;
            el('btnLoadMoreART').disabled = state.artOffset >= state.artTotal;
            el('btnLoadMoreART').classList.toggle('d-none', state.artOffset >= state.artTotal);
        } catch (error) {
            if (reset) el('artList').innerHTML = '';
            el('artListEmpty').classList.remove('d-none');
            el('artListEmpty').innerHTML = `<i class="fas fa-exclamation-circle text-danger"></i><span>${escapeHtml(error.message)}</span>`;
            showMessage(error.message, 'error');
        } finally {
            setLoading('art', false);
        }
    }

    function selectKk(id) {
        const row = document.querySelector(`.workspace-kk-item[data-id="${Number(id)}"]`);
        if (!row) return;
        document.querySelectorAll('.workspace-kk-item').forEach((item) => item.classList.remove('is-selected'));
        row.classList.add('is-selected');
        const isArchived = row.dataset.kkArchived === '1';
        state.selectedKk = {
            id_kk: Number(id),
            no_kk: row.dataset.noKk || '',
            deleted_at: isArchived ? 'archived' : null
        };
        el('panelARTTitle').textContent = `Anggota keluarga ${state.selectedKk.no_kk}`;
        el('panelKKMeta').textContent = isArchived
            ? 'Keluarga berada di arsip. Pulihkan KK untuk mengubah data anggotanya.'
            : 'Gunakan aksi pada setiap ART untuk memperbarui atau mengarsipkan data.';
        el('workspaceARTSearch').disabled = false;
        el('btnExportART').disabled = false;
        state.artSearch = '';
        el('workspaceARTSearch').value = '';
        loadART(true);
    }

    function updateBulkToolbar(entity) {
        const selected = entity === 'kk' ? state.selectedKks : state.selectedArts;
        const toolbar = el(entity === 'kk' ? 'kkBulkToolbar' : 'artBulkToolbar');
        const summary = el(entity === 'kk' ? 'kkSelectionSummary' : 'artSelectionSummary');
        const button = el(entity === 'kk' ? 'btnBulkKK' : 'btnBulkART');
        if (!toolbar || !summary || !button) return;
        const count = selected.size;
        toolbar.classList.toggle('d-none', count === 0);
        summary.textContent = `${count} dipilih`;
        const restore = state.status === 'archive';
        button.classList.toggle('btn-outline-danger', !restore);
        button.classList.toggle('btn-outline-primary', restore);
        const icon = button.querySelector('i, svg');
        if (icon) {
            icon.outerHTML = `<i class="fas ${restore ? 'fa-undo' : 'fa-archive'} mr-1"></i>`;
        }
        const label = button.querySelector('span');
        if (label) label.textContent = restore
            ? (entity === 'kk' ? 'Pulihkan KK' : 'Pulihkan ART')
            : (entity === 'kk' ? 'Arsipkan KK' : 'Arsipkan ART');
    }

    async function bulkArchive(entity) {
        const selected = entity === 'kk' ? state.selectedKks : state.selectedArts;
        if (!selected.size) return;
        const action = state.status === 'archive' ? 'restore' : 'archive';
        if (action === 'archive' && !await confirmArchive(`Arsipkan ${selected.size} data terpilih? Data dapat dipulihkan.`)) return;

        const formData = new FormData();
        formData.append('action', action);
        selected.forEach((id) => formData.append('ids[]', String(id)));
        try {
            const result = await postForm(endpoint(`bulk-archive/${entity}`), formData);
            showMessage(result.message, 'success');
            selected.clear();
            updateBulkToolbar(entity);
            await Promise.all([loadStats(), loadKK(false)]);
            if (state.selectedKk) {
                const selectedRow = document.querySelector(`.workspace-kk-item[data-id="${state.selectedKk.id_kk}"]`);
                if (selectedRow) {
                    selectKk(state.selectedKk.id_kk);
                } else if (entity === 'kk' || state.status === 'archive') {
                    clearSelectedKk();
                } else {
                    await loadART(true);
                }
            }
        } catch (error) {
            showMessage(error.message, 'error');
        }
    }

    function showModal(id) {
        const modal = el(id);
        if (window.bootstrap && window.bootstrap.Modal) {
            const Modal = window.bootstrap.Modal;
            const instance = Modal.getOrCreateInstance ? Modal.getOrCreateInstance(modal) : new Modal(modal);
            instance.show();
        } else if (window.jQuery && window.jQuery.fn.modal) {
            window.jQuery(modal).modal('show');
        } else {
            showMessage('Komponen modal belum tersedia.', 'error');
        }
    }

    function hideModal(id) {
        const modal = el(id);
        if (window.bootstrap && window.bootstrap.Modal) {
            const Modal = window.bootstrap.Modal;
            const instance = Modal.getInstance ? Modal.getInstance(modal) : null;
            if (instance) instance.hide();
        } else if (window.jQuery && window.jQuery.fn.modal) {
            window.jQuery(modal).modal('hide');
        }
    }

    function setSelectOptions(select, rows, selected, emptyLabel) {
        const options = [`<option value="">${escapeHtml(emptyLabel || 'Pilih...')}</option>`];
        (rows || []).forEach((row) => {
            options.push(`<option value="${escapeHtml(row.id)}">${escapeHtml(row.label)}</option>`);
        });
        select.innerHTML = options.join('');
        select.value = selected == null ? '' : String(selected);
    }

    async function loadLookup(path) {
        if (!state.lookupCache[path]) {
            const result = await getJson(endpoint(`lookups/${path}`));
            state.lookupCache[path] = result.data || [];
        }
        return state.lookupCache[path];
    }

    async function editKk(id) {
        try {
            const result = await getJson(entityEndpoint('kk', `edit/${id}`));
            const data = result.data;
            const form = el('formEditKKWorkspace');
            form.elements.id_kk.value = data.id_kk;
            form.elements.no_kk.value = data.no_kk || '';
            form.elements.kepala_keluarga.value = data.kepala_keluarga || '';
            form.elements.alamat.value = data.alamat || '';
            form.elements.program_bansos.value = data.program_bansos || '';
            form.elements.kategori_adat.value = data.kategori_adat || 'Tidak';
            showModal('modalEditKK');
        } catch (error) {
            showMessage(error.message, 'error');
        }
    }

    async function editArt(id) {
        try {
            const result = await getJson(entityEndpoint('art', `edit/${id}`));
            const data = result.data;
            const [shdk, education, jobs, disabilities] = await Promise.all([
                loadLookup('shdk'),
                loadLookup('pendidikan'),
                loadLookup('pekerjaan'),
                loadLookup('disabilitas')
            ]);
            setSelectOptions(el('editShdkWorkspace'), shdk, data.shdk, 'Pilih hubungan...');
            setSelectOptions(el('editPendidikanWorkspace'), education, data.pendidikan_terakhir, 'Pilih pendidikan...');
            setSelectOptions(el('editPekerjaanWorkspace'), jobs, data.pekerjaan, 'Pilih pekerjaan...');
            setSelectOptions(el('editDisabilitasWorkspace'), disabilities, data.disabilitas, 'Tidak ada');

            const form = el('formEditARTWorkspace');
            form.elements.id_art.value = data.id_art;
            form.elements.nik.value = data.nik || '';
            form.dataset.originalNik = data.nik || '';
            form.elements.nama.value = data.nama || '';
            form.elements.jenis_kelamin.value = data.jenis_kelamin || '';
            form.elements.tanggal_lahir.value = data.tanggal_lahir || '';
            form.elements.status_hamil.value = data.status_hamil || 'Tidak';
            form.elements.ibu_kandung.value = data.ibu_kandung || '';
            form.elements.reason_nik.value = '';
            el('reasonNikGroup').classList.toggle('d-none', (data.nik || '') === '');
            el('editARTNoKK').value = data.no_kk || '';
            showModal('modalEditART');
        } catch (error) {
            showMessage(error.message, 'error');
        }
    }

    function exportData(type) {
        const params = new URLSearchParams({
            type,
            status: state.status,
            q: type === 'kk' ? state.kkSearch : state.artSearch
        });
        if (type === 'art' && state.selectedKk) params.set('id_kk', state.selectedKk.id_kk);
        window.location.assign(`${endpoint('export')}?${params.toString()}`);
    }

    async function archiveItem(entity, id, action, triggerButton) {
        if (triggerButton && triggerButton.disabled) return;
        if (action === 'archive' && !await confirmArchive('Arsipkan data ini? Data tidak dihapus permanen dan dapat dipulihkan.')) return;
        if (triggerButton) triggerButton.disabled = true;
        try {
            const result = await postAction(entityEndpoint(entity, `archive/${id}`), { action });
            const numericId = Number(id);
            if (entity === 'art') {
                state.selectedArts.delete(numericId);
                updateBulkToolbar('art');
            } else {
                state.selectedKks.delete(numericId);
                updateBulkToolbar('kk');
            }
            showMessage(result.message, 'success');
            await Promise.all([loadStats(), loadKK(false)]);
            if (state.selectedKk) {
                const selectedRow = document.querySelector(`.workspace-kk-item[data-id="${state.selectedKk.id_kk}"]`);
                if (selectedRow) {
                    selectKk(state.selectedKk.id_kk);
                } else if (entity === 'kk' || state.status === 'archive') {
                    clearSelectedKk();
                } else {
                    await loadART(true);
                }
            }
        } catch (error) {
            showMessage(error.message, 'error');
        } finally {
            if (triggerButton && document.body.contains(triggerButton)) {
                triggerButton.disabled = false;
            }
        }
    }

    function debounce(callback, delay) {
        let timer;
        return function () {
            window.clearTimeout(timer);
            timer = window.setTimeout(callback, delay);
        };
    }

    function bindEvents() {
        document.addEventListener('click', (event) => {
            if (event.target.closest('.js-select-kk, .js-select-art')) return;
            const copyButton = event.target.closest('.js-copy-no-kk');
            if (copyButton) {
                event.preventDefault();
                event.stopPropagation();
                copyNoKk(copyButton.dataset.noKk)
                    .then(() => showSuccess('Nomor KK berhasil disalin.'))
                    .catch((error) => showMessage(error.message || 'Nomor KK tidak dapat disalin.', 'error'));
                return;
            }
            const statusButton = event.target.closest('.js-kk-status');
            if (statusButton) {
                state.status = statusButton.dataset.status;
                state.missingPhoto = false;
                el('btnKKNoFoto').classList.remove('active');
                document.querySelectorAll('.js-kk-status').forEach((button) => {
                    const active = button === statusButton;
                    button.classList.toggle('btn-primary', active);
                    button.classList.toggle('btn-outline-secondary', !active);
                });
                clearSelectedKk();
                loadKK(true, true);
                return;
            }

            const archiveButton = event.target.closest('.js-kk-archive, .js-art-archive');
            if (archiveButton) {
                event.stopPropagation();
                const entity = archiveButton.classList.contains('js-kk-archive') ? 'kk' : 'art';
                archiveItem(entity, archiveButton.dataset.id, archiveButton.dataset.action, archiveButton);
                return;
            }

            const editButton = event.target.closest('.js-edit-art');
            if (editButton) {
                editArt(editButton.dataset.id);
                return;
            }

            const editKkButton = event.target.closest('.js-edit-kk');
            if (editKkButton) {
                editKk(editKkButton.dataset.id);
                return;
            }

            const kkItem = event.target.closest('.workspace-kk-item');
            if (kkItem) {
                selectKk(kkItem.dataset.id);
            }
        });

        document.addEventListener('change', (event) => {
            const kkCheckbox = event.target.closest('.js-select-kk');
            if (kkCheckbox) {
                const id = Number(kkCheckbox.dataset.id);
                if (kkCheckbox.checked && state.selectedKks.size >= 100) {
                    kkCheckbox.checked = false;
                    showMessage('Pilih maksimal 100 data dalam satu aksi.', 'warning');
                    return;
                }
                if (kkCheckbox.checked) state.selectedKks.add(id);
                else state.selectedKks.delete(id);
                updateBulkToolbar('kk');
                return;
            }

            const artCheckbox = event.target.closest('.js-select-art');
            if (artCheckbox) {
                const id = Number(artCheckbox.dataset.id);
                if (artCheckbox.checked && state.selectedArts.size >= 100) {
                    artCheckbox.checked = false;
                    showMessage('Pilih maksimal 100 data dalam satu aksi.', 'warning');
                    return;
                }
                if (artCheckbox.checked) state.selectedArts.add(id);
                else state.selectedArts.delete(id);
                updateBulkToolbar('art');
                return;
            }

            if (event.target.id === 'checkAllART') {
                const visible = Array.from(document.querySelectorAll('.js-select-art'));
                let available = Math.max(0, 100 - state.selectedArts.size);
                visible.forEach((checkbox) => {
                    const id = Number(checkbox.dataset.id);
                    if (event.target.checked && !state.selectedArts.has(id) && available === 0) {
                        checkbox.checked = false;
                        return;
                    }
                    checkbox.checked = event.target.checked;
                    if (checkbox.checked) {
                        state.selectedArts.add(id);
                        available--;
                    } else {
                        state.selectedArts.delete(id);
                    }
                });
                if (event.target.checked && available === 0) {
                    showMessage('Maksimal 100 data dapat dipilih.', 'warning');
                }
                updateBulkToolbar('art');
            }
        });

        document.addEventListener('keydown', (event) => {
            if ((event.key === 'Enter' || event.key === ' ') && event.target.classList.contains('workspace-kk-item')) {
                event.preventDefault();
                selectKk(event.target.dataset.id);
            }
        });

        el('workspaceKKSearch').addEventListener('input', debounce(() => {
            state.kkSearch = el('workspaceKKSearch').value.trim();
            clearSelectedKk();
            loadKK(true, true);
        }, 300));
        el('workspaceARTSearch').addEventListener('input', debounce(() => {
            state.artSearch = el('workspaceARTSearch').value.trim();
            loadART(true);
        }, 300));
        el('btnKKSearchClear').addEventListener('click', () => {
            el('workspaceKKSearch').value = '';
            state.kkSearch = '';
            clearSelectedKk();
            loadKK(true, true);
        });
        el('btnARTSearchClear').addEventListener('click', () => {
            el('workspaceARTSearch').value = '';
            state.artSearch = '';
            loadART(true);
        });
        el('btnKKNoFoto').addEventListener('click', () => {
            if (state.status !== 'active') {
                showMessage('Filter foto tersedia untuk daftar KK aktif.', 'info');
                return;
            }
            state.missingPhoto = !state.missingPhoto;
            el('btnKKNoFoto').classList.toggle('active', state.missingPhoto);
            clearSelectedKk();
            loadKK(true, true);
        });
        el('btnLoadMoreART').addEventListener('click', () => loadART(false));
        el('btnBulkKK').addEventListener('click', () => bulkArchive('kk'));
        el('btnBulkART').addEventListener('click', () => bulkArchive('art'));
        el('btnRefreshWorkspace').addEventListener('click', async () => {
            try {
                await Promise.all([loadStats(), loadKK(false, true)]);
                if (state.selectedKk) {
                    const selectedRow = document.querySelector(`.workspace-kk-item[data-id="${state.selectedKk.id_kk}"]`);
                    if (selectedRow) selectKk(state.selectedKk.id_kk);
                    else await loadART(true);
                }
            } catch (error) {
                showMessage(error.message, 'error');
            }
        });
        el('btnExportKK').addEventListener('click', () => exportData('kk'));
        el('btnExportART').addEventListener('click', () => exportData('art'));

        el('formEditKKWorkspace').addEventListener('submit', async (event) => {
            event.preventDefault();
            const form = event.currentTarget;
            const id = form.elements.id_kk.value;
            try {
                const result = await postForm(entityEndpoint('kk', `update/${id}`), new FormData(form));
                hideModal('modalEditKK');
                showMessage(result.message, 'success');
                await Promise.all([loadStats(), loadKK(false, true)]);
                if (state.selectedKk) selectKk(state.selectedKk.id_kk);
            } catch (error) {
                showMessage(error.message, 'error');
            }
        });
        el('formEditARTWorkspace').addEventListener('submit', async (event) => {
            event.preventDefault();
            const form = event.currentTarget;
            const id = form.elements.id_art.value;
            try {
                const result = await postForm(entityEndpoint('art', `update/${id}`), new FormData(form));
                hideModal('modalEditART');
                showMessage(result.message, 'success');
                await Promise.all([loadStats(), loadKK(false)]);
                await loadART(true);
            } catch (error) {
                showMessage(error.message, 'error');
            }
        });
        el('editNIK').addEventListener('input', () => {
            const original = el('formEditARTWorkspace').dataset.originalNik || '';
            el('reasonNikGroup').classList.toggle('d-none', el('editNIK').value === original);
        });
    }

    document.addEventListener('DOMContentLoaded', async () => {
        bindEvents();
        try {
            await loadStats();
            initKKTable();
        } catch (error) {
            showMessage(error.message, 'error');
        }
    });
})();
