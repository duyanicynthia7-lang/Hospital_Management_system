/* Hospital Management System - UI helpers (vanilla JS) */
document.addEventListener('DOMContentLoaded', function () {
    // ----- Mobile sidebar toggle -----
    const sidebar = document.getElementById('hmsSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const toggle = document.getElementById('sidebarToggle');
    if (sidebar && toggle) {
        const close = () => { sidebar.classList.remove('show'); backdrop && backdrop.classList.remove('show'); };
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            backdrop && backdrop.classList.toggle('show');
        });
        backdrop && backdrop.addEventListener('click', close);
    }

    // ----- Generic confirmation modal (delete / discharge etc.) -----
    // Usage: <button data-bs-toggle="modal" data-bs-target="#confirmModal"
    //          data-action="URL" data-method="DELETE" data-title="..." data-message="..." data-confirm="Delete">
    const confirmModal = document.getElementById('confirmModal');
    if (confirmModal) {
        confirmModal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            if (!trigger) return;
            const form = confirmModal.querySelector('form');
            form.action = trigger.getAttribute('data-action');
            form.querySelector('input[name="_method"]').value = trigger.getAttribute('data-method') || 'DELETE';
            confirmModal.querySelector('.modal-title').textContent = trigger.getAttribute('data-title') || 'Please confirm';
            confirmModal.querySelector('.confirm-message').textContent =
                trigger.getAttribute('data-message') || 'Are you sure? This action cannot be undone.';
            const btn = confirmModal.querySelector('.confirm-btn');
            btn.textContent = trigger.getAttribute('data-confirm') || 'Delete';
        });
    }

    // ----- Auto-dismiss success alerts after 5 seconds -----
    document.querySelectorAll('.alert-success.auto-dismiss').forEach(function (el) {
        setTimeout(function () {
            if (window.bootstrap) {
                bootstrap.Alert.getOrCreateInstance(el).close();
            }
        }, 5000);
    });

    // ----- Prevent double form submission -----
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            const btn = form.querySelector('button[type="submit"]');
            if (btn && !btn.dataset.noDisable) {
                setTimeout(() => { btn.disabled = true; }, 0);
            }
        });
    });

    // ----- Admission form: toggle discharge date requirement with status -----
    const statusSelect = document.getElementById('admission_status');
    const dischargeInput = document.getElementById('discharged_at');
    if (statusSelect && dischargeInput) {
        const sync = () => {
            const discharged = statusSelect.value === 'discharged';
            dischargeInput.required = discharged;
            dischargeInput.closest('.discharge-wrapper')?.classList.toggle('opacity-50', !discharged);
        };
        statusSelect.addEventListener('change', sync);
        sync();
    }
});
