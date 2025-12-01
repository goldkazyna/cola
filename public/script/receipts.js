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

		// Обработка выбора файла
		if (fileInput) {
			fileInput.addEventListener('change', (e) => {
				this.handleFileSelect(e.target.files);
				e.target.value = ''; // Сбрасываем чтобы можно было выбрать тот же файл
			});
		}

		if (cameraInput) {
			cameraInput.addEventListener('change', (e) => {
				this.handleFileSelect(e.target.files);
				e.target.value = ''; // Сбрасываем
			});
		}
	},

    // ===== Обработка выбранного файла =====
    handleFileSelect(files) {
        if (!files || files.length === 0) return;

        const file = files[0];

        // Проверка типа
        if (!file.type.startsWith('image/')) {
            this.showError('Выберите изображение');
            return;
        }

        // Проверка размера (10MB)
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

            // Скрываем область выбора
            if (uploadArea) {
                uploadArea.style.display = 'none';
            }

            // Кнопка удаления превью
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
                    // Очищаем форму
                    this.clearPreview();
                    
                    // Показываем окно успеха
                    this.showSuccessWindow();
                    
                    // Обновляем список чеков
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
        const formData = new FormData();
        formData.append('image', file);

        const response = await fetch('/receipts/upload', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': this.getCSRFToken(),
            },
            body: formData,
        });

        return response.json();
    },

    // ===== Загрузка списка чеков пользователя =====
    async loadUserReceipts() {
        const checksGrid = document.querySelector('.checks-grid');
        const chancesNumber = document.querySelector('.chances-number');

        if (!checksGrid) return;

        try {
            const response = await fetch('/receipts');
            const result = await response.json();

            if (result.success) {
                // Обновляем количество шансов
                if (chancesNumber) {
                    chancesNumber.textContent = result.chances || 0;
                }

                // Очищаем и заполняем сетку
                checksGrid.innerHTML = '';

                if (result.receipts && result.receipts.length > 0) {
                    result.receipts.forEach(receipt => {
                        checksGrid.appendChild(this.createReceiptItem(receipt));
                    });
                } else {
                    checksGrid.innerHTML = '<p class="no-receipts">У вас пока нет загруженных чеков</p>';
                }
            }
        } catch (error) {
            console.error('Ошибка загрузки чеков:', error);
        }
    },

    // ===== Создание элемента чека =====
    createReceiptItem(receipt) {
        const div = document.createElement('div');
        div.className = 'check-item';
        div.dataset.id = receipt.id;

        let statusClass = '';
        if (receipt.status === 'approved') statusClass = 'status-approved';
        if (receipt.status === 'rejected') statusClass = 'status-rejected';

        div.innerHTML = `
            <img src="${receipt.image_url}" alt="Чек" class="check-image">
            <div class="check-status ${statusClass}">${receipt.status_text}</div>
            <button class="delete-check" data-id="${receipt.id}">
                <img src="assets/close-icon.png" alt="Удалить">
            </button>
        `;

        // Обработчик удаления
        div.querySelector('.delete-check').addEventListener('click', (e) => {
            e.stopPropagation();
            this.deleteReceipt(receipt.id);
        });

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
                // Удаляем элемент из DOM
                const item = document.querySelector(`.check-item[data-id="${id}"]`);
                if (item) item.remove();

                // Обновляем список
                this.loadUserReceipts();
            } else {
                this.showError(result.message || 'Ошибка удаления');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            this.showError('Ошибка соединения');
        }
    },

    // ===== Инициализация кнопок удаления (для статичных) =====
    initDeleteButtons() {
        // Для динамически созданных кнопок используем делегирование
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
        // Можно сделать красивее потом
        alert(message);
    },
};

// Запуск после загрузки DOM
document.addEventListener('DOMContentLoaded', () => {
    Receipts.init();
});