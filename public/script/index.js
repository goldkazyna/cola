// ===== API для работы с сервером =====
const API = {
    // CSRF токен для Laravel
    getCSRFToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content;
    },

    // Отправка SMS-кода
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

    // Проверка кода
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

    // Проверка авторизации
    async checkAuth() {
        const response = await fetch('/auth/check');
        return response.json();
    },

    // Выход
    async logout() {
        const response = await fetch('/auth/logout', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': this.getCSRFToken(),
            },
        });
        return response.json();
    },
};

// Глобальная переменная для хранения телефона между шагами
let currentPhone = '';

class Slider {
    constructor(container) {
        this.container = container;
        this.slider = container.querySelector('.slider');
        this.slides = container.querySelectorAll('.slide');
        this.indicators = container.querySelectorAll('.indicator');
        this.currentSlide = 0;

        this.isDragging = false;
        this.startPos = 0;
        this.currentTranslate = 0;
        this.prevTranslate = 0;

        this.resetTransforms();
        this.init();
    }

    resetTransforms() {
        this.slides.forEach(slide => {
            slide.style.transform = 'translateX(0)';
        });
        this.slider.style.transform = 'translateX(0)';
    }

    init() {
        this.resetTransforms();

        this.slider.addEventListener('mousedown', this.dragStart.bind(this));
        document.addEventListener('mousemove', this.drag.bind(this));
        document.addEventListener('mouseup', this.dragEnd.bind(this));

        this.slider.addEventListener('touchstart', this.dragStart.bind(this));
        document.addEventListener('touchmove', this.drag.bind(this));
        document.addEventListener('touchend', this.dragEnd.bind(this));

        this.indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => this.goToSlide(index));
        });

        this.updateSlider();
    }

    dragStart(event) {
        this.startPos = event.type === 'touchstart' ? event.touches[0].clientX : event.clientX;
        this.isDragging = true;
        this.slider.style.transition = 'none';
        if (event.type !== 'touchstart') event.preventDefault();
    }

    drag(event) {
        if (!this.isDragging) return;

        const currentPosition = event.type === 'touchmove' ? event.touches[0].clientX : event.clientX;
        const diff = currentPosition - this.startPos;

        this.currentTranslate = this.prevTranslate + diff;
        this.slider.style.transform = `translateX(${this.currentTranslate}px)`;
    }

    dragEnd() {
        if (!this.isDragging) return;
        this.isDragging = false;

        const movedBy = this.currentTranslate - this.prevTranslate;
        const slideWidth = this.container.offsetWidth;

        if (Math.abs(movedBy) > 50) {
            if (movedBy > 0 && this.currentSlide > 0) {
                this.currentSlide--;
            } else if (movedBy < 0 && this.currentSlide < this.slides.length - 1) {
                this.currentSlide++;
            }
        }

        this.updateSlider();
    }

    goToSlide(index) {
        this.currentSlide = index;
        this.updateSlider();
    }

    updateSlider() {
        const slideWidth = this.container.offsetWidth;
        const translateX = -this.currentSlide * slideWidth;

        this.currentTranslate = this.prevTranslate = translateX;
        this.slider.style.transition = 'transform 0.3s ease';
        this.slider.style.transform = `translateX(${translateX}px)`;

        this.updateIndicators();
    }

    updateIndicators() {
        this.indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === this.currentSlide);
        });
    }
}

function toggleAccordion(headerElement) {
    const accordion = headerElement.parentElement;
    const content = accordion.querySelector('.accordion-content');
    const arrow = accordion.querySelector('.accordion-arrow');

    document.querySelectorAll('.header-accordion .accordion-content').forEach(otherContent => {
        if (otherContent !== content && otherContent.classList.contains('open')) {
            otherContent.classList.remove('open');
            otherContent.parentElement.querySelector('.accordion-arrow').style.transform = 'rotate(0deg)';
        }
    });

    content.classList.toggle('open');

    if (content.classList.contains('open')) {
        arrow.style.transform = 'rotate(180deg)';
    } else {
        arrow.style.transform = 'rotate(0deg)';
    }
}


document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - starting initialization');

    // Инициализация слайдера
    const sliderContainer = document.querySelector('.slider-container');
    if (sliderContainer) {
        new Slider(sliderContainer);
    }

    // Закрытие аккордеонов при клике вне
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.header-accordion')) {
            document.querySelectorAll('.header-accordion .accordion-content').forEach(content => {
                content.classList.remove('open');
            });
            document.querySelectorAll('.accordion-arrow').forEach(arrow => {
                arrow.style.transform = 'rotate(0deg)';
            });
        }
    });

    // Элементы навигации
    const menuToggle = document.getElementById('menu-toggle');
    const authLink = document.querySelector('.auth-link');
    const checksLink = document.querySelector('.checks-link');
    const uploadLink = document.querySelector('.menu-content a:nth-child(3)'); // Загрузить чек из меню
    const uploadFromChecksLink = document.querySelectorAll('.downCheck'); // Кнопка "Загрузить чек" в окне Мои чеки
    const authWindow = document.querySelector('.auth-window');
    const verificationWindow = document.querySelector('.auth-verification');
    const checksWindow = document.querySelector('.auth-checks');
    const checksAddWindow = document.querySelector('.auth-checks-add');
    const uploadSuccessWindow = document.querySelector('.auth-upload-success');
    const authForm = document.getElementById('auth-form');
    const menuButton = document.querySelector('.menu-button');
    const body = document.body;

    // Все окна для управления
    const allWindows = [authWindow, verificationWindow, checksWindow, checksAddWindow, uploadSuccessWindow].filter(Boolean);

    // Управление прокруткой при открытии/закрытии меню
    menuToggle.addEventListener('change', function() {
        if (this.checked) {
            body.classList.add('menu-open');
        } else {
            body.classList.remove('menu-open');
        }
    });

    // ПРОСТАЯ ФУНКЦИЯ ДЛЯ ОТКРЫТИЯ ОКОН
    function openWindow(windowElement) {
        closeAllWindows();
        windowElement.classList.add('active');
        menuButton.classList.add('menu-back-arrow');
        menuToggle.checked = false;
        body.classList.remove('menu-open');
    }

    // Функция для показа окна успеха
    function showUploadSuccess() {
        closeAllWindows();
        uploadSuccessWindow.classList.add('active');
        menuButton.classList.add('menu-back-arrow');
        menuToggle.checked = false;
        body.classList.remove('menu-open');
    }

    // Функция для возврата на главный экран
    function returnToMainScreen() {
        closeAllWindows();
        menuButton.classList.remove('menu-back-arrow');
        menuToggle.checked = false;
        body.classList.remove('menu-open');
        console.log('Returned to main screen');
    }

    // Открытие авторизации
    if (authLink && authWindow) {
        authLink.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Opening auth window');
            openWindow(authWindow);
        });
    }

    // Открытие Мои чеки
    if (checksLink && checksWindow) {
        checksLink.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Opening checks window');
            openWindow(checksWindow);
        });
    }

    // Открытие Загрузить чек из меню
    if (uploadLink && checksAddWindow) {
        uploadLink.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Opening upload checks window from menu');
            openWindow(checksAddWindow);
        });
    }


    const uploadFromChecksLinks = document.querySelectorAll('.downCheck, .downChecks'); // Все кнопки "Загрузить чек"

    if (uploadFromChecksLinks.length > 0 && checksAddWindow) {
        uploadFromChecksLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                openWindow(checksAddWindow);
            });
        });
    }


	// Отправка формы авторизации (ввод телефона)
	if (authForm && authWindow && verificationWindow) {
		authForm.addEventListener('submit', async function(e) {
			e.preventDefault();
			
			const phoneInput = document.getElementById('phone-input');
			const submitBtn = authForm.querySelector('.auth-submit');
			const phone = phoneInput.value.trim();
			
			if (!phone) {
				alert('Введите номер телефона');
				return;
			}
			
			// Блокируем кнопку
			submitBtn.disabled = true;
			submitBtn.textContent = 'ОТПРАВКА...';
			
			try {
				const result = await API.sendCode(phone);
				
				if (result.success) {
					currentPhone = phone;
					
					// Для тестирования показываем код в консоли
					if (result.debug_code) {
						console.log('DEBUG: SMS код:', result.debug_code);
					}
					
					// Открываем окно верификации
					openWindow(verificationWindow);
				} else {
					alert(result.message || 'Ошибка отправки кода');
				}
			} catch (error) {
				console.error('Ошибка:', error);
				alert('Ошибка соединения с сервером');
			} finally {
				submitBtn.disabled = false;
				submitBtn.textContent = 'ПОЛУЧИТЬ SMS КОД';
			}
		});
	}

    // УПРОЩЕННАЯ ЛОГИКА КЛИКА ПО КНОПКЕ МЕНЮ
    menuButton.addEventListener('click', function(e) {
        console.log('Menu button clicked, back arrow:', menuButton.classList.contains('menu-back-arrow'));

        // Если есть активное окно - закрываем его и показываем меню
        const activeWindow = allWindows.find(window => window.classList.contains('active'));

        if (activeWindow) {
            e.preventDefault();
            console.log('Closing active window:', activeWindow.className);

            // Если это окно успеха - возвращаемся на главный экран
            if (activeWindow === uploadSuccessWindow) {
                returnToMainScreen();
            } else {
                closeAllWindows();
                menuButton.classList.remove('menu-back-arrow');
                menuToggle.checked = true;
                body.classList.add('menu-open');
            }
        } else {
            // Обычное переключение меню - не перехватываем событие
            console.log('Normal menu toggle');
            // Пусть чекбокс обрабатывает клик сам
        }
    });

    // Функция закрытия всех окон
    function closeAllWindows() {
        allWindows.forEach(window => {
            window.classList.remove('active');
        });
    }

    // Закрытие окон при клике вне их области
    allWindows.forEach(window => {
        window.addEventListener('click', function(e) {
            if (e.target === window) {
                // Если это окно успеха - возвращаемся на главный экран
                if (window === uploadSuccessWindow) {
                    returnToMainScreen();
                } else {
                    closeAllWindows();
                    menuButton.classList.remove('menu-back-arrow');
                    menuToggle.checked = true;
                    body.classList.add('menu-open');
                }
            }
        });
    });

    // Обработка других ссылок в меню
    const menuLinks = document.querySelectorAll('.menu-content a:not(.auth-link):not(.checks-link)');
    menuLinks.forEach(link => {
        if (link !== uploadLink) { // Исключаем уже обработанную ссылку
            link.addEventListener('click', function(e) {
                e.preventDefault();
                menuToggle.checked = false;
                body.classList.remove('menu-open');
                console.log('Other menu link:', this.textContent);
            });
        }
    });

    // Управление удалением чеков
    const deleteButtons = document.querySelectorAll('.delete-check');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const checkItem = this.closest('.check-item');
            if (confirm()) {
                checkItem.remove();
                updateChecksCount();
            }
        });
    });

    // Обновление счетчика чеков
    function updateChecksCount() {
        const checkItems = document.querySelectorAll('.check-item:not(.empty-check)');
        const checksCount = document.querySelector('.checks-count span');
        if (checksCount) {
            checksCount.textContent = `${checkItems.length}/10`;
        }
    }

    // === ФУНКЦИОНАЛЬНОСТЬ ДЛЯ ОКНА "ЗАГРУЗИТЬ ЧЕК" ===
    const uploadForm = document.getElementById('upload-form');
    const fileInput = document.getElementById('file-input');
    const uploadArea = document.getElementById('upload-area');
    const uploadedPreviews = document.getElementById('uploaded-previews');
    const uploadSubmit = document.querySelector('.upload-submit');
    const successButton = document.getElementById('success-button');

    let uploadedFiles = [];

    // Обработчик кнопки "УРА!"
    if (successButton) {
        successButton.addEventListener('click', function(e) {
            e.preventDefault();
            returnToMainScreen();
        });
    }

    // Инициализация функциональности загрузки, если элементы существуют
    if (uploadArea && fileInput && uploadedPreviews && uploadSubmit) {
        // Клик по области загрузки
        uploadArea.addEventListener('click', function() {
            fileInput.click();
        });

        // Drag and drop функциональность
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            const files = e.dataTransfer.files;
            handleFiles(files);
        });

        // Выбор файлов через input
        fileInput.addEventListener('change', function(e) {
            handleFiles(e.target.files);
        });

        // Отправка формы
        if (uploadForm) {
            uploadForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (uploadedFiles.length > 0) {
                    submitFiles();
                }
            });
        }
    }

    // Обработка выбранных файлов
    function handleFiles(files) {
        for (let file of files) {
            if (file.type.startsWith('image/')) {
                // Проверка размера файла (максимум 10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('Файл слишком большой. Максимальный размер: 10MB');
                    continue;
                }

                const reader = new FileReader();

                reader.onload = function(e) {
                    const fileData = {
                        file: file,
                        url: e.target.result,
                        name: file.name,
                        size: file.size
                    };
                    uploadedFiles.push(fileData);
                    createPreview(fileData, uploadedFiles.length - 1);
                    updateSubmitButton();
                };

                reader.onerror = function() {
                    alert('Ошибка при чтении файла');
                };

                reader.readAsDataURL(file);
            } else {
                alert('Пожалуйста, выберите только изображения (JPG, PNG)');
            }
        }

        // Очищаем input чтобы можно было выбрать те же файлы снова
        if (fileInput) {
            fileInput.value = '';
        }
    }

    // Создание превью
    function createPreview(fileData, index) {
        const preview = document.createElement('div');
        preview.className = 'upload-preview';
        preview.innerHTML = `
            <img src="${fileData.url}" alt="Превью чека">
            <button type="button" class="preview-remove" data-index="${index}">
                <img src="assets/close-icon.png" alt="Удалить">
            </button>
        `;

        uploadedPreviews.appendChild(preview);

        // Обработчик удаления превью
        const removeBtn = preview.querySelector('.preview-remove');
        removeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            removePreview(index);
        });
    }

    // Удаление превью
    function removePreview(index) {
        uploadedFiles.splice(index, 1);
        updatePreviews();
        updateSubmitButton();
    }

    // Обновление всех превью
    function updatePreviews() {
        uploadedPreviews.innerHTML = '';
        uploadedFiles.forEach((fileData, index) => {
            createPreview(fileData, index);
        });
    }

    // Обновление состояния кнопки отправки
    function updateSubmitButton() {
        if (uploadSubmit) {
            if (uploadedFiles.length > 0) {
                uploadSubmit.classList.add('active');
                uploadSubmit.disabled = false;
            } else {
                uploadSubmit.classList.remove('active');
                uploadSubmit.disabled = true;
            }
        }
    }

    // Отправка файлов на сервер
    function submitFiles() {
        console.log('Отправка файлов:', uploadedFiles);

        // Показываем окно успеха вместо alert
        showUploadSuccess();

        // Очищаем форму после успешной отправки
        uploadedFiles = [];
        updatePreviews();
        updateSubmitButton();
    }

    // Верификация
    const verificationForm = document.getElementById('verification-form');
    const codeInputs = document.querySelectorAll('.code-input');
    const timerElement = document.getElementById('timer');
    const resendCode = document.getElementById('resend-code');

    let timer = 60;
    let timerInterval;

    // Управление вводом кода
    codeInputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            const value = e.target.value;

            if (value.length === 1 && index < codeInputs.length - 1) {
                codeInputs[index + 1].focus();
            }

            if (value.length === 1) {
                input.classList.add('filled');
            } else {
                input.classList.remove('filled');
            }
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                codeInputs[index - 1].focus();
            }
        });

        input.addEventListener('keypress', function(e) {
            if (!/\d/.test(e.key)) {
                e.preventDefault();
            }
        });
    });

	// Отправка формы верификации
	if (verificationForm) {
		verificationForm.addEventListener('submit', async function(e) {
			e.preventDefault();
			
			const code = Array.from(codeInputs).map(input => input.value).join('');
			const submitBtn = verificationForm.querySelector('.verification-submit');
			
			if (code.length !== 4) {
				alert('Введите 4-значный код');
				return;
			}
			
			submitBtn.disabled = true;
			submitBtn.textContent = 'ПРОВЕРКА...';
			
			try {
				const result = await API.verifyCode(currentPhone, code);
				
				if (result.success) {
					// Успешная авторизация — открываем "Мои чеки"
					openWindow(checksWindow);
					
					// Очищаем форму
					codeInputs.forEach(input => {
						input.value = '';
						input.classList.remove('filled');
					});
				} else {
					alert(result.message || 'Неверный код');
				}
			} catch (error) {
				console.error('Ошибка:', error);
				alert('Ошибка соединения с сервером');
			} finally {
				submitBtn.disabled = false;
				submitBtn.textContent = 'Войти';
			}
		});
	}

    // Таймер
    function startTimer() {
        timer = 60;
        if (resendCode) resendCode.classList.add('disabled');

        timerInterval = setInterval(() => {
            timer--;
            if (timerElement) {
                timerElement.textContent = `Отправить код повторно через: ${timer} сек`;
            }

            if (timer <= 0) {
                clearInterval(timerInterval);
                if (timerElement) {
                    timerElement.textContent = 'Код можно отправить повторно';
                }
                if (resendCode) resendCode.classList.remove('disabled');
            }
        }, 1000);
    }

    // Повторная отправка кода
    if (resendCode) {
        resendCode.addEventListener('click', function(e) {
            e.preventDefault();
            if (!resendCode.classList.contains('disabled')) {
                startTimer();
            }
        });
    }

    // Закрытие по ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const activeWindow = allWindows.find(window => window.classList.contains('active'));
            if (activeWindow) {
                // Если это окно успеха - возвращаемся на главный экран
                if (activeWindow === uploadSuccessWindow) {
                    returnToMainScreen();
                } else {
                    closeAllWindows();
                    menuButton.classList.remove('menu-back-arrow');
                    menuToggle.checked = true;
                    body.classList.add('menu-open');
                }
            } else if (menuToggle.checked) {
                menuToggle.checked = false;
                body.classList.remove('menu-open');
            }
        }
    });

    // Инициализация счетчика чеков
    updateChecksCount();

    console.log('Navigation initialized successfully');
});
