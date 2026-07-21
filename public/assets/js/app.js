(function () {
    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function badgeClass(status) {
        switch ((status || '').toLowerCase()) {
            case 'active':
                return 'badge-soft-green';
            case 'transferred':
                return 'badge-soft-orange';
            case 'admitted':
                return 'badge-soft-yellow';
            case 'discharged':
                return 'badge-soft-gray';
            case 'deceased':
                return 'bg-dark';
            default:
                return 'badge-soft-gray';
        }
    }

    function getPatientInvestigationsHtml(patient) {
        if (!patient.investigations || patient.investigations.length === 0) {
            return '';
        }
        
        window.KEPTS_INVESTIGATIONS_CACHE = window.KEPTS_INVESTIGATIONS_CACHE || {};
        
        const max = 5;
        const visible = patient.investigations.slice(0, max);
        const remaining = patient.investigations.length - max;
        
        const badgesHtml = visible.map(inv => {
            window.KEPTS_INVESTIGATIONS_CACHE[inv.id] = inv;
            
            let color = '#6b7280';
            const status = (inv.status || '').toLowerCase();
            if (status === 'pending') color = '#eab308';
            else if (status === 'completed') color = '#16a34a';
            else if (status === 'cancelled') color = '#dc2626';
            else if (['sample taken', 'sent', 'in progress'].includes(status)) color = '#2563eb';
            
            return `
                <span class="badge cursor-pointer status-badge position-relative" 
                      data-id="${inv.id}" 
                      data-name="${escapeHtml(inv.investigation_type)}" 
                      data-status="${escapeHtml(inv.status)}" 
                      data-notes="${escapeHtml(inv.notes || '')}"
                      style="background: ${color}; color: #fff; font-size: 0.7rem; cursor: pointer; padding: 0.35em 0.7em;" 
                      title="${escapeHtml(inv.investigation_type)} (${escapeHtml(inv.status)})">
                    ${escapeHtml(inv.investigation_type)}
                </span>
            `;
        }).join(' ');

        const remainingBadge = remaining > 0 ? `<span class="badge bg-secondary" style="font-size: 0.7rem;">+${remaining} more</span>` : '';
        return `<div class="d-flex flex-wrap gap-1 mt-1 align-items-center">${badgesHtml}${remainingBadge}</div>`;
    }

    function renderWardRows(patients) {
        return patients.map((patient) => `
            <tr class="patient-ward-row patient-ward-row-${String(patient.current_ward || 'unassigned').toLowerCase().replaceAll(' ', '-')}">
                <td><span class="fw-semibold">${escapeHtml(patient.ghims_number)}</span></td>
                <td>
                    <div class="patient-name">${escapeHtml(patient.patient_name)}</div>
                    ${getPatientInvestigationsHtml(patient)}
                </td>
                <td>${escapeHtml(patient.age)}</td>
                <td>${escapeHtml(patient.chief_complaint)}</td>
                <td><span class="badge ${patient.assigned_team ? 'badge-soft-green' : 'badge-soft-gray'}">${escapeHtml(patient.assigned_team || 'Unassigned')}</span></td>
                <td>${escapeHtml(patient.time_in || '')}</td>
            </tr>
        `).join('');
    }

    function wardRowData(patients) {
        return patients.map((patient) => [
            `<span class="fw-semibold">${escapeHtml(patient.ghims_number)}</span>`,
            `<div class="patient-name">${escapeHtml(patient.patient_name)}</div>${getPatientInvestigationsHtml(patient)}`,
            escapeHtml(patient.age),
            escapeHtml(patient.chief_complaint),
            `<span class="badge ${patient.assigned_team ? 'badge-soft-green' : 'badge-soft-gray'}">${escapeHtml(patient.assigned_team || 'Unassigned')}</span>`,
            escapeHtml(patient.time_in || ''),
        ]);
    }

    function renderWardBoardRows(patients) {
        return patients.map((patient) => `
            <tr class="patient-ward-row patient-ward-row-${String(patient.current_ward || 'unassigned').toLowerCase().replaceAll(' ', '-')}">
                <td><span class="fw-semibold">${escapeHtml(patient.ghims_number)}</span></td>
                <td>
                    <div class="patient-name">${escapeHtml(patient.patient_name)}</div>
                    ${getPatientInvestigationsHtml(patient)}
                </td>
                <td><span class="badge ${patient.assigned_team ? 'badge-soft-green' : 'badge-soft-gray'}">${escapeHtml(patient.assigned_team || 'Unassigned')}</span></td>
                <td>${escapeHtml(patient.time_in || '')}</td>
            </tr>
        `).join('');
    }

    function wardBoardRowData(patients) {
        return patients.map((patient) => [
            `<span class="fw-semibold">${escapeHtml(patient.ghims_number)}</span>`,
            `<div class="patient-name">${escapeHtml(patient.patient_name)}</div>${getPatientInvestigationsHtml(patient)}`,
            `<span class="badge ${patient.assigned_team ? 'badge-soft-green' : 'badge-soft-gray'}">${escapeHtml(patient.assigned_team || 'Unassigned')}</span>`,
            escapeHtml(patient.time_in || ''),
        ]);
    }

    function renderWhiteBoardWardRows(patients) {
        return patients.map((patient) => `
            <tr class="patient-ward-row patient-ward-row-${String(patient.current_ward || 'unassigned').toLowerCase().replaceAll(' ', '-')}">
                <td><span class="fw-semibold">${escapeHtml(patient.ghims_number)}</span></td>
                <td>
                    <div class="patient-name">${escapeHtml(patient.patient_name)}</div>
                    ${getPatientInvestigationsHtml(patient)}
                </td>
                <td><span class="badge ${patient.assigned_team ? 'badge-soft-green' : 'badge-soft-gray'}">${escapeHtml(patient.assigned_team || 'Unassigned')}</span></td>
                <td>${escapeHtml(patient.nurse_notes ? String(patient.nurse_notes).slice(0, 80) : '—')}</td>
                <td>${escapeHtml(patient.ward_time || '—')}</td>
                <td>${escapeHtml(patient.time_in || '')}</td>
            </tr>
        `).join('');
    }

    function whiteBoardWardRowData(patients) {
        return patients.map((patient) => [
            `<span class="fw-semibold">${escapeHtml(patient.ghims_number)}</span>`,
            `<div class="patient-name">${escapeHtml(patient.patient_name)}</div>${getPatientInvestigationsHtml(patient)}`,
            `<span class="badge ${patient.assigned_team ? 'badge-soft-green' : 'badge-soft-gray'}">${escapeHtml(patient.assigned_team || 'Unassigned')}</span>`,
            escapeHtml(patient.nurse_notes ? String(patient.nurse_notes).slice(0, 80) : '—'),
            escapeHtml(patient.ward_time || '—'),
            escapeHtml(patient.time_in || ''),
        ]);
    }

    function renderSpecialtyRows(patients) {
        return patients.map((patient) => `
            <tr>
                <td><span class="fw-semibold">${escapeHtml(patient.ghims_number)}</span></td>
                <td>
                    <div class="patient-name">${escapeHtml(patient.patient_name)}</div>
                    ${getPatientInvestigationsHtml(patient)}
                </td>
                <td>${escapeHtml(patient.current_ward || '')}</td>
                <td>${escapeHtml(patient.chief_complaint)}</td>
                <td>${escapeHtml(patient.time_in || '')}</td>
            </tr>
        `).join('');
    }

    function specialtyRowData(patients) {
        return patients.map((patient) => [
            `<span class="fw-semibold">${escapeHtml(patient.ghims_number)}</span>`,
            `<div class="patient-name">${escapeHtml(patient.patient_name)}</div>${getPatientInvestigationsHtml(patient)}`,
            escapeHtml(patient.current_ward || ''),
            escapeHtml(patient.chief_complaint),
            escapeHtml(patient.time_in || ''),
        ]);
    }

    function renderWhiteBoardRows(patients) {
        return patients.map((patient) => {
            const wardClass = String(patient.current_ward || 'unassigned').toLowerCase().replaceAll(' ', '-');
            return `
            <tr class="patient-ward-row patient-ward-row-${escapeHtml(wardClass)}">
                <td><span class="fw-semibold">${escapeHtml(patient.ghims_number)}</span></td>
                <td>
                    <div class="patient-name">${escapeHtml(patient.patient_name)}</div>
                    ${getPatientInvestigationsHtml(patient)}
                </td>
                <td><span class="badge" style="background: ${escapeHtml(patient.ward_color || '#6b7280')}; color: #fff;">${escapeHtml(patient.current_ward || 'Unassigned')}</span></td>
                <td>${escapeHtml(patient.assigned_team || 'Unassigned')}</td>
                <td>${escapeHtml(patient.nurse_notes ? String(patient.nurse_notes).slice(0, 80) : '—')}</td>
                <td>${escapeHtml((patient.condition || 'unknown').charAt(0).toUpperCase() + (patient.condition || 'unknown').slice(1))}</td>
                <td>${escapeHtml(patient.ward_time || '—')}</td>
                <td><span class="badge ${badgeClass(patient.status)}">${escapeHtml((patient.status || '').charAt(0).toUpperCase() + (patient.status || '').slice(1))}</span></td>
                <td>${escapeHtml(patient.time_in || '')}</td>
            </tr>
        `;
        }).join('');
    }

    function whiteBoardRowData(patients) {
        return patients.map((patient) => [
            `<span class="fw-semibold">${escapeHtml(patient.ghims_number)}</span>`,
            `<div class="patient-name">${escapeHtml(patient.patient_name)}</div>${getPatientInvestigationsHtml(patient)}`,
            `<span class="badge" style="background: ${escapeHtml(patient.ward_color || '#6b7280')}; color: #fff;">${escapeHtml(patient.current_ward || 'Unassigned')}</span>`,
            escapeHtml(patient.assigned_team || 'Unassigned'),
            escapeHtml(patient.nurse_notes ? String(patient.nurse_notes).slice(0, 80) : '—'),
            escapeHtml((patient.condition || 'unknown').charAt(0).toUpperCase() + (patient.condition || 'unknown').slice(1)),
            escapeHtml(patient.ward_time || '—'),
            `<span class="badge ${badgeClass(patient.status)}">${escapeHtml((patient.status || '').charAt(0).toUpperCase() + (patient.status || '').slice(1))}</span>`,
            escapeHtml(patient.time_in || ''),
        ]);
    }

    function refreshLiveTable(table) {
        fetch(table.dataset.liveUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then((response) => response.json())
            .then((payload) => {
                const patients = payload.data || [];
                if (window.jQuery && $.fn.DataTable) {
                    const instance = $.fn.dataTable.isDataTable(table) ? $(table).DataTable() : null;
                    if (instance) {
                        instance.clear();
                        instance.rows.add(table.dataset.tableKind === 'specialty'
                            ? specialtyRowData(patients)
                            : table.dataset.tableKind === 'white-board'
                                ? whiteBoardRowData(patients)
                                : table.dataset.tableKind === 'white-board-ward'
                                    ? whiteBoardWardRowData(patients)
                                    : table.dataset.tableKind === 'ward-board'
                                        ? wardBoardRowData(patients)
                                        : wardRowData(patients));
                        instance.draw(false);
                        return;
                    }
                }

                const tbody = table.querySelector('tbody');
                if (!tbody) {
                    return;
                }

                tbody.innerHTML = table.dataset.tableKind === 'specialty'
                    ? renderSpecialtyRows(patients)
                    : table.dataset.tableKind === 'white-board'
                        ? renderWhiteBoardRows(patients)
                        : table.dataset.tableKind === 'white-board-ward'
                            ? renderWhiteBoardWardRows(patients)
                            : table.dataset.tableKind === 'ward-board'
                                ? renderWardBoardRows(patients)
                                : renderWardRows(patients);
            })
            .catch(() => {
                // Keep the last good snapshot on transient polling errors.
            });
    }

    function initDataTables() {
        if (!window.jQuery || !$.fn.DataTable) {
            return;
        }

        $('.datatable').each(function () {
            if ($.fn.dataTable.isDataTable(this)) {
                return;
            }

            $(this).DataTable({
                responsive: true,
                pageLength: 10,
                order: [],
            });
        });
    }

    function initConfirmations() {
        document.querySelectorAll('[data-confirm]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                event.preventDefault();

                Swal.fire({
                    title: 'Confirm action',
                    text: form.dataset.confirm,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, continue',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#0f766e',
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    }

    function initAlerts() {
        document.querySelectorAll('.alert-dismissible').forEach((alert) => {
            window.setTimeout(() => {
                alert.classList.remove('show');
                alert.classList.add('fade');
            }, 4500);
        });
    }

    function initLivePolling() {
        document.querySelectorAll('[data-live-url]').forEach((table) => {
            refreshLiveTable(table);
            window.setInterval(() => refreshLiveTable(table), 3000);
        });
    }

    function initSearchModal() {
        const searchInput = document.querySelector('#searchModal input[name="query"]');
        const searchModal = document.getElementById('searchModal');

        if (searchModal && searchInput) {
            searchModal.addEventListener('shown.bs.modal', () => searchInput.focus());
        }
    }

    function initTransfers() {
        document.querySelectorAll('.transfer-btn').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                const id = btn.dataset.patientId;
                const name = btn.dataset.patientName || '';
                const modal = document.getElementById('transferModal');
                const transferForm = document.getElementById('transferForm');
                const patientNameEl = document.getElementById('transferPatientName');

                if (!transferForm || !modal) return;

                // set form action to the movements endpoint for the selected patient
                transferForm.action = '/patients/' + id + '/movements';
                transferForm.dataset.patientId = id;
                if (patientNameEl) patientNameEl.textContent = name;

                // show bootstrap modal
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
            });
        });
    }

    // AJAX submit for transfer form
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!form || form.id !== 'transferForm') return;
        e.preventDefault();

        const action = form.action;
        const formData = new FormData(form);
        const tokenEl = document.querySelector('meta[name="csrf-token"]');
        const csrf = tokenEl ? tokenEl.getAttribute('content') : '';

        fetch(action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: formData,
        }).then((res) => {
            if (!res.ok) throw res;
            return res.json().catch(() => ({ message: 'OK' }));
        }).then((payload) => {
            const modalEl = document.getElementById('transferModal');
            const bs = bootstrap.Modal.getInstance(modalEl);
            if (bs) bs.hide();

            Swal.fire({
                icon: 'success',
                title: 'Transferred',
                text: payload.message || 'Patient transferred successfully.',
                confirmButtonColor: '#0f766e',
            }).then(() => {
                // reload to reflect updated counts/UI quickly
                window.location.reload();
            });
        }).catch(async (err) => {
            let text = 'Failed to transfer patient.';
            try {
                const json = await err.json();
                text = json.message || text;
            } catch (_) { }

            Swal.fire({ icon: 'error', title: 'Error', text, confirmButtonColor: '#dc2626' });
        });
    });

    function initNavToggle() {
        const shell = document.querySelector('.kepts-shell');
        const toggles = Array.from(document.querySelectorAll('[data-nav-toggle]'));

        if (!shell || toggles.length === 0) {
            return;
        }

        const storageKey = 'kepts-nav-hidden';

        const syncToggleState = (hidden) => {
            shell.classList.toggle('nav-hidden', hidden);

            toggles.forEach((toggle) => {
                const icon = toggle.querySelector('i');
                const label = toggle.querySelector('span');

                toggle.classList.toggle('nav-toggle-active', hidden && toggle.closest('.kepts-navbar') !== null);
                toggle.setAttribute('aria-pressed', hidden ? 'true' : 'false');

                if (icon) {
                    const isHeaderToggle = toggle.closest('.kepts-navbar') !== null;
                    if (isHeaderToggle) {
                        icon.className = hidden
                            ? 'fa-solid fa-compress me-2'
                            : 'fa-solid fa-up-right-and-down-left-from-center me-2';
                    } else {
                        icon.className = 'fa-solid fa-compress me-2';
                    }
                }

                if (label) {
                    const isHeaderToggle = toggle.closest('.kepts-navbar') !== null;
                    label.textContent = hidden
                        ? (isHeaderToggle ? 'Show nav' : 'Exit full screen')
                        : 'Full screen';
                }
            });
        };

        const savedState = window.localStorage.getItem(storageKey) === '1';
        syncToggleState(savedState);

        toggles.forEach((toggle) => {
            toggle.addEventListener('click', () => {
                const hidden = !shell.classList.contains('nav-hidden');
                window.localStorage.setItem(storageKey, hidden ? '1' : '0');
                syncToggleState(hidden);
            });
        });
    }

    // Notifications polling and reading
    function initNotifications() {
        const badge = document.getElementById('notificationBadge');
        const list = document.getElementById('notificationList');
        const markAllBtn = document.getElementById('markAllReadBtn');
        if (!badge || !list) return;

        function refreshNotifications() {
            fetch('/api/notifications/unread')
                .then(res => res.json())
                .then(data => {
                    const count = data.unread_count || 0;
                    if (count > 0) {
                        badge.textContent = count;
                        badge.classList.remove('d-none');
                        if (markAllBtn) markAllBtn.classList.remove('d-none');
                    } else {
                        badge.classList.add('d-none');
                        if (markAllBtn) markAllBtn.classList.add('d-none');
                    }

                    const items = data.notifications || [];
                    if (items.length > 0) {
                        list.innerHTML = items.map(n => {
                            const unreadStyle = !n.is_read ? 'background-color: rgba(15, 118, 110, 0.05); font-weight: 600;' : '';
                            return `
                                <a href="/patients/${n.patient_id}" class="list-group-item list-group-item-action p-3 notification-item" data-id="${n.id}" style="${unreadStyle} font-size: 0.82rem;">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-teal text-uppercase" style="font-size: 0.7rem; font-weight: 700; color: var(--kepts-primary);">
                                            ${n.type} alert
                                        </span>
                                        <small class="text-muted" style="font-size: 0.7rem;">${n.created_at}</small>
                                    </div>
                                    <div class="text-dark message-text">${escapeHtml(n.message)}</div>
                                </a>
                            `;
                        }).join('');
                    } else {
                        list.innerHTML = '<div class="p-3 text-center text-muted small">No new alerts</div>';
                    }
                })
                .catch(() => {});
        }

        // Poll every 3 seconds
        refreshNotifications();
        window.setInterval(refreshNotifications, 3000);

        // Click on notification to mark read
        list.addEventListener('click', function (e) {
            const item = e.target.closest('.notification-item');
            if (!item) return;
            const id = item.dataset.id;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            
            e.preventDefault();
            fetch(`/api/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrf,
                }
            }).then(() => {
                window.location.href = item.getAttribute('href');
            });
        });

        // Mark all as read
        if (markAllBtn) {
            markAllBtn.addEventListener('click', function (e) {
                e.preventDefault();
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                fetch('/api/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf,
                    }
                }).then(res => res.json()).then(() => {
                    refreshNotifications();
                });
            });
        }
    }

    // Modal forms handling and custom triggers
    function initInvestigationModals() {
        const assignSelect = document.getElementById('investigation_type_select');
        const customContainer = document.getElementById('custom_type_container');
        const customInput = document.getElementById('custom_investigation_type');
        const categoryInput = document.getElementById('investigation_category');

        if (assignSelect && customContainer) {
            assignSelect.addEventListener('change', function () {
                const opt = assignSelect.options[assignSelect.selectedIndex];
                const cat = opt?.dataset.category || '';
                categoryInput.value = cat;

                if (assignSelect.value.includes('Custom')) {
                    customContainer.classList.remove('d-none');
                    customInput.setAttribute('required', 'required');
                } else {
                    customContainer.classList.add('d-none');
                    customInput.removeAttribute('required');
                }
            });
        }

        // Trigger Assign modal
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('#assignInvestigationBtn') || e.target.closest('.assign-inv-btn');
            if (!btn) return;
            
            const id = btn.dataset.patientId;
            const name = btn.dataset.patientName;
            
            const modalEl = document.getElementById('assignInvestigationModal');
            if (!modalEl) return;

            const form = document.getElementById('assignInvestigationForm');
            form.action = `/patients/${id}/investigations`;
            
            document.getElementById('assign_patient_name').value = name;
            
            if (assignSelect) {
                assignSelect.value = '';
                categoryInput.value = '';
            }
            if (customContainer) {
                customContainer.classList.add('d-none');
            }
            if (customInput) {
                customInput.value = '';
                customInput.removeAttribute('required');
            }
            document.getElementById('notes').value = '';
            
            const bs = new bootstrap.Modal(modalEl);
            bs.show();
        });

        // Trigger Status update modal
        document.addEventListener('click', function (e) {
            const badge = e.target.closest('.status-badge');
            if (!badge) return;
            
            const id = badge.dataset.id;
            const inv = (window.KEPTS_INVESTIGATIONS_CACHE && window.KEPTS_INVESTIGATIONS_CACHE[id]) || {
                id: id,
                investigation_type: badge.dataset.name,
                status: badge.dataset.status,
                notes: badge.dataset.notes,
                updates: []
            };

            // Parse updates from attribute if cache doesn't have it (fallback)
            let updates = inv.updates || [];
            if (typeof updates === 'string') {
                try {
                    updates = JSON.parse(updates);
                } catch(_) {
                    updates = [];
                }
            } else if (badge.dataset.timeline) {
                try {
                    updates = JSON.parse(badge.dataset.timeline);
                } catch(_) {}
            }
            
            const modalEl = document.getElementById('updateInvestigationStatusModal');
            if (!modalEl) return;
            
            const form = document.getElementById('updateInvestigationStatusForm');
            form.action = '/investigations/' + id + '/status';
            
            document.getElementById('update_investigation_name').value = inv.investigation_type;
            document.getElementById('update_status').value = inv.status;
            document.getElementById('update_comments').value = '';
            
            // Populate history timeline
            const timelineEl = document.getElementById('investigationTimeline');
            if (timelineEl) {
                if (updates.length > 0) {
                    timelineEl.innerHTML = updates.map(u => `
                        <div class="list-group-item bg-transparent py-2 px-1 border-0 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge ${badgeClass(u.status)}" style="font-size: 0.65rem;">${escapeHtml(u.status)}</span>
                                <small class="text-muted" style="font-size: 0.7rem;">${escapeHtml(u.created_at)}</small>
                            </div>
                            <div class="text-dark small mt-1" style="font-size: 0.75rem;">
                                <strong>${escapeHtml(u.updated_by_name)}:</strong> ${escapeHtml(u.comments || 'No comments')}
                            </div>
                        </div>
                    `).join('');
                } else {
                    timelineEl.innerHTML = '<div class="p-3 text-center text-muted small">No updates recorded</div>';
                }
            }
            
            const bsModal = new bootstrap.Modal(modalEl);
            bsModal.show();
        });

        // AJAX submit for Assign Investigation Form
        const assignForm = document.getElementById('assignInvestigationForm');
        if (assignForm) {
            assignForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const data = new FormData(assignForm);

                fetch(assignForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: data
                }).then(res => {
                    if (!res.ok) throw res;
                    return res.json();
                }).then(payload => {
                    const modalEl = document.getElementById('assignInvestigationModal');
                    const bs = bootstrap.Modal.getInstance(modalEl);
                    if (bs) bs.hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'Assigned',
                        text: payload.message || 'Investigation assigned successfully.',
                        confirmButtonColor: '#0f766e',
                    }).then(() => {
                        if (document.getElementById('proceduresBoardContainer')) {
                            refreshProceduresBoard();
                        } else {
                            window.location.reload();
                        }
                    });
                }).catch(async err => {
                    let text = 'Failed to assign investigation.';
                    try {
                        const json = await err.json();
                        text = json.message || text;
                    } catch (_) {}
                    Swal.fire({ icon: 'error', title: 'Error', text, confirmButtonColor: '#dc2626' });
                });
            });
        }

        // AJAX submit for Update Investigation Status Form
        const updateForm = document.getElementById('updateInvestigationStatusForm');
        if (updateForm) {
            updateForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const data = new FormData(updateForm);

                fetch(updateForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: data
                }).then(res => {
                    if (!res.ok) throw res;
                    return res.json();
                }).then(payload => {
                    const modalEl = document.getElementById('updateInvestigationStatusModal');
                    const bs = bootstrap.Modal.getInstance(modalEl);
                    if (bs) bs.hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'Updated',
                        text: payload.message || 'Status updated successfully.',
                        confirmButtonColor: '#0f766e',
                    }).then(() => {
                        if (document.getElementById('proceduresBoardContainer')) {
                            refreshProceduresBoard();
                        } else {
                            window.location.reload();
                        }
                    });
                }).catch(async err => {
                    let text = 'Failed to update status.';
                    try {
                        const json = await err.json();
                        text = json.message || text;
                    } catch (_) {}
                    Swal.fire({ icon: 'error', title: 'Error', text, confirmButtonColor: '#dc2626' });
                });
            });
        }

        // Expand/collapse toggle for cards showing > 5 investigations
        document.addEventListener('click', function (e) {
            const toggleBtn = e.target.closest('.toggle-more-inv');
            if (!toggleBtn) return;
            
            const targetId = toggleBtn.dataset.target;
            const container = document.getElementById(targetId);
            if (!container) return;

            const isHidden = container.classList.contains('d-none');
            if (isHidden) {
                container.classList.remove('d-none');
                toggleBtn.innerHTML = '<i class="fa-solid fa-chevron-up me-1"></i>Show less';
            } else {
                container.classList.add('d-none');
                const count = container.children.length;
                toggleBtn.innerHTML = `<i class="fa-solid fa-chevron-down me-1"></i>Show ${count} more`;
            }
        });
    }

    // Procedures Board main engine
    let boardPatientsGlobal = [];

    function initProceduresBoard() {
        const board = document.getElementById('proceduresBoardContainer');
        if (!board) return;

        window.refreshProceduresBoard = function() {
            fetch(board.dataset.liveUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(payload => {
                boardPatientsGlobal = payload.data || [];
                applyBoardFiltersAndRender();
            })
            .catch(() => {});
        };

        // Listen for filter changes
        document.querySelectorAll('.filter-btn, #filterPriorityUrgent').forEach(el => {
            el.addEventListener('change', applyBoardFiltersAndRender);
        });
        document.getElementById('boardSearchInput')?.addEventListener('keyup', applyBoardFiltersAndRender);

        // Initial refresh
        refreshProceduresBoard();
        window.setInterval(refreshProceduresBoard, 3000);
    }

    function applyBoardFiltersAndRender() {
        const board = document.getElementById('proceduresBoardContainer');
        if (!board) return;

        const searchQuery = (document.getElementById('boardSearchInput')?.value || '').toLowerCase().trim();
        const selectedCategories = Array.from(document.querySelectorAll('[data-filter="category"]:checked')).map(el => el.dataset.value);
        const selectedStatuses = Array.from(document.querySelectorAll('[data-filter="status"]:checked')).flatMap(el => el.dataset.value.split(','));
        const urgentOnly = document.getElementById('filterPriorityUrgent')?.checked || false;

        const filtered = boardPatientsGlobal.map(p => {
            const cloned = { ...p, investigations: [...(p.investigations || [])] };
            
            cloned.investigations = cloned.investigations.filter(inv => {
                const catMatch = selectedCategories.includes(inv.category);
                const statusMatch = selectedStatuses.includes(inv.status);
                const priorityMatch = !urgentOnly || ['Urgent', 'Stat'].includes(inv.priority);
                return catMatch && statusMatch && priorityMatch;
            });
            
            return cloned;
        }).filter(p => {
            const matchesSearch = p.patient_name.toLowerCase().includes(searchQuery) || 
                                  p.ghims_number.toLowerCase().includes(searchQuery);
            if (!matchesSearch) return false;

            const hasTotalInv = (p.investigations || []).length > 0;
            const hasMatchingInv = p.investigations.length > 0;

            if (hasTotalInv && !hasMatchingInv) {
                return false;
            }

            if (!hasTotalInv) {
                if (urgentOnly || (selectedStatuses.length > 0 && !selectedStatuses.includes('Pending'))) {
                    return false;
                }
            }

            return true;
        });

        // Group patients by ward
        const wards = {
            'TRIAGE HOLDING': [],
            'RED': [],
            'ORANGE': [],
            'YELLOW': []
        };

        filtered.forEach(p => {
            const wardKey = String(p.current_ward || '').toUpperCase().trim();
            if (wards[wardKey] !== undefined) {
                wards[wardKey].push(p);
            }
        });

        // Render columns
        Object.keys(wards).forEach(wardKey => {
            const containerId = 'column-' + wardKey.toLowerCase().replace(' ', '-');
            const countId = 'count-' + wardKey.toLowerCase().replace(' ', '-');
            const listEl = document.getElementById(containerId);
            const countEl = document.getElementById(countId);
            
            if (countEl) countEl.textContent = wards[wardKey].length;

            if (listEl) {
                if (wards[wardKey].length > 0) {
                    listEl.innerHTML = wards[wardKey].map(p => renderPatientCard(p)).join('');
                } else {
                    listEl.innerHTML = `
                        <div class="text-center text-muted py-4 small bg-light rounded-3" style="border: 1px dashed rgba(15, 23, 42, 0.08);">
                            No patients in ${escapeHtml(wardKey)}
                        </div>
                    `;
                }
            }
        });
    }

    function renderPatientCard(patient) {
        const canAssign = window.KEPTS_CAN_ASSIGN === true;
        const investigations = patient.investigations || [];
        const isCollapsed = investigations.length > 5;
        const visibleInv = isCollapsed ? investigations.slice(0, 5) : investigations;
        const hiddenInv = isCollapsed ? investigations.slice(5) : [];
        
        const invListHtml = visibleInv.map(inv => renderInvestigationItem(inv)).join('');
        const hiddenListHtml = hiddenInv.map(inv => renderInvestigationItem(inv)).join('');
        
        const collapseToggle = isCollapsed 
            ? `<div class="mt-2">
                 <button class="btn btn-sm btn-link text-decoration-none p-0 toggle-more-inv" data-target="more-inv-${patient.id}" style="font-size: 0.8rem; color: var(--kepts-primary);">
                     <i class="fa-solid fa-chevron-down me-1"></i>Show ${hiddenInv.length} more
                 </button>
               </div>`
            : '';

        const assignBtn = canAssign 
            ? `<button class="btn btn-sm btn-outline-teal w-100 mt-2 rounded-pill assign-inv-btn" 
                       data-patient-id="${patient.id}" 
                       data-patient-name="${escapeHtml(patient.patient_name)}">
                   <i class="fa-solid fa-plus me-1"></i>Assign Investigation
               </button>`
            : '';

        return `
            <div class="patient-procedures-card" data-patient-id="${patient.id}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">${escapeHtml(patient.patient_name)}</h6>
                        <div class="text-muted small" style="font-size: 0.75rem;">
                            GHIMS: ${escapeHtml(patient.ghims_number)} | ${escapeHtml(patient.age)} Years
                        </div>
                    </div>
                    <span class="badge ${patient.assigned_team ? 'badge-soft-green' : 'badge-soft-gray'}" style="font-size: 0.7rem;">
                        ${escapeHtml(patient.assigned_team || 'No Team')}
                    </span>
                </div>
                
                <div class="patient-condition mt-1 mb-2">
                    <span class="badge" style="background: ${escapeHtml(patient.ward_color || '#6b7280')}; color: #fff; font-size: 0.7rem;">
                        ${escapeHtml(patient.current_ward || 'Triage')}
                    </span>
                    <span class="badge bg-light text-dark border ms-1" style="font-size: 0.7rem;">
                        ${escapeHtml(patient.condition || 'Stable')}
                    </span>
                </div>

                <div class="investigations-section mt-2 border-top pt-2">
                    <div class="small fw-semibold text-muted mb-1" style="font-size: 0.75rem;">Investigations:</div>
                    <div class="d-flex flex-column gap-1">
                        ${invListHtml || '<div class="text-muted small py-2 text-center bg-light rounded" style="font-size: 0.75rem;">No investigations assigned</div>'}
                        <div class="d-none d-flex flex-column gap-1 mt-1" id="more-inv-${patient.id}">
                            ${hiddenListHtml}
                        </div>
                        ${collapseToggle}
                    </div>
                </div>

                ${assignBtn}
            </div>
        `;
    }

    function renderInvestigationItem(inv) {
        let colorClass = 'badge-soft-yellow';
        const status = (inv.status || '').toLowerCase();
        if (status === 'pending') colorClass = 'badge-soft-yellow';
        else if (status === 'completed') colorClass = 'badge-soft-green';
        else if (status === 'cancelled') colorClass = 'badge-soft-red';
        else if (['sample taken', 'sent', 'in progress'].includes(status)) colorClass = 'badge-soft-primary';

        const priorityLabel = ['Urgent', 'Stat'].includes(inv.priority) 
            ? `<span class="badge bg-danger text-white ms-1" style="font-size: 0.65rem;">${escapeHtml(inv.priority)}</span>` 
            : '';

        return `
            <div class="d-flex align-items-center justify-content-between p-1 px-2 bg-light rounded border-start border-3 border-teal" style="border-left-color: var(--kepts-primary) !important;">
                <span class="small text-dark fw-medium" style="font-size: 0.78rem;">
                    ${escapeHtml(inv.investigation_type)} ${priorityLabel}
                </span>
                <span class="badge cursor-pointer status-badge ${colorClass}" 
                      data-id="${inv.id}" 
                      data-name="${escapeHtml(inv.investigation_type)}" 
                      data-status="${escapeHtml(inv.status)}" 
                      data-notes="${escapeHtml(inv.notes || '')}"
                      style="font-size: 0.7rem; cursor: pointer; padding: 0.3em 0.6em;">
                    ${escapeHtml(inv.status)}
                </span>
            </div>
        `;
    }

    document.addEventListener('DOMContentLoaded', () => {
        initDataTables();
        initConfirmations();
        initAlerts();
        initLivePolling();
        initSearchModal();
        initTransfers();
        initNavToggle();
        initNotifications();
        initInvestigationModals();
        initProceduresBoard();
    });

    window.KEPTS = {
        badgeClass,
    };
})();
