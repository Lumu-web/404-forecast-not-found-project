<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="loginForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required />
                        <label for="email">Email</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required />
                        <label for="password">Password</label>
                    </div>

                    <div id="loginErrorMessage" class="alert alert-danger d-none" role="alert"></div>
                </div>

                <div class="modal-footer">
                    <meta name="csrf-token" content="{{ csrf_token() }}">
                    <button type="submit" class="btn btn-primary w-100 d-flex justify-content-center align-items-center">
                        <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                        <span>Login</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

