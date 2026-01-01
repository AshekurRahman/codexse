/**
 * AJAX Form Validation Component
 * Automatically applies to forms with data-ajax-form attribute
 */

class AjaxFormValidator {
    constructor(form) {
        this.form = form;
        this.submitBtn = form.querySelector('[type="submit"]');
        this.originalBtnText = this.submitBtn?.innerHTML || 'Submit';
        this.validators = {
            required: (value) => value.trim() !== '' ? null : 'This field is required',
            email: (value) => !value || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) ? null : 'Please enter a valid email address',
            min: (value, param) => !value || value.length >= parseInt(param) ? null : `Must be at least ${param} characters`,
            max: (value, param) => !value || value.length <= parseInt(param) ? null : `Must be no more than ${param} characters`,
            numeric: (value) => !value || /^\d+$/.test(value) ? null : 'Must be a number',
            url: (value) => !value || /^https?:\/\/.+/.test(value) ? null : 'Please enter a valid URL',
            match: (value, param) => {
                const matchField = this.form.querySelector(`[name="${param}"]`);
                return !value || value === matchField?.value ? null : 'Fields do not match';
            },
        };

        this.init();
    }

    init() {
        // Create error container if not exists
        this.createGlobalErrorContainer();

        // Add validation listeners to fields
        this.form.querySelectorAll('[data-validate]').forEach(field => {
            field.addEventListener('blur', () => this.validateField(field));
            field.addEventListener('input', () => {
                if (field.classList.contains('border-red-500')) {
                    this.validateField(field);
                }
            });
        });

        // Handle form submission
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    createGlobalErrorContainer() {
        if (this.form.querySelector('.ajax-form-errors')) return;

        const container = document.createElement('div');
        container.className = 'ajax-form-errors hidden rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 mb-6';
        container.innerHTML = `
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-red-800 dark:text-red-200">Please fix the following errors:</h3>
                    <ul class="ajax-error-list mt-2 text-sm text-red-700 dark:text-red-300 list-disc list-inside space-y-1"></ul>
                </div>
            </div>
        `;
        this.form.insertBefore(container, this.form.firstChild);
        this.errorContainer = container;
        this.errorList = container.querySelector('.ajax-error-list');
    }

    validateField(field) {
        const rules = field.dataset.validate?.split('|') || [];
        const value = field.value;
        let error = null;

        for (const rule of rules) {
            const [ruleName, param] = rule.split(':');
            if (this.validators[ruleName]) {
                error = this.validators[ruleName](value, param);
                if (error) break;
            }
        }

        this.setFieldError(field, error);
        return error;
    }

    setFieldError(field, error) {
        // Find or create error element
        let errorEl = field.parentElement.querySelector('.field-error');
        if (!errorEl) {
            errorEl = document.createElement('p');
            errorEl.className = 'field-error mt-1 text-sm text-red-500 hidden';
            field.parentElement.appendChild(errorEl);
        }

        // Border classes to handle (multiple input styles)
        const normalBorderClasses = [
            'border-surface-200', 'border-surface-300',
            'dark:border-surface-600', 'dark:border-surface-700',
            'focus:border-primary-500', 'focus:ring-primary-500'
        ];
        const errorBorderClasses = ['border-red-500', 'focus:border-red-500', 'focus:ring-red-500'];

        if (error) {
            errorEl.textContent = error;
            errorEl.classList.remove('hidden');
            normalBorderClasses.forEach(cls => field.classList.remove(cls));
            errorBorderClasses.forEach(cls => field.classList.add(cls));
        } else {
            errorEl.classList.add('hidden');
            errorEl.textContent = '';
            errorBorderClasses.forEach(cls => field.classList.remove(cls));
            // Restore original border (use surface-200 as default)
            field.classList.add('border-surface-200', 'dark:border-surface-700', 'focus:border-primary-500', 'focus:ring-primary-500');
        }
    }

    validateAll() {
        const fields = this.form.querySelectorAll('[data-validate]');
        const errors = [];

        fields.forEach(field => {
            const error = this.validateField(field);
            if (error) {
                const label = field.closest('div')?.querySelector('label');
                const fieldName = label?.textContent?.trim().replace('*', '').replace('(optional)', '').trim() || field.name;
                errors.push(`${fieldName}: ${error}`);
            }
        });

        return errors;
    }

    showGlobalErrors(errors) {
        if (!this.errorContainer) return;

        if (errors.length > 0) {
            this.errorList.innerHTML = errors.map(e => `<li>${e}</li>`).join('');
            this.errorContainer.classList.remove('hidden');
            this.errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            this.errorContainer.classList.add('hidden');
            this.errorList.innerHTML = '';
        }
    }

    setLoading(loading) {
        if (!this.submitBtn) return;

        this.submitBtn.disabled = loading;
        if (loading) {
            this.submitBtn.dataset.originalHtml = this.submitBtn.innerHTML;
            this.submitBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 inline" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing...
            `;
            this.submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
        } else {
            this.submitBtn.innerHTML = this.submitBtn.dataset.originalHtml || this.originalBtnText;
            this.submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
        }
    }

    async handleSubmit(e) {
        e.preventDefault();

        // Client-side validation
        const errors = this.validateAll();
        if (errors.length > 0) {
            this.showGlobalErrors(errors);
            return;
        }

        this.showGlobalErrors([]);
        this.setLoading(true);

        try {
            const formData = new FormData(this.form);
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ||
                              this.form.querySelector('input[name="_token"]')?.value;

            const response = await fetch(this.form.action, {
                method: this.form.method || 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const contentType = response.headers.get('content-type');

            if (contentType && contentType.includes('application/json')) {
                const data = await response.json();

                if (response.ok) {
                    // Success
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else if (data.url) {
                        window.location.href = data.url;
                    } else if (data.message) {
                        this.showSuccess(data.message);
                        if (data.reload) {
                            setTimeout(() => window.location.reload(), 1500);
                        }
                    } else {
                        // Fallback - submit normally
                        this.form.submit();
                    }
                } else {
                    // Server validation errors
                    if (data.errors) {
                        const serverErrors = [];
                        for (const [fieldName, messages] of Object.entries(data.errors)) {
                            const field = this.form.querySelector(`[name="${fieldName}"]`);
                            if (field) {
                                this.setFieldError(field, messages[0]);
                            }
                            serverErrors.push(...messages);
                        }
                        this.showGlobalErrors(serverErrors);
                    } else if (data.message) {
                        this.showGlobalErrors([data.message]);
                    } else {
                        this.showGlobalErrors(['An error occurred. Please try again.']);
                    }
                    this.setLoading(false);
                }
            } else {
                // Non-JSON response, likely a redirect - follow it
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    // Submit normally as fallback
                    this.form.submit();
                }
            }
        } catch (error) {
            console.error('Form submission error:', error);
            // If AJAX fails, submit form normally
            this.form.submit();
        }
    }

    showSuccess(message) {
        if (!this.errorContainer) return;

        this.errorContainer.className = 'ajax-form-errors rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 mb-6';
        this.errorContainer.innerHTML = `
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <p class="text-sm font-medium text-green-800 dark:text-green-200">${message}</p>
            </div>
        `;
        this.errorContainer.classList.remove('hidden');
        this.setLoading(false);
    }
}

// Auto-initialize on forms with data-ajax-form attribute
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[data-ajax-form]').forEach(form => {
        new AjaxFormValidator(form);
    });
});

// Export for manual initialization
window.AjaxFormValidator = AjaxFormValidator;
