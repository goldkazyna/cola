// ===== Работа с чеками =====

const Receipts = {
    uploadedFiles: [],
	translateDrawingName(name) {
		const lang = (typeof Lang !== 'undefined') ? Lang.current : 'ru';
		if (lang === 'kk') {
			return name
				.replace('Розыгрыш', 'Ұтыс')
				.replace('декабря', 'желтоқсан')
				.replace('января', 'қаңтар');
		}
		return name;
	},
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

    // Получение перевода
    getLang(key) {
        return (typeof Lang !== 'undefined') ? Lang.get(key) : key;
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
            this.showError(this.getLang('receipt.error.image'));
            return;
        }

        // Проверка размера (10MB)
        if (file.size > 10 * 1024 * 1024) {
            this.showError(this.getLang('receipt.error.size'));
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
                this.showError(this.getLang('receipt.error.select'));
                return;
            }

            const submitBtn = uploadForm.querySelector('.upload-submit');
            submitBtn.disabled = true;
            submitBtn.textContent = this.getLang('receipt.loading');

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
                    this.showError(result.message || this.getLang('receipt.error.upload'));
                }
            } catch (error) {
                console.error('Ошибка:', error);
                this.showError(this.getLang('receipt.error.connection'));
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = this.getLang('upload.submit');
                this.updateSubmitButton();
            }
        });
    },

    // ===== Загрузка чека на сервер =====
	async uploadReceipt(file) {
		const formData = new FormData();
		formData.append('image', file);

		const csrfToken = this.getCSRFToken();
		console.log('CSRF Token:', csrfToken); // Отладка

		try {
			const response = await fetch('/receipts/upload', {
				method: 'POST',
				headers: {
					'X-CSRF-TOKEN': csrfToken,
				},
				body: formData,
			});

			console.log('Response status:', response.status); // Отладка

			if (!response.ok) {
				const text = await response.text();
				console.log('Error response:', text); // Отладка
			}

			return response.json();
		} catch (error) {
			console.error('Upload error:', error); // Отладка
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
				// Обновляем количество шансов
				if (chancesNumber) {
					chancesNumber.textContent = result.chances || 0;
				}

				// Обновляем информацию о ближайшем розыгрыше
				this.updateNextDrawingInfo(result.next_drawing);

				// Рендерим чеки по периодам
				this.renderReceiptsByPeriods(result.periods);
			}
		} catch (error) {
			console.error('Ошибка загрузки чеков:', error);
		}
	},

	// ===== Информация о ближайшем розыгрыше =====
	updateNextDrawingInfo(nextDrawing) {
		let infoBlock = document.querySelector('.next-drawing-info');
		
		// Создаём блок если его нет
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
				<p class="next-drawing-title">${this.getLang('receipt.next.title')}</p>
				<p class="next-drawing-name">${this.translateDrawingName(nextDrawing.name)}</p>
				<p class="next-drawing-date">${nextDrawing.date_formatted}</p>
				${nextDrawing.days_left > 0 ? `<p class="next-drawing-days">${this.getLang('receipt.next.left')} ${this.pluralizeDays(nextDrawing.days_left)}</p>` : `<p class="next-drawing-days">${this.getLang('receipt.next.today')}</p>`}
			`;
		} else if (infoBlock) {
			infoBlock.innerHTML = `<p class="next-drawing-title">${this.getLang('receipt.next.finished')}</p>`;
		}
	},

	// ===== Склонение дней =====
	pluralizeDays(n) {
		n = Math.ceil(n);
		const lang = (typeof Lang !== 'undefined') ? Lang.current : 'ru';
		
		if (lang === 'kk') {
			return `${n} күн`;
		}
		
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
			checksGrid.innerHTML = `<p class="no-receipts">${this.getLang('receipt.empty')}</p>`;
			return;
		}

		periods.forEach(period => {
			// Создаём блок периода
			const periodBlock = document.createElement('div');
			periodBlock.className = `period-block ${period.is_passed ? 'period-passed' : 'period-active'}`;

			// Заголовок периода
			const periodHeader = document.createElement('div');
			periodHeader.className = 'period-header';
			periodHeader.innerHTML = `
				<div class="period-info">
					<span class="period-name">${this.translateDrawingName(period.drawing_name)}</span>
					<span class="period-date">${period.drawing_date_formatted}</span>
				</div>
				<div class="period-status ${period.is_passed ? 'status-passed' : 'status-upcoming'}">
					${period.is_passed ? this.getLang('receipt.status.passed') : this.getLang('receipt.status.upcoming')}
				</div>
			`;

			// Сетка чеков
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
			statusBadge = `<div class="check-badge passed">${this.getLang('receipt.status.passed')}</div>`;
		} else if (receipt.drawing_status && receipt.drawing_status.days_left !== undefined) {
			if (receipt.drawing_status.days_left > 0) {
				statusBadge = `<div class="check-badge active">${this.getLang('receipt.in')} ${this.pluralizeDays(receipt.drawing_status.days_left)}</div>`;
			} else {
				statusBadge = `<div class="check-badge today">${this.getLang('receipt.today')}</div>`;
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
        if (!confirm(this.getLang('receipt.confirm.delete'))) return;

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
                this.showError(result.message || this.getLang('receipt.error.delete'));
            }
        } catch (error) {
            console.error('Ошибка:', error);
            this.showError(this.getLang('receipt.error.connection'));
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