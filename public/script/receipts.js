// ===== Работа с чеками =====

const Receipts = {
    uploadedFiles: [],

    init() {
        this.initUploadButtons();
        this.initUploadForm();
        this.initDeleteButtons();
        this.loadUserReceipts();
    },

    // CSRF токен
    getCSRFToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content;
    },

    // ===== Логирование ошибок на сервер =====
    async logError(type, message, status = null, extra = {}) {
        try {
            await fetch('/log/error', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken(),
                },
                body: JSON.stringify({
                    type: type,
                    message: message,
                    status: status,
                    url: window.location.href,
                    ...extra,
                }),
            });
        } catch (e) {
            console.error('Failed to log error:', e);
        }
    },

    // ===== Кнопки выбора (камера/галерея) =====
    initUploadButtons() {
        const galleryBtn = document.getElementById('gallery-btn');
        const cameraBtn = document.getElementById('camera-btn');
        const fileInput = document.getElementById('file-input');
        const cameraInput = document.getElementById('camera-input');

        if (galleryBtn && fileInput) {
            galleryBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                fileInput.click();
            });
        }

        if (cameraBtn && cameraInput) {
            cameraBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                cameraInput.click();
            });
        }

        if (fileInput) {
            fileInput.addEventListener('change', (e) => {
                this.handleFileSelect(e.target.files);
                e.target.value = '';
            });
        }

        if (cameraInput) {
            cameraInput.addEventListener('change', (e) => {
                this.handleFileSelect(e.target.files);
                e.target.value = '';
            });
        }
    },

    // ===== Обработка выбранного файла =====
    handleFileSelect(files) {
        if (!files || files.length === 0) return;

        const file = files[0];

        if (!file.type.startsWith('image/')) {
            this.showError('Выберите изображение');
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            this.showError('Файл слишком большой. Максимум 10MB');
            return;
        }

        this.uploadedFiles = [file];
        this.showPreview(file);
        this.updateSubmitButton();
    },

    // ===== Показ превью =====
    showPreview(file) {
        const previewsContainer = document.getElementById('uploaded-previews');
        const uploadArea = document.getElementById('upload-area');
        
        if (!previewsContainer) return;

        previewsContainer.innerHTML = '';

        const reader = new FileReader();
        reader.onload = (e) => {
            const preview = document.createElement('div');
            preview.className = 'upload-preview';
            preview.innerHTML = `
                <img src="${e.target.result}" alt="Превью чека">
                <button type="button" class="preview-remove">
                    <img src="assets/close-icon.png" alt="Удалить">
                </button>
            `;

            previewsContainer.appendChild(preview);

            if (uploadArea) {
                uploadArea.style.display = 'none';
            }

            preview.querySelector('.preview-remove').addEventListener('click', () => {
                this.clearPreview();
            });
        };

        reader.readAsDataURL(file);
    },

    // ===== Очистка превью =====
    clearPreview() {
        const previewsContainer = document.getElementById('uploaded-previews');
        const uploadArea = document.getElementById('upload-area');
        const fileInput = document.getElementById('file-input');
        const cameraInput = document.getElementById('camera-input');

        this.uploadedFiles = [];

        if (previewsContainer) {
            previewsContainer.innerHTML = '';
        }

        if (uploadArea) {
            uploadArea.style.display = 'block';
        }

        if (fileInput) fileInput.value = '';
        if (cameraInput) cameraInput.value = '';

        this.updateSubmitButton();
    },

    // ===== Обновление кнопки отправки =====
    updateSubmitButton() {
        const submitBtn = document.querySelector('.upload-submit');
        if (!submitBtn) return;

        if (this.uploadedFiles.length > 0) {
            submitBtn.disabled = false;
            submitBtn.classList.add('active');
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.remove('active');
        }
    },

    // ===== Форма отправки =====
    initUploadForm() {
        const uploadForm = document.getElementById('upload-form');
        if (!uploadForm) return;

        uploadForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (this.uploadedFiles.length === 0) {
                this.showError('Выберите фото чека');
                return;
            }

            const submitBtn = uploadForm.querySelector('.upload-submit');
            submitBtn.disabled = true;
            submitBtn.textContent = 'ЗАГРУЗКА...';

            try {
                const result = await this.uploadReceipt(this.uploadedFiles[0]);

                if (result.success) {
                    this.clearPreview();
                    this.showSuccessWindow();
                    this.loadUserReceipts();
                } else {
                    this.showError(result.message || 'Ошибка загрузки');
                }
            } catch (error) {
                console.error('Ошибка:', error);
                this.showError('Ошибка соединения с сервером');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'ОТПРАВИТЬ';
                this.updateSubmitButton();
            }
        });
    },

    // ===== Загрузка чека на сервер =====
    async uploadReceipt(file) {
		const compressedFile = await this.compressImage(file);
        const formData = new FormData();
		formData.append('image', compressedFile);

        const csrfToken = this.getCSRFToken();

        try {
            const response = await fetch('/receipts/upload', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData,
            });

            // Клонируем чтобы можно было прочитать дважды при ошибке
            const responseClone = response.clone();
            
            let result;
            try {
                result = await response.json();
            } catch (jsonError) {
                const text = await responseClone.text();
                
                // Логируем на сервер
                await this.logError('upload_parse_error', 'Response is not JSON', response.status, {
                    response_text: text.substring(0, 1000),
                    file_name: file.name,
                    file_size: file.size,
                });
                
                return { success: false, message: 'Ошибка сервера' };
            }

            if (!response.ok) {
                // Логируем на сервер
                await this.logError('upload_failed', result.message || 'Unknown error', response.status, {
                    response: result,
                    file_name: file.name,
                    file_size: file.size,
                });

                // 419 = CSRF истёк
                if (response.status === 419) {
                    alert('Сессия истекла. Страница будет перезагружена.');
                    window.location.reload();
                    return { success: false, message: 'Сессия истекла' };
                }

                // 401 = не авторизован
                if (response.status === 401) {
                    alert('Необходимо авторизоваться заново.');
                    window.location.reload();
                    return { success: false, message: 'Не авторизован' };
                }

                return { success: false, message: result.message || 'Ошибка загрузки' };
            }

            return result;

        } catch (error) {
            // Логируем сетевую ошибку на сервер
            await this.logError('upload_network_error', error.message, null, {
                error_type: error.name,
                file_name: file.name,
                file_size: file.size,
            });
            
            throw error;
        }
    },

    // ===== Загрузка списка чеков пользователя =====
    async loadUserReceipts() {
        const checksContent = document.querySelector('.checks-content');
        const chancesNumber = document.querySelector('.chances-number');

        if (!checksContent) return;

        try {
            const response = await fetch('/receipts');
            const result = await response.json();

            if (result.success) {
                if (chancesNumber) {
                    chancesNumber.textContent = result.chances || 0;
                }
                this.updateNextDrawingInfo(result.next_drawing);
                this.renderReceiptsByPeriods(result.periods);
            }
        } catch (error) {
            console.error('Ошибка загрузки чеков:', error);
            this.logError('load_receipts_error', error.message);
        }
    },

    // ===== Информация о ближайшем розыгрыше =====
    updateNextDrawingInfo(nextDrawing) {
        let infoBlock = document.querySelector('.next-drawing-info');
        
        if (!infoBlock) {
            const chancesBlock = document.querySelector('.chances-count');
            if (chancesBlock) {
                infoBlock = document.createElement('div');
                infoBlock.className = 'next-drawing-info';
                chancesBlock.after(infoBlock);
            }
        }

        if (infoBlock && nextDrawing) {
            infoBlock.innerHTML = `
                <p class="next-drawing-title">Ближайший розыгрыш:</p>
                <p class="next-drawing-name">${nextDrawing.name}</p>
                <p class="next-drawing-date">${nextDrawing.date_formatted}</p>
                ${nextDrawing.days_left > 0 ? `<p class="next-drawing-days">Осталось ${this.pluralizeDays(nextDrawing.days_left)}</p>` : '<p class="next-drawing-days">Сегодня!</p>'}
            `;
        } else if (infoBlock) {
            infoBlock.innerHTML = '<p class="next-drawing-title">Все розыгрыши завершены</p>';
        }
    },

    // ===== Склонение дней =====
    pluralizeDays(n) {
        n = Math.ceil(n);
        const forms = ['день', 'дня', 'дней'];
        const n1 = Math.abs(n) % 100;
        const n2 = n1 % 10;
        if (n1 > 10 && n1 < 20) return `${n} ${forms[2]}`;
        if (n2 > 1 && n2 < 5) return `${n} ${forms[1]}`;
        if (n2 === 1) return `${n} ${forms[0]}`;
        return `${n} ${forms[2]}`;
    },

    // ===== Рендер чеков по периодам =====
    renderReceiptsByPeriods(periods) {
        const checksGrid = document.querySelector('.checks-grid');
        if (!checksGrid) return;

        checksGrid.innerHTML = '';

        if (!periods || periods.length === 0) {
            checksGrid.innerHTML = '<p class="no-receipts">У вас пока нет загруженных чеков</p>';
            return;
        }

        periods.forEach(period => {
            const periodBlock = document.createElement('div');
            periodBlock.className = `period-block ${period.is_passed ? 'period-passed' : 'period-active'}`;

            const periodHeader = document.createElement('div');
            periodHeader.className = 'period-header';
            periodHeader.innerHTML = `
                <div class="period-info">
                    <span class="period-name">${period.drawing_name}</span>
                    <span class="period-date">${period.drawing_date_formatted}</span>
                </div>
                <div class="period-status ${period.is_passed ? 'status-passed' : 'status-upcoming'}">
                    ${period.is_passed ? 'Розыгрыш прошёл' : 'Ожидается'}
                </div>
            `;

            const receiptsGrid = document.createElement('div');
            receiptsGrid.className = 'period-receipts-grid';

            period.receipts.forEach(receipt => {
                receiptsGrid.appendChild(this.createReceiptItem(receipt, period.is_passed));
            });

            periodBlock.appendChild(periodHeader);
            periodBlock.appendChild(receiptsGrid);
            checksGrid.appendChild(periodBlock);
        });
    },

    // ===== Создание элемента чека =====
    createReceiptItem(receipt, isPassed) {
        const div = document.createElement('div');
        div.className = `check-item ${isPassed ? 'check-passed' : ''}`;
        div.dataset.id = receipt.id;

        let statusBadge = '';
        if (isPassed) {
            statusBadge = '<div class="check-badge passed">Розыгрыш прошёл</div>';
        } else if (receipt.drawing_status && receipt.drawing_status.days_left !== undefined) {
            if (receipt.drawing_status.days_left > 0) {
                statusBadge = `<div class="check-badge active">Через ${this.pluralizeDays(receipt.drawing_status.days_left)}</div>`;
            } else {
                statusBadge = '<div class="check-badge today">Сегодня розыгрыш!</div>';
            }
        }

        div.innerHTML = `
            <img src="${receipt.image_url}" alt="Чек" class="check-image">
            ${statusBadge}
            <div class="check-date">${receipt.created_at}</div>
            <button class="delete-check" data-id="${receipt.id}">
                <img src="assets/close-icon.png" alt="Удалить">
            </button>
        `;

        return div;
    },

    // ===== Удаление чека =====
    async deleteReceipt(id) {
        if (!confirm('Удалить этот чек?')) return;

        try {
            const response = await fetch(`/receipts/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': this.getCSRFToken(),
                },
            });

            const result = await response.json();

            if (result.success) {
                const item = document.querySelector(`.check-item[data-id="${id}"]`);
                if (item) item.remove();
                this.loadUserReceipts();
            } else {
                this.showError(result.message || 'Ошибка удаления');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            this.logError('delete_receipt_error', error.message, null, { receipt_id: id });
            this.showError('Ошибка соединения');
        }
    },

    // ===== Инициализация кнопок удаления =====
    initDeleteButtons() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('.delete-check')) {
                const btn = e.target.closest('.delete-check');
                const id = btn.dataset.id;
                if (id) {
                    e.stopPropagation();
                    this.deleteReceipt(id);
                }
            }
        });
    },

    // ===== Показ окна успеха =====
    showSuccessWindow() {
        const uploadWindow = document.querySelector('.auth-checks-add');
        const successWindow = document.querySelector('.auth-upload-success');

        if (uploadWindow) uploadWindow.classList.remove('active');
        if (successWindow) successWindow.classList.add('active');
    },

    // ===== Показ ошибки =====
    showError(message) {
        alert(message);
    },
	
	// Добавь в объект Receipts:

	// Сжатие изображения перед отправкой
	async compressImage(file, maxWidth = 1920, quality = 0.8) {
		return new Promise((resolve) => {
			const reader = new FileReader();
			reader.onload = (e) => {
				const img = new Image();
				img.onload = () => {
					const canvas = document.createElement('canvas');
					let width = img.width;
					let height = img.height;

					// Уменьшаем если больше maxWidth
					if (width > maxWidth) {
						height = (height * maxWidth) / width;
						width = maxWidth;
					}

					canvas.width = width;
					canvas.height = height;

					const ctx = canvas.getContext('2d');
					ctx.drawImage(img, 0, 0, width, height);

					canvas.toBlob((blob) => {
						resolve(new File([blob], file.name, { type: 'image/jpeg' }));
					}, 'image/jpeg', quality);
				};
				img.src = e.target.result;
			};
			reader.readAsDataURL(file);
		});
	},
};

// Запуск после загрузки DOM
document.addEventListener('DOMContentLoaded', () => {
    Receipts.init();
});