export default class LoginForm {
    constructor(formId = 'loginForm', modalId = 'loginModal', errorId = 'loginErrorMessage') {
        this.form = document.getElementById(formId);
        this.modalId = modalId;
        this.errorBox = document.getElementById(errorId);
        this.submitButton = this.form?.querySelector('button[type="submit"]');
        this.spinner = this.form?.querySelector('.spinner-border');

        if (!this.form) {
           return;
        }
        if (!this.errorBox) {
            return;
        }

        // Get CSRF token from meta tag once in constructor
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        this.form.addEventListener('submit', this.handleSubmit.bind(this));
    }

    async handleSubmit(event) {
        event.preventDefault();

        this.clearErrors();
        this.setLoading(true);

        const formData = {
            email: this.form.email.value.trim(),
            password: this.form.password.value,
        };

        try {
            const response = await fetch('/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken || '',  // Add CSRF token header here
                },
                body: JSON.stringify(formData),
            });

            const result = await response.json();

            if (!response.ok) {
                this.showErrors(result);
                return;
            }

            this.closeModal();
            if (result.redirect_url) {
                window.location.href = result.redirect_url;
            } else {
                window.location.reload();
            }

        } catch (error) {
            console.error(error);
            this.showErrorMessage('An unexpected error occurred.');
        } finally {
            this.setLoading(false);
        }
    }

    setLoading(isLoading) {
        if (this.submitButton && this.spinner) {
            this.submitButton.disabled = isLoading;
            this.spinner.classList.toggle('d-none', !isLoading);
        }
    }

    clearErrors() {
        this.errorBox.style.display = 'none';
        this.errorBox.textContent = '';
    }

    showErrors(result) {
        if (result.errors) {
            this.errorBox.textContent = Object.values(result.errors).flat().join(', ');
        } else if (result.message) {
            this.errorBox.textContent = result.message;
        } else {
            this.errorBox.textContent = 'Login failed. Please try again.';
        }
        this.errorBox.style.display = 'block';
    }

    showErrorMessage(message) {
        this.errorBox.textContent = message;
        this.errorBox.style.display = 'block';
    }

    closeModal() {
        const modalEl = document.getElementById(this.modalId);
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) {
            modalInstance.hide();
        }
    }
}
