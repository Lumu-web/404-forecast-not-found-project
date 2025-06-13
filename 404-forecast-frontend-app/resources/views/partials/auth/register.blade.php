<div
    class="modal fade"
    id="registerModal"
    tabindex="-1"
    aria-labelledby="registerModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="registerForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerModalLabel">Register Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <input type="text" name="name" class="form-control mb-3" placeholder="Name" required>
                    <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
                    <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
                    <input type="password" name="password_confirmation" class="form-control mb-3" placeholder="Confirm Password" required>

                    <div id="errorMessage" class="text-danger" style="display:none;"></div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="registerBtn">
                        <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true" id="registerSpinner"></span>
                        Register
                    </button>

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
