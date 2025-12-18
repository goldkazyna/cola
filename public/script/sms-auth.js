const SmsAuth = {
    currentPhone: '',
    currentName: '',
    currentSurname: '',
    currentCity: '',
    timerInterval: null,
    timerSeconds: 60,
    isAuthenticated: false,

    init() {
        this.initPhoneMask();
        this.initAuthForm();
        this.initVerificationForm();
        this.initResendCode();
        this.initUploadButtons();
        this.initAgreeCheckbox();
        this.checkAuthStatus();
    },

    getCSRFToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content;
    },

    // ===== Маска телефона =====
    initPhoneMask() {
        const phoneInput = document.getElementById('phone-input');
        if (phoneInput) {
            this.applyPhoneMask(phoneInput);
        }
    },

    applyPhoneMask(input) {
        input.value = '+7 ';

        input.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.startsWith('7')) value = value.substring(1);
            if (value.startsWith('8')) value = value.substring(1);
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
    },

    // ===== Форма авторизации =====
    initAuthForm() {
        const authForm = document.getElementById('auth-form');
        if (!authForm) return;

        authForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const nameInput = document.getElementById('name-input');
            const surnameInput = document.getElementById('surname-input');
            const cityInput = document.getElementById('city-input');
            const phoneInput = document.getElementById('phone-input');
            const submitBtn = authForm.querySelector('.auth-submit');

            const name = nameInput.value.trim();
            const surname = surnameInput.value.trim();
            const city = cityInput.value.trim();
            const phone = phoneInput.value.trim();

            // Проверки
            if (name.length < 2) {
                this.showError('auth', 'Введите имя');
                return;
            }
            if (surname.length < 2) {
                this.showError('auth', 'Введите фамилию');
                return;
            }
            if (city.length < 2) {
                this.showError('auth', 'Введите город');
                return;
            }
            if (phone.length < 16) {
                this.showError('auth', 'Введите полный номер телефона');
                return;
            }

            this.hideError('auth');
            submitBtn.disabled = true;
            submitBtn.textContent = 'ОТПРАВКА...';

            try {
                console.log('Отправка кода на:', phone);
                const result = await this.sendCode(phone, name, surname, city);
                console.log('Ответ:', result);

                if (result.success) {
                    this.currentPhone = phone;
                    this.currentName = name;
                    this.currentSurname = surname;
                    this.currentCity = city;

                    this.openVerificationWindow();
                    this.startTimer();
                } else {
                    this.showError('auth', result.message || 'Ошибка отправки кода');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                this.showError('auth', 'Ошибка соединения с сервером');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'ПОЛУЧИТЬ SMS КОД';
            }
        });
    },

    // ===== Форма верификации =====
    initVerificationForm() {
        const verificationForm = document.getElementById('verification-form');
        const codeInputs = document.querySelectorAll('.code-input');
        if (!verificationForm || !codeInputs.length) return;

        // Автопереход между полями
        codeInputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                const value = e.target.value;
                if (value.length === 1) {
                    input.classList.add('filled');
                    if (index < codeInputs.length - 1) {
                        codeInputs[index + 1].focus();
                    }
                } else {
                    input.classList.remove('filled');
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    codeInputs[index - 1].focus();
                }
            });

            input.addEventListener('keypress', (e) => {
                if (!/\d/.test(e.key)) {
                    e.preventDefault();
                }
            });
        });

        // Отправка формы
        verificationForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const code = Array.from(codeInputs).map(input => input.value).join('');
            const submitBtn = verificationForm.querySelector('.verification-submit');

            if (code.length !== 4) {
                this.showError('verification', 'Введите 4-значный код');
                return;
            }

            this.hideError('verification');
            submitBtn.disabled = true;
            submitBtn.textContent = 'ПРОВЕРКА...';

            try {
                console.log('Проверка кода:', code);
                const result = await this.verifyCode(code);
                console.log('Ответ:', result);

                if (result.success) {
                    if (result.csrf_token) {
                        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                        if (csrfMeta) csrfMeta.setAttribute('content', result.csrf_token);
                    }

                    this.clearCodeInputs();
                    this.stopTimer();
                    this.openChecksWindow();
                    this.updateAuthUI(true);

                    if (typeof Receipts !== 'undefined') {
                        Receipts.loadUserReceipts();
                    }
                } else {
                    this.showError('verification', result.message || 'Неверный код');
                    this.clearCodeInputs();
                    codeInputs[0].focus();
                }
            } catch (error) {
                console.error('Ошибка:', error);
                this.showError('verification', 'Ошибка соединения с сервером');
                this.clearCodeInputs();
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'ВОЙТИ';
            }
        });
    },

    // ===== Повторная отправка кода =====
    initResendCode() {
        const resendBtn = document.getElementById('resend-code');
        if (!resendBtn) return;

        resendBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            if (resendBtn.classList.contains('disabled')) return;

            try {
                const result = await this.sendCode(
                    this.currentPhone,
                    this.currentName,
                    this.currentSurname,
                    this.currentCity
                );

                if (result.success) {
                    this.startTimer();
                    this.hideError('verification');
                } else {
                    this.showError('verification', result.message || 'Ошибка отправки');
                }
            } catch (error) {
                this.showError('verification', 'Ошибка соединения');
            }
        });
    },

    // ===== Чекбокс согласия =====
    initAgreeCheckbox() {
        const checkbox = document.getElementById('agree-rules');
        const submitBtn = document.getElementById('auth-submit');
        if (!checkbox || !submitBtn) return;

        checkbox.addEventListener('change', () => {
            submitBtn.disabled = !checkbox.checked;
        });
    },

    // ===== Кнопки загрузки чека =====
    initUploadButtons() {
        const uploadButtons = document.querySelectorAll('.downCheck, .downChecks, .upload-link');
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
    async sendCode(phone, name, surname, city) {
        const response = await fetch('/auth/send-code', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.getCSRFToken(),
            },
            body: JSON.stringify({ phone, name, surname, city }),
        });
        return response.json();
    },

    async verifyCode(code) {
        const response = await fetch('/auth/verify-code', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.getCSRFToken(),
            },
            body: JSON.stringify({
                phone: this.currentPhone,
                code: code,
                name: this.currentName,
                surname: this.currentSurname,
                city: this.currentCity,
            }),
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

    // ===== Таймер =====
    startTimer() {
        const resendBtn = document.getElementById('resend-code');
        const timerText = document.querySelector('.no-code');
        if (!resendBtn) return;

        this.timerSeconds = 60;
        resendBtn.classList.add('disabled');

        this.updateTimerText(timerText);

        this.timerInterval = setInterval(() => {
            this.timerSeconds--;
            this.updateTimerText(timerText);

            if (this.timerSeconds <= 0) {
                this.stopTimer();
            }
        }, 1000);
    },

    updateTimerText(timerText) {
        if (timerText) {
            if (this.timerSeconds > 0) {
                timerText.textContent = `Повторная отправка через ${this.timerSeconds} сек`;
            } else {
                timerText.textContent = 'Не получили код?';
            }
        }
    },

    stopTimer() {
        const resendBtn = document.getElementById('resend-code');
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }
        if (resendBtn) {
            resendBtn.classList.remove('disabled');
        }
    },

    // ===== Утилиты =====
    clearCodeInputs() {
        document.querySelectorAll('.code-input').forEach(input => {
            input.value = '';
            input.classList.remove('filled');
        });
    },

    showError(form, message) {
        const errorEl = document.getElementById(form + '-error');
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.style.display = 'block';
        }
    },

    hideError(form) {
        const errorEl = document.getElementById(form + '-error');
        if (errorEl) {
            errorEl.style.display = 'none';
        }
    },

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

    // ===== Открытие окон =====
    openAuthWindow() {
        this.closeAllWindows();
        const authWindow = document.querySelector('.auth-window');
        const menuButton = document.querySelector('.menu-button');
        if (authWindow) authWindow.classList.add('active');
        if (menuButton) menuButton.classList.add('menu-back-arrow');
    },

    openVerificationWindow() {
        const authWindow = document.querySelector('.auth-window');
        const verificationWindow = document.querySelector('.auth-verification');
        if (authWindow) authWindow.classList.remove('active');
        if (verificationWindow) verificationWindow.classList.add('active');
    },

    openChecksWindow() {
        this.closeAllWindows();
        const checksWindow = document.querySelector('.auth-checks');
        const menuButton = document.querySelector('.menu-button');
        if (checksWindow) checksWindow.classList.add('active');
        if (menuButton) menuButton.classList.add('menu-back-arrow');
    },

    openUploadWindow() {
        this.closeAllWindows();
        const uploadWindow = document.querySelector('.auth-checks-add');
        const menuButton = document.querySelector('.menu-button');
        if (uploadWindow) uploadWindow.classList.add('active');
        if (menuButton) menuButton.classList.add('menu-back-arrow');
    },

    closeAllWindows() {
        document.querySelectorAll('.auth-window, .auth-verification, .auth-checks, .auth-checks-add, .auth-upload-success').forEach(w => {
            w.classList.remove('active');
        });
    },
};

document.addEventListener('DOMContentLoaded', () => {
    SmsAuth.init();
});