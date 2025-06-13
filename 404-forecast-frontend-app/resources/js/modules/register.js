export default class RegisterForm {
    //register.js for handling user registration form
    constructor(formId = 'registerForm', modalId = 'registerModal', errorId = 'errorMessage') {
        this.form = document.getElementById(formId);
        this.modalId = modalId;
        this.errorBox = document.getElementById(errorId);
        this.submitButton = document.getElementById('registerBtn');
        this.spinner = document.getElementById('registerSpinner');

        if (!this.form) {
            return;
        }
        if (!this.errorBox) {
            return;
        }
        this.form.addEventListener('submit', this.handleSubmit.bind(this));
    }

    async handleSubmit(event) {
        event.preventDefault();

        this.clearErrors();
        this.setLoading(true); // Start loading

        const formData = {
            name: this.form.name.value.trim(),
            email: this.form.email.value.trim(),
            password: this.form.password.value,
            password_confirmation: this.form.password_confirmation.value,
        };

        try {
            const response = await fetch('/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(formData),
            });

            const result = await response.json();

            if (!response.ok) {
                this.showErrors(result);
                return;
            }

            alert('Registration successful!');
            this.closeModal();
            window.location.reload();

        } catch (error) {
            console.error(error);
            this.showErrorMessage('An unexpected error occurred.');
        } finally {
            this.setLoading(false); // End loading regardless of success/failure
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
            this.errorBox.textContent = 'Registration failed. Please try again.';
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
