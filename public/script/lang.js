// ===== Переводы =====
const translations = {
    ru: {
        // Меню
        'menu.auth': 'АВТОРИЗАЦИЯ',
        'menu.checks': 'МОИ ЧЕКИ',
        'menu.upload': 'ЗАГРУЗИТЬ ЧЕК',
        'menu.lang': 'ҚАЗАҚША',
        
        // Авторизация
        'auth.title': 'АВТОРИЗАЦИЯ',
        'auth.subtitle': 'Укажите номер телефона',
        'auth.confirm': 'Введите повторно номер телефона',
        'auth.button': 'ПРОДОЛЖИТЬ',
        'auth.button.login': 'ВОЙТИ',
        'auth.hint': 'Введите номер телефона<br>для авторизации',
        'auth.hint.confirm': 'Повторите номер телефона<br>для подтверждения',
        'auth.error.phone': 'Введите полный номер телефона',
        'auth.error.confirm': 'Повторите номер телефона',
        'auth.error.mismatch': 'Номера телефонов не совпадают',
        
        // Главная
        'main.title1': 'СДЕЛАЙ НОВЫЙ ГОД',
        'main.title2': 'ЯРЧЕ С',
        'main.subtitle': 'Покупай продукцию Coca Cola и участвуй в розыгрыше еженедельных и главного приза!',
        'main.dates': '8 декабря 2025 — 11 января 2026',
        'main.upload': 'Загрузить чек',
        
        // Призы
        'prizes.title': 'ПРИЗЫ',
        'prizes.note': 'Призы могут отличаться от изображений',
        'prizes.main': 'ГЛАВНЫЙ ПРИЗ',
        'prizes.main.desc': 'Три сертификата от тур оператора на незабываемое путешествие на двоих',
        
        // Как участвовать
        'howto.title': 'Как участвовать?',
        'howto.step1.title': 'Купи 2 бутылки',
        'howto.step1.desc': '2 литра продукции Coca Cola в Small',
        'howto.step2.title': 'Сфотографируй чек',
        'howto.step3.title': 'Загрузи на сайт',
        'howto.step3.desc': 'SMALL-COCACOLA.kz',
        
        // Условия
        'terms.title': 'Условия акции',
        'terms.products': 'Участвующие товары',
        'terms.products.desc': 'Coca-Cola, Coca-Cola Zero Sugar, Fanta, Sprite (2L)',
        'terms.minimum': 'Минимальная покупка',
        'terms.minimum.desc': 'две бутылки объемом 2 литра любой комбинации',
        'terms.rules': 'ПОЛНЫЕ ПРАВИЛА АКЦИИ',
        
        // Победители
        'winners.title': 'ПОБЕДИТЕЛИ',
        'winners.date': 'Дата',
        'winners.prize': 'Приз',
        'winners.phone': 'Номер',
        
        // Мои чеки
        'checks.title': 'МОИ ЧЕКИ',
        'checks.chances': 'КОЛИЧЕСТВО ВАШИХ ШАНСОВ',
        'checks.upload': 'Загрузить чек',
        
        // Загрузка чека
        'upload.title': 'ЗАГРУЗИТЬ ЧЕК',
        'upload.gallery': 'Галерея',
        'upload.camera': 'Камера',
        'upload.submit': 'ОТПРАВИТЬ',
        
        // Успех
        'success.title': 'Ваш чек<br>отправлен на проверку',
        'success.button': 'УРА!',
        
        // Футер
        'footer.rules': 'Полные правила акции',
		'verify.title': 'ВЕРИФИКАЦИЯ',
		'verify.subtitle': 'Укажите Код из SMS',
		'verify.button': 'Войти',
		'verify.nocode': 'Не получили код?',
		'verify.resend': 'Отправить код повторно',
		'receipt.error.image': 'Выберите изображение',
		'receipt.error.size': 'Файл слишком большой. Максимум 10MB',
		'receipt.error.select': 'Выберите фото чека',
		'receipt.error.connection': 'Ошибка соединения с сервером',
		'receipt.loading': 'ЗАГРУЗКА...',
		'receipt.next.title': 'Ближайший розыгрыш:',
		'receipt.next.left': 'Осталось',
		'receipt.next.today': 'Сегодня!',
		'receipt.next.finished': 'Все розыгрыши завершены',
		'receipt.empty': 'У вас пока нет загруженных чеков',
		'receipt.status.passed': 'Розыгрыш прошёл',
		'receipt.status.upcoming': 'Ожидается',
		'receipt.in': 'Через',
		'receipt.today': 'Сегодня розыгрыш!',
		'receipt.confirm.delete': 'Удалить этот чек?',
    },
    
    kk: {
        // Меню
        'menu.auth': 'АВТОРИЗАЦИЯ',
        'menu.checks': 'МЕНІҢ ЧЕКТЕРІМ',
        'menu.upload': 'ЧЕК ЖҮКТЕУ',
        'menu.lang': 'РУССКИЙ',
        
        // Авторизация
        'auth.title': 'АВТОРИЗАЦИЯ',
        'auth.subtitle': 'Телефон нөміріңізді енгізіңіз',
        'auth.confirm': 'Телефон нөмірін қайта енгізіңіз',
        'auth.button': 'ЖАЛҒАСТЫРУ',
        'auth.button.login': 'КІРУ',
        'auth.hint': 'Авторизация үшін<br>телефон нөмірін енгізіңіз',
        'auth.hint.confirm': 'Растау үшін телефон<br>нөмірін қайталаңыз',
        'auth.error.phone': 'Толық телефон нөмірін енгізіңіз',
        'auth.error.confirm': 'Телефон нөмірін қайталаңыз',
        'auth.error.mismatch': 'Телефон нөмірлері сәйкес келмейді',
        
        // Главная
        'main.title1': 'ЖАҢА ЖЫЛДЫ',
        'main.title2': 'ЖАРҚЫН ЕТ',
        'main.subtitle': 'Coca Cola өнімдерін сатып ал және апталық және бас жүлделер ұтысына қатыс!',
        'main.dates': '2025 жылдың 8 желтоқсаны — 2026 жылдың 11 қаңтары',
        'main.upload': 'Чек жүктеу',
        
        // Призы
        'prizes.title': 'ЖҮЛДЕЛЕР',
        'prizes.note': 'Жүлделер суреттерден өзгеше болуы мүмкін',
        'prizes.main': 'БАС ЖҮЛДЕ',
        'prizes.main.desc': 'Тур операторынан екі адамға ұмытылмас саяхатқа үш сертификат',
        
        // Как участвовать
        'howto.title': 'Қалай қатысуға болады?',
        'howto.step1.title': '2 бөтелке сатып ал',
        'howto.step1.desc': 'Small-да 2 литр Coca Cola өнімі',
        'howto.step2.title': 'Чекті суретке түсір',
        'howto.step3.title': 'Сайтқа жүкте',
        'howto.step3.desc': 'SMALL-COCACOLA.kz',
        
        // Условия
        'terms.title': 'Акция шарттары',
        'terms.products': 'Қатысушы тауарлар',
        'terms.products.desc': 'Coca-Cola, Coca-Cola Zero Sugar, Fanta, Sprite (2L)',
        'terms.minimum': 'Ең аз сатып алу',
        'terms.minimum.desc': 'кез келген комбинациядағы 2 литрлік екі бөтелке',
        'terms.rules': 'АКЦИЯНЫҢ ТОЛЫҚ ЕРЕЖЕЛЕРІ',
        
        // Победители
        'winners.title': 'ЖЕҢІМПАЗДАР',
        'winners.date': 'Күні',
        'winners.prize': 'Жүлде',
        'winners.phone': 'Нөмір',
        
        // Мои чеки
        'checks.title': 'МЕНІҢ ЧЕКТЕРІМ',
        'checks.chances': 'СІЗДІҢ МҮМКІНДІКТЕРІҢІЗ',
        'checks.upload': 'Чек жүктеу',
        
        // Загрузка чека
        'upload.title': 'ЧЕК ЖҮКТЕУ',
        'upload.gallery': 'Галерея',
        'upload.camera': 'Камера',
        'upload.submit': 'ЖІБЕРУ',
        
        // Успех
        'success.title': 'Сіздің чегіңіз<br>тексеруге жіберілді',
        'success.button': 'УРА!',
        
        // Футер
        'footer.rules': 'Акцияның толық ережелері',
		'verify.title': 'ВЕРИФИКАЦИЯ',
		'verify.subtitle': 'SMS кодын енгізіңіз',
		'verify.button': 'Кіру',
		'verify.nocode': 'Код алмадыңыз ба?',
		'verify.resend': 'Кодты қайта жіберу',
		'receipt.error.image': 'Суретті таңдаңыз',
		'receipt.error.size': 'Файл тым үлкен. Максимум 10MB',
		'receipt.error.select': 'Чек фотосын таңдаңыз',
		'receipt.error.connection': 'Сервермен байланыс қатесі',
		'receipt.loading': 'ЖҮКТЕЛУДЕ...',
		'receipt.next.title': 'Жақын арадағы ұтыс:',
		'receipt.next.left': 'Қалды',
		'receipt.next.today': 'Бүгін!',
		'receipt.next.finished': 'Барлық ұтыстар аяқталды',
		'receipt.empty': 'Сізде әлі жүктелген чектер жоқ',
		'receipt.status.passed': 'Ұтыс өтті',
		'receipt.status.upcoming': 'Күтілуде',
		'receipt.in': 'Арқылы',
		'receipt.today': 'Бүгін ұтыс!',
		'receipt.confirm.delete': 'Бұл чекті жою керек пе?',
		
    }
};

// ===== Переключение языка =====
const Lang = {
    current: localStorage.getItem('lang') || 'ru',
    
    init() {
        this.applyLanguage(this.current);
        this.initSwitcher();
    },
    
    initSwitcher() {
        const switcher = document.querySelector('.hamburgerUserText');
        if (switcher) {
            switcher.style.cursor = 'pointer';
            switcher.addEventListener('click', () => {
                this.toggle();
            });
        }
    },
    
	toggle() {
		const newLang = this.current === 'ru' ? 'kk' : 'ru';
		this.current = newLang;
		localStorage.setItem('lang', newLang);
		this.applyLanguage(newLang);
		
		// Перерендер динамического контента
		if (typeof Receipts !== 'undefined') {
			Receipts.loadUserReceipts();
		}
	},
    
    applyLanguage(lang) {
        const t = translations[lang];
        
        // Находим все элементы с data-lang и меняем текст
        document.querySelectorAll('[data-lang]').forEach(el => {
            const key = el.getAttribute('data-lang');
            if (t[key]) {
                el.innerHTML = t[key];
            }
        });
        
        // Обновляем кнопку переключения
        const switcher = document.querySelector('.hamburgerUserText');
        if (switcher) {
            switcher.textContent = t['menu.lang'];
        }
    },
    
    // Получить перевод по ключу
    get(key) {
        return translations[this.current][key] || key;
    }
};

document.addEventListener('DOMContentLoaded', () => {
    Lang.init();
});