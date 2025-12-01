// ===== SMS Авторизация =====

const SmsAuth = {
    currentPhone: '',
    timerInterval: null,
    timerSeconds: 60,

	init() {
		this.isAuthenticated = false; // Добавь эту строку в начало
		this.initPhoneMask();
		this.initAuthForm();
		this.initVerificationForm();
		this.initResendCode();
		this.initUploadButtons(); // Добавь эту строку
		this.checkAuthStatus();
	},

    // CSRF токен
    getCSRFToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content;
    },

    // ===== Маска телефона =====
    initPhoneMask() {
        const phoneInput = document.getElementById('phone-input');
        if (!phoneInput) return;

        // Устанавливаем начальное значение
        phoneInput.value = '+7 ';

        phoneInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, ''); // Только цифры
            
            // Убираем 7 или 8 в начале если есть
            if (value.startsWith('7')) {
                value = value.substring(1);
            } else if (value.startsWith('8')) {
                value = value.substring(1);
            }

            // Ограничиваем 10 цифрами (без 7)
            value = value.substring(0, 10);

            // Форматируем: +7 777 433 38 22
            let formatted = '+7';
            if (value.length > 0) {
                formatted += ' ' + value.substring(0, 3);
            }
            if (value.length > 3) {
                formatted += ' ' + value.substring(3, 6);
            }
            if (value.length > 6) {
                formatted += ' ' + value.substring(6, 8);
            }
            if (value.length > 8) {
                formatted += ' ' + value.substring(8, 10);
            }

            e.target.value = formatted;
        });

        // Не даём удалить +7
        phoneInput.addEventListener('keydown', (e) => {
            if (e.target.value.length <= 3 && e.key === 'Backspace') {
                e.preventDefault();
            }
        });

        // При фокусе ставим курсор в конец
        phoneInput.addEventListener('focus', (e) => {
            if (e.target.value === '+7 ') {
                setTimeout(() => {
                    e.target.setSelectionRange(4, 4);
                }, 0);
            }
        });
    },

    // ===== Форма ввода телефона =====
    initAuthForm() {
        const authForm = document.getElementById('auth-form');
        if (!authForm) return;

        authForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const phoneInput = document.getElementById('phone-input');
            const submitBtn = authForm.querySelector('.auth-submit');
            const phone = phoneInput.value.trim();

            // Проверяем что номер полный (17 символов: +7 777 433 38 22)
            if (phone.length < 16) {
                this.showError('auth', 'Введите полный номер телефона');
                return;
            }

            this.hideError('auth');
            submitBtn.disabled = true;
            submitBtn.textContent = 'ОТПРАВКА...';

            try {
                const result = await this.sendCode(phone);

                if (result.success) {
                    this.currentPhone = phone;

                    // Для тестирования
                    if (result.debug_code) {
                        console.log('DEBUG SMS код:', result.debug_code);
                    }

                    // Открываем окно верификации
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

            // Только цифры
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
                const result = await this.verifyCode(this.currentPhone, code);

                if (result.success) {
                    // Успешно — открываем "Мои чеки"
                    this.clearCodeInputs();
                    this.stopTimer();
                    this.openChecksWindow();
                    this.updateAuthUI(true);
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
                submitBtn.textContent = 'Войти';
            }
        });
    },

    // ===== Повторная отправка кода =====
    initResendCode() {
        const resendBtn = document.getElementById('resend-code');
        if (!resendBtn) return;

        resendBtn.addEventListener('click', async (e) => {
            e.preventDefault();

            if (resendBtn.classList.contains('disabled')) {
                return;
            }

            try {
                const result = await this.sendCode(this.currentPhone);

                if (result.success) {
                    if (result.debug_code) {
                        console.log('DEBUG SMS код (повторно):', result.debug_code);
                    }
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
	// ===== Обработка кнопок "Загрузить чек" =====
	initUploadButtons() {
		// Все кнопки "Загрузить чек" на странице
		const uploadButtons = document.querySelectorAll('.downCheck, .downChecks, .menu-content .upload-link');
		
		uploadButtons.forEach(btn => {
			btn.addEventListener('click', (e) => {
				e.preventDefault();
				
				if (this.isAuthenticated) {
					// Авторизован — открываем окно загрузки
					this.openUploadWindow();
				} else {
					// Не авторизован — открываем авторизацию
					this.openAuthWindow();
				}
			});
		});
	},

	// ===== Открытие окна авторизации =====
	openAuthWindow() {
		const authWindow = document.querySelector('.auth-window');
		const menuButton = document.querySelector('.menu-button');
		
		// Закрываем все окна
		document.querySelectorAll('.auth-window, .auth-verification, .auth-checks, .auth-checks-add, .auth-upload-success').forEach(w => {
			w.classList.remove('active');
		});
		
		if (authWindow) {
			authWindow.classList.add('active');
		}
		if (menuButton) {
			menuButton.classList.add('menu-back-arrow');
		}
	},

	// ===== Открытие окна загрузки чека =====
	openUploadWindow() {
		const uploadWindow = document.querySelector('.auth-checks-add');
		const menuButton = document.querySelector('.menu-button');
		
		// Закрываем все окна
		document.querySelectorAll('.auth-window, .auth-verification, .auth-checks, .auth-checks-add, .auth-upload-success').forEach(w => {
			w.classList.remove('active');
		});
		
		if (uploadWindow) {
			uploadWindow.classList.add('active');
		}
		if (menuButton) {
			menuButton.classList.add('menu-back-arrow');
		}
	},
    // ===== Таймер =====
    startTimer() {
        const resendBtn = document.getElementById('resend-code');
        const timerText = document.querySelector('.no-code');
        if (!resendBtn) return;

        this.timerSeconds = 60;
        resendBtn.classList.add('disabled');
        resendBtn.style.pointerEvents = 'none';
        resendBtn.style.opacity = '0.5';

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
            resendBtn.style.pointerEvents = 'auto';
            resendBtn.style.opacity = '1';
        }
    },

    // ===== Очистка полей кода =====
    clearCodeInputs() {
        const codeInputs = document.querySelectorAll('.code-input');
        codeInputs.forEach(input => {
            input.value = '';
            input.classList.remove('filled');
        });
    },

    // ===== Показ/скрытие ошибок =====
    showError(form, message) {
        let errorEl;
        if (form === 'auth') {
            errorEl = document.getElementById('auth-error');
        } else {
            errorEl = document.getElementById('verification-error');
        }

        if (errorEl) {
            errorEl.textContent = message;
            errorEl.style.display = 'block';
        }
    },

    hideError(form) {
        let errorEl;
        if (form === 'auth') {
            errorEl = document.getElementById('auth-error');
        } else {
            errorEl = document.getElementById('verification-error');
        }

        if (errorEl) {
            errorEl.style.display = 'none';
        }
    },

    // ===== Проверка авторизации при загрузке =====
    async checkAuthStatus() {
        try {
            const result = await fetch('/auth/check').then(r => r.json());
            this.updateAuthUI(result.authenticated);
        } catch (error) {
            console.error('Ошибка проверки авторизации:', error);
        }
    },

    // ===== Обновление UI в зависимости от авторизации =====
	updateAuthUI(isAuthenticated) {
		const authLink = document.querySelector('.auth-link');
		const checksLink = document.querySelector('.checks-link');
		const uploadLink = document.querySelector('.upload-link');

		if (isAuthenticated) {
			// Скрываем "Авторизация"
			if (authLink) authLink.style.display = 'none';
			// Показываем "Мои чеки" и "Загрузить чек"
			if (checksLink) checksLink.style.display = 'block';
			if (uploadLink) uploadLink.style.display = 'block';
		} else {
			// Показываем только "Авторизация"
			if (authLink) authLink.style.display = 'block';
			// Скрываем остальное
			if (checksLink) checksLink.style.display = 'none';
			if (uploadLink) uploadLink.style.display = 'none';
		}

		// Сохраняем статус глобально
		this.isAuthenticated = isAuthenticated;
	},

    // ===== API запросы =====
    async sendCode(phone) {
        const response = await fetch('/auth/send-code', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.getCSRFToken(),
            },
            body: JSON.stringify({ phone }),
        });
        return response.json();
    },

    async verifyCode(phone, code) {
        const response = await fetch('/auth/verify-code', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.getCSRFToken(),
            },
            body: JSON.stringify({ phone, code }),
        });
        return response.json();
    },

    // ===== Открытие окон =====
    openVerificationWindow() {
        const authWindow = document.querySelector('.auth-window');
        const verificationWindow = document.querySelector('.auth-verification');
        
        if (authWindow) authWindow.classList.remove('active');
        if (verificationWindow) verificationWindow.classList.add('active');
    },

    openChecksWindow() {
        const verificationWindow = document.querySelector('.auth-verification');
        const checksWindow = document.querySelector('.auth-checks');
        
        if (verificationWindow) verificationWindow.classList.remove('active');
        if (checksWindow) checksWindow.classList.add('active');
    },
};

// Запускаем после загрузки DOM
document.addEventListener('DOMContentLoaded', () => {
    SmsAuth.init();
});