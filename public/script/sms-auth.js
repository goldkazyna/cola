// ===== Временная авторизация (повторный ввод номера) =====

const SmsAuth = {
    isAuthenticated: false,

    init() {
        this.initPhoneMask();
        this.initAuthForm();
        this.initUploadButtons();
        this.checkAuthStatus();
    },

    getCSRFToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content;
    },

    // ===== Маска телефона =====
    applyPhoneMask(input) {
        input.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.startsWith('7')) {
                value = value.substring(1);
            } else if (value.startsWith('8')) {
                value = value.substring(1);
            }

            value = value.substring(0, 10);

            let formatted = '+7';
            if (value.length > 0) formatted += ' ' + value.substring(0, 3);
            if (value.length > 3) formatted += ' ' + value.substring(3, 6);
            if (value.length > 6) formatted += ' ' + value.substring(6, 8);
            if (value.length > 8) formatted += ' ' + value.substring(8, 10);

            e.target.value = formatted;
        });

        input.addEventListener('keydown', (e) => {
            if (e.target.value.length <= 3 && e.key === 'Backspace') {
                e.preventDefault();
            }
        });

        input.addEventListener('focus', (e) => {
            if (e.target.value === '' || e.target.value === '+7') {
                e.target.value = '+7 ';
                setTimeout(() => e.target.setSelectionRange(4, 4), 0);
            }
        });
    },

    initPhoneMask() {
        const phoneInput = document.getElementById('phone-input');
        if (phoneInput) {
            phoneInput.value = '+7 ';
            this.applyPhoneMask(phoneInput);
        }
    },

    // ===== Форма авторизации =====
    initAuthForm() {
        const authForm = document.getElementById('auth-form');
        if (!authForm) return;

        const phoneInput = document.getElementById('phone-input');
        const confirmWrapper = document.getElementById('phone-confirm-wrapper');
        const confirmInput = document.getElementById('phone-confirm-input');
        const submitBtn = authForm.querySelector('.auth-submit');
        const authLinksText = document.querySelector('.auth-links-text');

        if (confirmInput) {
            this.applyPhoneMask(confirmInput);
        }

        // Показываем второе поле когда первый номер полный
        phoneInput.addEventListener('input', () => {
            const phone = phoneInput.value.trim();
            
            if (phone.length >= 16) {
                confirmWrapper.style.display = 'block';
                confirmInput.value = '+7 ';
                confirmInput.focus();
                submitBtn.textContent = 'ВОЙТИ';
                if (authLinksText) {
                    authLinksText.innerHTML = 'Повторите номер телефона<br>для подтверждения';
                }
            } else {
                confirmWrapper.style.display = 'none';
                submitBtn.textContent = 'ПРОДОЛЖИТЬ';
                if (authLinksText) {
                    authLinksText.innerHTML = 'Введите номер телефона<br>для авторизации';
                }
            }
        });

        // Отправка формы
        authForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const phone = phoneInput.value.trim();
            const phoneConfirm = confirmInput ? confirmInput.value.trim() : '';

            if (phone.length < 16) {
                this.showError('Введите полный номер телефона');
                return;
            }

            if (phoneConfirm.length < 16) {
                this.showError('Повторите номер телефона');
                if (confirmInput) confirmInput.focus();
                return;
            }

            this.hideError();
            submitBtn.disabled = true;
            submitBtn.textContent = 'ПРОВЕРКА...';

            try {
                const result = await this.verifyPhone(phone, phoneConfirm);

                if (result.success) {
                    if (result.csrf_token) {
                        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                        if (csrfMeta) csrfMeta.setAttribute('content', result.csrf_token);
                    }
                    
                    this.openChecksWindow();
                    this.updateAuthUI(true);
                    
                    // Сброс формы
                    phoneInput.value = '+7 ';
                    if (confirmInput) confirmInput.value = '';
                    if (confirmWrapper) confirmWrapper.style.display = 'none';
                    
                    if (typeof Receipts !== 'undefined') {
                        Receipts.loadUserReceipts();
                    }
                } else {
                    this.showError(result.message || 'Ошибка авторизации');
                    if (confirmInput) {
                        confirmInput.value = '+7 ';
                        confirmInput.focus();
                    }
                }
            } catch (error) {
                console.error('Ошибка:', error);
                this.showError('Ошибка соединения с сервером');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'ВОЙТИ';
            }
        });
    },

    // ===== Кнопки "Загрузить чек" =====
    initUploadButtons() {
        const uploadButtons = document.querySelectorAll('.downCheck, .downChecks, .menu-content .upload-link');
        
        uploadButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                if (this.isAuthenticated) {
                    this.openUploadWindow();
                } else {
                    this.openAuthWindow();
                }
            });
        });
    },

    // ===== API =====
    async verifyPhone(phone, phoneConfirm) {
        const response = await fetch('/auth/verify-phone', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.getCSRFToken(),
            },
            body: JSON.stringify({ phone, phone_confirm: phoneConfirm }),
        });
        return response.json();
    },

    async checkAuthStatus() {
        try {
            const result = await fetch('/auth/check').then(r => r.json());
            this.updateAuthUI(result.authenticated);
        } catch (error) {
            console.error('Ошибка проверки авторизации:', error);
        }
    },

    // ===== UI =====
    updateAuthUI(isAuthenticated) {
        const authLink = document.querySelector('.auth-link');
        const checksLink = document.querySelector('.checks-link');
        const uploadLink = document.querySelector('.upload-link');

        if (isAuthenticated) {
            if (authLink) authLink.style.display = 'none';
            if (checksLink) checksLink.style.display = 'block';
            if (uploadLink) uploadLink.style.display = 'block';
        } else {
            if (authLink) authLink.style.display = 'block';
            if (checksLink) checksLink.style.display = 'none';
            if (uploadLink) uploadLink.style.display = 'none';
        }
        this.isAuthenticated = isAuthenticated;
    },

    showError(message) {
        const errorEl = document.getElementById('auth-error');
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.style.display = 'block';
        }
    },

    hideError() {
        const errorEl = document.getElementById('auth-error');
        if (errorEl) errorEl.style.display = 'none';
    },

    // ===== Окна =====
    openAuthWindow() {
        document.querySelectorAll('.auth-window, .auth-verification, .auth-checks, .auth-checks-add, .auth-upload-success').forEach(w => w.classList.remove('active'));
        document.querySelector('.auth-window')?.classList.add('active');
        document.querySelector('.menu-button')?.classList.add('menu-back-arrow');
    },

    openUploadWindow() {
        document.querySelectorAll('.auth-window, .auth-verification, .auth-checks, .auth-checks-add, .auth-upload-success').forEach(w => w.classList.remove('active'));
        document.querySelector('.auth-checks-add')?.classList.add('active');
        document.querySelector('.menu-button')?.classList.add('menu-back-arrow');
    },

    openChecksWindow() {
        document.querySelector('.auth-window')?.classList.remove('active');
        document.querySelector('.auth-checks')?.classList.add('active');
    },
};

document.addEventListener('DOMContentLoaded', () => {
    SmsAuth.init();
});