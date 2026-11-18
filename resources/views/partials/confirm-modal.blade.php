{{-- Reusable confirmation modal. Triggers set action/method/message via data-* attributes (see public/js/hms.js). --}}
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="#">
                @csrf
                <input type="hidden" name="_method" value="DELETE">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalTitle">Please confirm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex gap-3 align-items-start">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-2"></i>
                        <p class="confirm-message mb-0">Are you sure? This action cannot be undone.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-hms confirm-btn">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
