(function () {
    'use strict';

    const baseUrl = (window.baseUrl || '').replace(/\/$/, '');
    const state = {
        status: 'active',
        selectedKk: null,
        kkPage: 1,
        kkOffset: 0,
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

    const el = (id) => document.getElementById(id);
    const escapeHtml = (value) => String(value == null ? '' : value)
        .replace(/[&<>"']/g, (char) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        })[char]);
    const endpoint = (path) => `${baseUrl}/dtsen/pemeriksaan/${path}`;
    const entityEndpoint = (entity, path) => `${baseUrl}/dtsen/${entity}/${path}`;

    function showMessage(message, type) {
        if (window.toastr && typeof window.toastr[type || 'info'] === 'function') {
            window.toastr[type || 'info'](message);
        } else {
            window.alert(message);
        }
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
        const isSelected = state.selectedKk && Number(state.selectedKk.id_kk) === Number(row.id_kk);
        const cannotRestore = state.status === 'archive' && !isArchived;
        const artCount = state.status === 'archive'
            ? Number(isArchived ? row.art_count : row.archived_art_count)
            : Number(row.active_art_count);
        const photoBadge = row.foto_kk
            ? '<span class="badge badge-light text-success"><i class="fas fa-image"></i> Foto</span>'
            : '<span class="badge badge-light text-warning"><i class="far fa-image"></i> Tanpa foto</span>';
        const statusBadge = isArchived
            ? '<span class="badge badge-warning">KK diarsipkan</span>'
            : (state.status === 'archive' ? '<span class="badge badge-info">ART diarsipkan</span>' : '<span class="badge badge-success">Aktif</span>');
        const action = isArchived
            ? `<button type="button" class="btn btn-link btn-sm p-0 text-primary js-kk-archive" data-id="${Number(row.id_kk)}" data-action="restore"><i class="fas fa-undo mr-1"></i>Pulihkan KK</button>`
            : `<button type="button" class="btn btn-link btn-sm p-0 text-danger js-kk-archive" data-id="${Number(row.id_kk)}" data-action="archive"><i class="fas fa-archive mr-1"></i>Arsipkan KK</button>`;

        return `<div class="workspace-kk-item${isSelected ? ' is-selected' : ''}" role="listitem" tabindex="0" data-id="${Number(row.id_kk)}" data-kk-archived="${isArchived ? '1' : '0'}">
            <div class="workspace-kk-title"><span>${escapeHtml(row.kepala_keluarga || 'Tanpa nama')}</span>${statusBadge}</div>
            <span class="workspace-kk-meta">${escapeHtml(row.no_kk || 'No. KK tidak tersedia')} · ${artCount} ART</span>
            <span class="workspace-kk-meta">${escapeHtml(row.alamat || 'Alamat belum diisi')}</span>
            <div class="workspace-kk-actions">
                <label class="mb-0 mr-auto" title="${cannotRestore ? 'Pilih KK yang diarsipkan untuk pemulihan massal' : 'Pilih untuk aksi massal'}">
                    <input type="checkbox" class="js-select-kk" data-id="${Number(row.id_kk)}" ${state.selectedKks.has(Number(row.id_kk)) ? 'checked' : ''} ${cannotRestore ? 'disabled' : ''} aria-label="Pilih KK ${escapeHtml(row.no_kk)}">
                </label>${photoBadge}${action}
            </div>
        </div>`;
    }

    function renderKkPagination() {
        const pagination = el('kkPagination');
        const pageCount = Math.ceil(state.kkTotal / kkPageSize);
        if (!pageCount) {
            pagination.innerHTML = '';
            return;
        }

        const startPage = Math.max(1, Math.min(state.kkPage - 2, pageCount - 4));
        const endPage = Math.min(pageCount, startPage + 4);
        const pages = [];
        pages.push(`<li class="page-item${state.kkPage === 1 ? ' disabled' : ''}">
            <button type="button" class="page-link" data-page="${state.kkPage - 1}" aria-label="Halaman sebelumnya">&laquo;</button>
        </li>`);
        for (let page = startPage; page <= endPage; page++) {
            pages.push(`<li class="page-item${page === state.kkPage ? ' active' : ''}">
                <button type="button" class="page-link" data-page="${page}"${page === state.kkPage ? ' aria-current="page"' : ''}>${page}</button>
            </li>`);
        }
        pages.push(`<li class="page-item${state.kkPage === pageCount ? ' disabled' : ''}">
            <button type="button" class="page-link" data-page="${state.kkPage + 1}" aria-label="Halaman berikutnya">&raquo;</button>
        </li>`);
        pagination.innerHTML = pages.join('');
    }

    async function loadKK(reset, clearSelection) {
        if (reset) {
            state.kkOffset = (state.kkPage - 1) * kkPageSize;
            if (clearSelection) state.selectedKks.clear();
            el('kkList').innerHTML = '';
            updateBulkToolbar('kk');
        }
        setLoading('kk', true);
        el('kkListEmpty').classList.add('d-none');
        const params = new URLSearchParams({
            status: state.status,
            start: String(state.kkOffset),
            length: String(kkPageSize),
            'search[value]': state.kkSearch
        });
        if (state.missingPhoto && state.status === 'active') {
            params.set('missing_photo', '1');
        }

        try {
            const result = await getJson(`${endpoint('listKK')}?${params.toString()}`);
            const rows = result.data || [];
            state.kkTotal = Number(result.recordsFiltered || 0);
            const pageCount = Math.ceil(state.kkTotal / kkPageSize);
            if (pageCount > 0 && state.kkPage > pageCount) {
                state.kkPage = pageCount;
                state.kkOffset = (state.kkPage - 1) * kkPageSize;
                return loadKK(true);
            }
            if (reset && state.selectedKk && state.status === 'active') {
                const stillVisible = rows.some((row) => Number(row.id_kk) === Number(state.selectedKk.id_kk));
                if (!stillVisible) clearSelectedKk();
            }
            el('kkList').insertAdjacentHTML('beforeend', rows.map(renderKkItem).join(''));
            if (reset && rows.length === 0) {
                el('kkListEmpty').classList.remove('d-none');
            }
            state.kkOffset += rows.length;
            attachKkEditButtons();
            const firstRow = state.kkTotal ? state.kkOffset - rows.length + 1 : 0;
            const lastRow = state.kkOffset;
            el('kkListSummary').textContent = `${firstRow}–${lastRow} dari ${state.kkTotal.toLocaleString('id-ID')} keluarga`;
            renderKkPagination();
        } catch (error) {
            if (reset) el('kkList').innerHTML = '';
            el('kkListEmpty').classList.remove('d-none');
            el('kkListEmpty').innerHTML = `<i class="fas fa-exclamation-circle text-danger"></i><span>${escapeHtml(error.message)}</span>`;
            showMessage(error.message, 'error');
        } finally {
            setLoading('kk', false);
        }
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
        const noKk = row.querySelector('.workspace-kk-meta');
        state.selectedKk = {
            id_kk: Number(id),
            no_kk: noKk ? noKk.textContent.split(' · ')[0] : '',
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
            icon.classList.toggle('fa-undo', restore);
            icon.classList.toggle('fa-archive', !restore);
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
        if (action === 'archive' && !window.confirm(`Arsipkan ${selected.size} data terpilih? Data dapat dipulihkan.`)) return;

        const formData = new FormData();
        formData.append('action', action);
        selected.forEach((id) => formData.append('ids[]', String(id)));
        try {
            const result = await postForm(endpoint(`bulk-archive/${entity}`), formData);
            showMessage(result.message, 'success');
            selected.clear();
            await Promise.all([loadStats(), loadKK(true)]);
            if (state.selectedKk) loadART(true);
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

    async function archiveItem(entity, id, action) {
        if (action === 'archive' && !window.confirm('Arsipkan data ini? Data tidak dihapus permanen dan dapat dipulihkan.')) return;
        try {
            const result = await postAction(entityEndpoint(entity, `archive/${id}`), { action });
            showMessage(result.message, 'success');
            await Promise.all([loadStats(), loadKK(true)]);
            if (state.selectedKk) {
                const selectedRow = document.querySelector(`.workspace-kk-item[data-id="${state.selectedKk.id_kk}"]`);
                if (!selectedRow) {
                    clearSelectedKk();
                } else {
                    selectKk(state.selectedKk.id_kk);
                }
            }
        } catch (error) {
            showMessage(error.message, 'error');
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
            const statusButton = event.target.closest('.js-kk-status');
            if (statusButton) {
                state.status = statusButton.dataset.status;
                state.missingPhoto = false;
                state.kkPage = 1;
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
                archiveItem(entity, archiveButton.dataset.id, archiveButton.dataset.action);
                return;
            }

            const editButton = event.target.closest('.js-edit-art');
            if (editButton) {
                editArt(editButton.dataset.id);
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
            state.kkPage = 1;
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
            state.kkPage = 1;
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
            state.kkPage = 1;
            el('btnKKNoFoto').classList.toggle('active', state.missingPhoto);
            clearSelectedKk();
            loadKK(true, true);
        });
        el('kkPagination').addEventListener('click', (event) => {
            const pageButton = event.target.closest('button[data-page]');
            if (!pageButton || !pageButton.closest('.page-item') || pageButton.closest('.page-item').classList.contains('disabled')) return;
            const page = Number(pageButton.dataset.page);
            const pageCount = Math.ceil(state.kkTotal / kkPageSize);
            if (!Number.isInteger(page) || page < 1 || page > pageCount || page === state.kkPage) return;
            state.kkPage = page;
            loadKK(true);
        });
        el('btnLoadMoreART').addEventListener('click', () => loadART(false));
        el('btnBulkKK').addEventListener('click', () => bulkArchive('kk'));
        el('btnBulkART').addEventListener('click', () => bulkArchive('art'));
        el('btnRefreshWorkspace').addEventListener('click', async () => {
            try {
                await loadStats();
                state.kkPage = 1;
                await loadKK(true, true);
                if (state.selectedKk) loadART(true);
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
                await Promise.all([loadStats(), loadKK(true, true)]);
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
                await loadStats();
                loadART(true);
            } catch (error) {
                showMessage(error.message, 'error');
            }
        });
        el('editNIK').addEventListener('input', () => {
            const original = el('formEditARTWorkspace').dataset.originalNik || '';
            el('reasonNikGroup').classList.toggle('d-none', el('editNIK').value === original);
        });
        document.addEventListener('click', (event) => {
            const editButton = event.target.closest('.js-edit-kk');
            if (editButton) editKk(editButton.dataset.id);
        });
    }

    function attachKkEditButtons() {
        document.querySelectorAll('.workspace-kk-item').forEach((item) => {
            const action = item.querySelector('.js-kk-archive');
            if (!action || action.dataset.action !== 'archive' || item.querySelector('.js-edit-kk')) return;
            const edit = document.createElement('button');
            edit.type = 'button';
            edit.className = 'btn btn-link btn-sm p-0 text-primary js-edit-kk';
            edit.dataset.id = item.dataset.id;
            edit.innerHTML = '<i class="fas fa-pen mr-1"></i>Edit KK';
            action.parentElement.insertBefore(edit, action);
        });
    }

    document.addEventListener('DOMContentLoaded', async () => {
        bindEvents();
        try {
            await loadStats();
            await loadKK(true);
        } catch (error) {
            showMessage(error.message, 'error');
        }
    });
})();
