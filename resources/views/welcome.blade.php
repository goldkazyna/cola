<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="{{ asset('style/main.css') }}?v={{ filemtime(public_path('style/main.css')) }}">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<script src="{{ asset('script/sms-auth.js') }}?v={{ filemtime(public_path('script/sms-auth.js')) }}"></script>
	<link rel="stylesheet" href="{{ asset('style/receipts.css') }}?v={{ filemtime(public_path('style/receipts.css')) }}">
	<script src="{{ asset('script/receipts.js') }}?v={{ filemtime(public_path('script/receipts.js')) }}"></script>
	<script src="{{ asset('script/lang.js') }}?v={{ filemtime(public_path('script/lang.js')) }}"></script>
    <title>Coca-Cola x Small — Новогодняя акция 2025</title>
	<link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}">
</head>
<body>

<div class="main">
    <div class="header-container">
        <img src="assets/small.png" alt="small">
        <img class="logo" src="assets/Frame 23.svg" alt="logo">
        <!-- Гамбургер меню -->
        <div class="hamburger-menu">
            <input type="checkbox" id="menu-toggle">
            <label for="menu-toggle" class="menu-button">
                <!-- Гамбургер иконка -->
                <span class="hamburger-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
                <span class="back-arrow">
                    <img src="assets/arrowMenu.png" alt="back-arrow">
                </span>
            </label>
            <nav class="menu-content">
                <a href="#" class="auth-link" data-lang="menu.auth">АВТОРИЗАЦИЯ</a>
                <a href="#" class="checks-link" data-lang="menu.checks">МОИ ЧЕКИ</a>
                <a href="#" class="upload-link" data-lang="menu.upload">ЗАГРУЗИТЬ ЧЕК</a>
                <div class="hamburgerUser">
                    <p class="hamburgerUserText" data-lang="menu.lang">ҚАЗАҚША</p>
                </div>
                <img class="lineHumberger" src="assets/lineTotalPrize.png" alt="line">
            </nav>

            <!-- Окно авторизации -->
			<div class="auth-window">
				<div class="auth-header">
					<h2>АВТОРИЗАЦИЯ</h2>
				</div>
				<div class="auth-content">
					<form class="auth-form" id="auth-form">
						<p class="auth-formText">Заполните данные</p>
						
						<p class="auth-error" id="auth-error" style="display: none; color: #ff4444; text-align: center; margin-bottom: 15px; font-family: 'Roboto', sans-serif;"></p>
						
						<input type="text" placeholder="Имя" required id="name-input">
						<input type="text" placeholder="Фамилия" required id="surname-input">
						<input type="text" placeholder="Город" required id="city-input">
						<input type="tel" placeholder="+7 (000) 000-00-00" required id="phone-input" inputmode="numeric">
						
						<label class="agree-checkbox">
							<input type="checkbox" id="agree-rules" required checked>
							<span>Я согласен с <a href="upload/Правила_и_условия_участия_Сделай новый год ярче с Coca-Cola.pdf" target="_blank">правилами акции</a></span>
						</label>
						
						<button type="submit" class="auth-submit" id="auth-submit">ПОЛУЧИТЬ SMS КОД</button>
					</form>
				</div>
			</div>

			<!-- Окно верификации -->
			<!-- Окно верификации -->
			<div class="auth-verification">
				<div class="auth-header">
					<h2>ВВЕДИТЕ КОД</h2>
				</div>
				<div class="verification-content">
					<p class="verification-text">Введите код из SMS</p>
					
					<p class="verification-error" id="verification-error" style="display: none; color: #ff4444; text-align: center; margin-bottom: 15px; font-family: 'Roboto', sans-serif;"></p>
					
					<!-- Ввод SMS кода -->
					<form class="verification-form" id="verification-form">
						<div class="code-inputs">
							<input type="text" maxlength="1" class="code-input" inputmode="numeric">
							<input type="text" maxlength="1" class="code-input" inputmode="numeric">
							<input type="text" maxlength="1" class="code-input" inputmode="numeric">
							<input type="text" maxlength="1" class="code-input" inputmode="numeric">
						</div>
						<button type="submit" class="verification-submit">ВОЙТИ</button>
					</form>
					
					<!-- Кнопка переключения на fallback -->
					<a href="#" class="no-sms-link" id="no-sms-link">Не пришло SMS? Подтвердить по номеру</a>
					
					<!-- Fallback — ввод номера повторно -->
					<div class="fallback-section" id="fallback-section" style="display: none;">
						<p class="fallback-text">Введите номер телефона повторно для подтверждения:</p>
						<input type="tel" placeholder="+7 (000) 000-00-00" id="fallback-phone-input" inputmode="numeric">
						<button type="button" class="verification-submit" id="fallback-submit">ПОДТВЕРДИТЬ</button>
					</div>
				</div>
			</div>

            <!-- Окно Мои чеки -->
            <div class="auth-checks">
                <div class="auth-header">
                    <h2 data-lang="checks.title">МОИ ЧЕКИ</h2>
                </div>
                <div class="checks-content">
                    <!-- Количество шансов -->
                    <div class="chances-count">
                        <p class="chances-number">0</p>
                        <p class="chances-text" data-lang="checks.chances">КОЛИЧЕСТВО ВАШИХ ШАНСОВ</p>
                    </div>

                    <!-- Сетка чеков -->
                    <div class="checks-grid">
						<!-- Чеки загружаются динамически -->
					</div>

                    <!--<div class="checks-info">
                        <div class="blockDown">
                            <a class="downChecks" href=""><span data-lang="checks.upload">Загрузить чек</span> <img src="assets/arrowWhiteR.png"> </a>
                        </div>
                    </div>-->
                </div>
            </div>

            <!--<div class="auth-checks-add">
                <div class="auth-header">
                    <h2 data-lang="upload.title">ЗАГРУЗИТЬ ЧЕК</h2>
                </div>
                <div class="checks-add-content">
                    <form class="upload-form" id="upload-form">
                        <div class="upload-area" id="upload-area">
							<input type="file" id="file-input" accept="image/*" style="display: none;">
							<input type="file" id="camera-input" accept="image/*" capture="environment" style="display: none;">
							
							<div class="upload-buttons">
								<button type="button" class="upload-option" id="gallery-btn">
									<img src="assets/upload-icon.png" alt="Галерея">
									<span data-lang="upload.gallery">Галерея</span>
								</button>
								<button type="button" class="upload-option" id="camera-btn">
									<img src="assets/upload-icon.png" alt="Камера">
									<span data-lang="upload.camera">Камера</span>
								</button>
							</div>
						</div>

                        <div class="uploaded-previews" id="uploaded-previews">
                        </div>

                        <button type="submit" class="upload-submit" disabled data-lang="upload.submit">ОТПРАВИТЬ</button>
                    </form>
                </div>
            </div>-->

            <div class="auth-upload-success">
                <div class="auth-header">
                    <img src='assets/gerlanda1.png' alt="gerlanda">
					
                </div>
                <div class="upload-success-content">
                    <div class="success-icon">
                        <img src="assets/sucsess-icon.png" alt="Успех">
                    </div>
                    <p class="success-title" data-lang="success.title">Ваш чек <br> отправлен на проверку</p>
                    <button class="success-button" id="success-button" data-lang="success.button">УРА!</button>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
.auth-header img {
    width: 100%;
    max-width: 400px; /* или сколько нужно */
    height: auto;
}
</style>
<div class="main">

    <section class="mainBlock">
    <div class="main">
        <div class="mainContainer">
            <h2 class="mainBlockText" data-lang="main.title1">СДЕЛАЙ НОВЫЙ ГОД</h2>
            <div class="mainBlockTextImg">
                <h2 class="mainBlockText" data-lang="main.title2">ЯРЧЕ С </h2>
                <img class="mainBlockImg" src="assets/cocacola.png" alt="small">
            </div>
        </div>

        <p class="mainBlockTitel" data-lang="main.subtitle">Покупай продукцию Coca Cola и участвуй <br>
            в розыгрыше еженедельных и главного приза!</p>

        <p class="mainBlockDescription" data-lang="main.dates">8 декабря 2025 — 11 января 2026</p>

        <img class="coloText" src="assets/coloText.png" alt="small">
        <div class="santa">
            <img class="mainBlockImgTwo" src="img/santa.png" alt="small">
            <img src="assets/wolna.png" alt="" class="mainBlockImgWolna">
        </div>
        <div class="blockDown">
            <!--<a class="downCheck upload-link" href=""><span data-lang="main.upload">Загрузить чек</span> <img src="assets/arrowRight.png"> </a>-->
        </div>
    </div>

    </section>


<section class="prize">
    <div class="main">
        <h2 class="prizeMainText" data-lang="prizes.title">ПРИЗЫ</h2>

        <div class="slider-container">
            <div class=" slider">
               <div class=" slide">
                    <div class="card">
                        <img class="" src="img/1/el3.svg" alt="">
                    </div>
                </div>
				<div class=" slide">
                    <div class="card">
                        <img class="" src="img/1/ta2.svg" alt="">
                    </div>
                </div>
				<div class=" slide">
                    <div class="card">
                        <img class="" src="img/1/co2.svg" alt="">
                    </div>
                </div>
				<div class=" slide">
                    <div class="card">
                        <img class="" src="img/1/se.svg" alt="">
                    </div>
                </div>
				<div class=" slide">
                    <div class="card">
                        <img class="" src="img/1/ph2.svg" alt="">
                    </div>
                </div>
				
                
            </div>

            <div class="slider-indicators">
                <span class="indicator active"></span>
                <span class="indicator"></span>
                <span class="indicator"></span>
                <span class="indicator"></span>
                <span class="indicator"></span>
            </div>

            <h2 class="prizeUnderText" data-lang="prizes.note">Призы могут отличаться от изображений</h2>
        </div>

    </div>
    <img class="linePrize" src="assets/linePrize.png" alt="">
</section>

<section class="totalPrize">
<div class="main">
    <h2 class="totalPrizeTextt" data-lang="prizes.main">ГЛАВНЫЙ ПРИЗ</h2>

    <div class="blockPrize">
        <img class="blockPrizeImg" src="assets/greenShar.png" alt="small">
        <img class="redShar" src="assets/redShar.png" alt="small">
        <img class="mainBlockImgTotal" src="img/1/tt2.png" alt="small">
        <img class="blockPrizeImgColaRed" src="assets/sharCola.png" alt="small">
        <img class="greenSharColaPrize" src="assets/greenSharCola.png" alt="small">
        <div class="blocktotalPrize">
            <p class="blocktotalPrizeText" data-lang="prizes.main.desc">
                Три сертификата от тур оператора <br>
                на незабываемое путешествие на двоих
            </p>
        </div>
    </div>

</div>
    <img class="linePrizeTotal" src="assets/lineTotalPrize.png" alt="">
</section>

    <section class="participate">
    <div class="main">
        <h2 class="participateText" data-lang="howto.title">Как участвовать?</h2>

        <div class="blockParticipate">

            <div class="blockParticipateInfo">
                <div class="blockParticipateImg">
                    <img class="blockParticipateImgs" src="assets/bulet.png" alt="">
                </div>
                <div class="blockParticipateText">
                    <h2 class="blockParticipateTextTitel" data-lang="howto.step1.title">Купи 2 бутылки </h2>
                    <p class="blockParticipateTextDescription" data-lang="howto.step1.desc">по 2 литра продукции Coca Cola в Small</p>
                </div>
            </div>

            <div class="blockParticipateInfo">
                <div class="blockParticipateImg">
                    <img class="blockParticipateImgs" src="assets/check.png" alt="">
                </div>
                <div class="blockParticipateText">
                    <h2 class="blockParticipateTextTitel" data-lang="howto.step2.title">Сфотографируй чек </h2>
                </div>
            </div>

            <div class="blockParticipateInfo">
                <div class="blockParticipateImg">
                    <img class="blockParticipateImgs" src="assets/gift.png" alt="">
                </div>
                <div class="blockParticipateText">
                    <h2 class="blockParticipateTextTitel" data-lang="howto.step3.title">Загрузи на сайт </h2>
                    <p class="blockParticipateTextDescription" data-lang="howto.step3.desc">COCACOLA-SMALL.KZ</p>
                </div>
            </div>
        </div>

        <div class="blockDown">
            <!--<a class="downCheck" href=""><span data-lang="main.upload">Загрузить чек</span> <img src="assets/arrowRight.png"> </a>-->
        </div>

    </div>

        <img class="lineParticipate" src="assets/linePrize.png" alt="">
    </section>


    <section class="yslovia">
        <div class="main">
            <h2 class="ysloviaText" data-lang="terms.title">Условия акции</h2>
            <h2 class="ysloviaTextTitle" data-lang="terms.products">Участвующие товары</h2>
            <p class="ysloviaTextDescription" data-lang="terms.products.desc">Coca-Cola, Coca-Cola Zero Sugar, Fanta, Sprite (2L)</p>

            <h2 class="ysloviaTextTitle" data-lang="terms.minimum">Минимальная покупка</h2>
            <p class="ysloviaTextDescription" data-lang="terms.minimum.desc">две любые бутылки объемом по 2 литра любой комбинации</p>

            <div class="blockDown">
                <a class="checkPrav" href="/upload/Правила_и_условия_участия_Сделай новый год ярче с Coca-Cola рус + каз.pdf"><span data-lang="terms.rules">ПОЛНЫЕ ПРАВИЛА АКЦИИ</span> <img src="assets/pdf.png" alt=""> </a>
            </div>
        </div>
        <img class="lineParticipate" src="assets/lineTotalPrize.png" alt="">
    </section>

    <section class="winer">
        <div class="main">
            <h2 class="winerText" data-lang="winners.title">ПОБЕДИТЕЛИ</h2>

            <div class="header-accordion">
    <div class="accordion-header" onclick="toggleAccordion(this)">
        <span class="accordion-title">Победители 15.12.2025</span>
        <span class="accordion-arrow">▼</span>
    </div>
    <div class="accordion-content">
        <div class="winners-table">
            <table>
                <thead>
                <tr>
                    <th data-lang="winners.name" style="width:40%">Имя</th>
                    <th data-lang="winners.prize" style="width:20%">Приз</th>
                    <th data-lang="winners.phone" style="width:40%">Номер</th>
                </tr>
                </thead>
                <tbody>
                <!-- Ёлка -->
                <tr>
                    <td>Манакпаева Жанар</td>
                    <td>Ёлка</td>
                    <td>+7 771 *** ** 95</td>
                </tr>
                <tr>
                    <td>Шельпяков Михаил</td>
                    <td>Ёлка</td>
                    <td>+7 707 *** ** 61</td>
                </tr>
                <tr>
                    <td>Шарабарина Алена</td>
                    <td>Ёлка</td>
                    <td>+7 705 *** ** 86</td>
                </tr>
                <!-- Камера -->
                <tr>
                    <td>Турганбек Зарина</td>
                    <td>Камера</td>
                    <td>+7 707 *** ** 53</td>
                </tr>
                <!-- Посуда -->
                <tr>
                    <td>Шахаров Азамат</td>
                    <td>Посуда</td>
                    <td>+7 707 *** ** 11</td>
                </tr>
                <tr>
                    <td>Самбетбаев Айдар</td>
                    <td>Посуда</td>
                    <td>+7 701 *** ** 34</td>
                </tr>
                <tr>
                    <td>Нуртазина Лаура</td>
                    <td>Посуда</td>
                    <td>+7 777 *** ** 25</td>
                </tr>
                <!-- Сертификат 20 000 -->
                <tr>
                    <td>Александр</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 771 *** ** 15</td>
                </tr>
                <tr>
                    <td>Шерхан</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 707 *** ** 96</td>
                </tr>
                <tr>
                    <td>Сымбат</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 705 *** ** 99</td>
                </tr>
                <tr>
                    <td>Акмарал</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 701 *** ** 78</td>
                </tr>
                <tr>
                    <td>Даулет</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 771 *** ** 12</td>
                </tr>
                <tr>
                    <td>Ринат</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 702 *** ** 82</td>
                </tr>
                <tr>
                    <td>Федор</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 708 *** ** 80</td>
                </tr>
                <tr>
                    <td>Ришат</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 771 *** ** 17</td>
                </tr>
                <tr>
                    <td>Артем</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 778 *** ** 88</td>
                </tr>
                <tr>
                    <td>Нурхан</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 707 *** ** 72</td>
                </tr>
                <!-- Coca-Cola -->
                <tr>
                    <td>Бисиков Рустам</td>
                    <td>Coca-Cola</td>
                    <td>+7 708 *** ** 58</td>
                </tr>
                <tr>
                    <td>Айтжан Аида</td>
                    <td>Coca-Cola</td>
                    <td>+7 707 *** ** 93</td>
                </tr>
                <tr>
                    <td>Сметова Сапаргуль</td>
                    <td>Coca-Cola</td>
                    <td>+7 747 *** ** 24</td>
                </tr>
                <tr>
                    <td>Сейтказин Баянбек</td>
                    <td>Coca-Cola</td>
                    <td>+7 775 *** ** 09</td>
                </tr>
                <tr>
                    <td>Егимбаева Диана</td>
                    <td>Coca-Cola</td>
                    <td>+7 705 *** ** 85</td>
                </tr>
                <tr>
                    <td>Жадигер Медет</td>
                    <td>Coca-Cola</td>
                    <td>+7 707 *** ** 05</td>
                </tr>
                <tr>
                    <td>Сагандыкова Меруерт</td>
                    <td>Coca-Cola</td>
                    <td>+7 775 *** ** 64</td>
                </tr>
                <tr>
                    <td>Сейтказин Руслан</td>
                    <td>Coca-Cola</td>
                    <td>+7 775 *** ** 10</td>
                </tr>
                <tr>
                    <td>Кусаинов Альнур</td>
                    <td>Coca-Cola</td>
                    <td>+7 700 *** ** 52</td>
                </tr>
                <tr>
                    <td>Абитова Алия</td>
                    <td>Coca-Cola</td>
                    <td>+7 776 *** ** 00</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>
.accordion-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
    background: #910101;
}

.accordion-content.open {
    display: flex;
    max-height: 400px;  /* Увеличил высоту */
    max-width: 100%;
    justify-content: center;
    overflow-y: auto;   /* Добавляем скролл */
}

/* Красивый скроллбар */
.accordion-content::-webkit-scrollbar {
    width: 6px;
}

.accordion-content::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 3px;
}

.accordion-content::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.4);
    border-radius: 3px;
}

.accordion-content::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.6);
}
</style>
<div class="header-accordion">
    <div class="accordion-header" onclick="toggleAccordion(this)">
        <span class="accordion-title">Победители 22.12.2025</span>
        <span class="accordion-arrow">▼</span>
    </div>
    <div class="accordion-content">
        <div class="winners-table">
            <table>
                <thead>
                <tr>
                    <th data-lang="winners.name" style="width:40%">Имя</th>
                    <th data-lang="winners.prize" style="width:20%">Приз</th>
                    <th data-lang="winners.phone" style="width:40%">Номер</th>
                </tr>
                </thead>
                <tbody>
                <!-- Ёлка -->
                <tr>
                    <td>Шамшадинова Улжан</td>
                    <td>Ёлка</td>
                    <td>+7 705 *** ** 20</td>
                </tr>
                <tr>
                    <td>Сарсенбаев Альтаир</td>
                    <td>Ёлка</td>
                    <td>+7 706 *** ** 77</td>
                </tr>
                <tr>
                    <td>Бактолыкова Гульмира</td>
                    <td>Ёлка</td>
                    <td>+7 701 *** ** 42</td>
                </tr>
                <!-- Камера -->
                <tr>
                    <td>Байбусинов Амир</td>
                    <td>Камера</td>
                    <td>+7 747 *** ** 61</td>
                </tr>
                <!-- Посуда -->
                <tr>
                    <td>Садыкова Алия</td>
                    <td>Посуда</td>
                    <td>+7 778 *** ** 47</td>
                </tr>
                <tr>
                    <td>Тунгушбаева Молдир</td>
                    <td>Посуда</td>
                    <td>+7 778 *** ** 58</td>
                </tr>
                <tr>
                    <td>Егимбаева Айзада</td>
                    <td>Посуда</td>
                    <td>+7 777 *** ** 93</td>
                </tr>
                <!-- Coca-Cola -->
                <tr>
                    <td>Кривущенко Дмитрий</td>
                    <td>Coca-Cola</td>
                    <td>+7 702 *** ** 18</td>
                </tr>
                <tr>
                    <td>Шипенков Александр</td>
                    <td>Coca-Cola</td>
                    <td>+7 777 *** ** 93</td>
                </tr>
                <tr>
                    <td>Джетыбаева Кунсулу</td>
                    <td>Coca-Cola</td>
                    <td>+7 775 *** ** 66</td>
                </tr>
                <tr>
                    <td>Бейсикеев Амир</td>
                    <td>Coca-Cola</td>
                    <td>+7 705 *** ** 35</td>
                </tr>
                <tr>
                    <td>Мурзабек Газиза</td>
                    <td>Coca-Cola</td>
                    <td>+7 707 *** ** 98</td>
                </tr>
                <tr>
                    <td>Карабалина Альбина</td>
                    <td>Coca-Cola</td>
                    <td>+7 702 *** ** 11</td>
                </tr>
                <tr>
                    <td>Байшахметова Акдана</td>
                    <td>Coca-Cola</td>
                    <td>+7 702 *** ** 27</td>
                </tr>
                <tr>
                    <td>Қуанышева Дана</td>
                    <td>Coca-Cola</td>
                    <td>+7 771 *** ** 68</td>
                </tr>
                <tr>
                    <td>Бегалина Арайлым</td>
                    <td>Coca-Cola</td>
                    <td>+7 705 *** ** 31</td>
                </tr>
                <tr>
                    <td>Салибаева Жанна</td>
                    <td>Coca-Cola</td>
                    <td>+7 707 *** ** 79</td>
                </tr>
                <!-- Сертификат 20 000 -->
                <tr>
                    <td>Карибжанова Жанэль</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 701 *** ** 00</td>
                </tr>
                <tr>
                    <td>Қабыш Әлемгүл</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 775 *** ** 09</td>
                </tr>
                <tr>
                    <td>Сарбасова Рената</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 771 *** ** 70</td>
                </tr>
                <tr>
                    <td>Камзаев Абай</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 707 *** ** 27</td>
                </tr>
                <tr>
                    <td>Быбченко Наталья</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 778 *** ** 01</td>
                </tr>
                <tr>
                    <td>Ержанов Куаныш</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 777 *** ** 68</td>
                </tr>
                <tr>
                    <td>Райымбек Альмира</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 778 *** ** 16</td>
                </tr>
                <tr>
                    <td>Ши Манзура</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 707 *** ** 27</td>
                </tr>
                <tr>
                    <td>Мустафина Камилла</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 705 *** ** 18</td>
                </tr>
                <tr>
                    <td>Аширбаева Куралай</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 747 *** ** 21</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="header-accordion">
    <div class="accordion-header" onclick="toggleAccordion(this)">
        <span class="accordion-title">Победители 29.12.2025</span>
        <span class="accordion-arrow">▼</span>
    </div>
    <div class="accordion-content">
        <div class="winners-table">
            <table>
                <thead>
                <tr>
                    <th data-lang="winners.name" style="width:40%">Имя</th>
                    <th data-lang="winners.prize" style="width:20%">Приз</th>
                    <th data-lang="winners.phone" style="width:40%">Номер</th>
                </tr>
                </thead>
                <tbody>
                <!-- Ёлка -->
                <tr>
                    <td>Исатаева Асель</td>
                    <td>Ёлка</td>
                    <td>+7 708 *** ** 80</td>
                </tr>
                <tr>
                    <td>Жакупова Зарина</td>
                    <td>Ёлка</td>
                    <td>+7 700 *** ** 67</td>
                </tr>
                <tr>
                    <td>Жандаулет Лейла</td>
                    <td>Ёлка</td>
                    <td>+7 778 *** ** 07</td>
                </tr>
                <tr>
                    <td>Кабаев Азамат</td>
                    <td>Ёлка</td>
                    <td>+7 778 *** ** 19</td>
                </tr>
                <tr>
                    <td>Уразалин Марат</td>
                    <td>Ёлка</td>
                    <td>+7 701 *** ** 29</td>
                </tr>
                <tr>
                    <td>Умбеталиева Жумагуль</td>
                    <td>Ёлка</td>
                    <td>+7 747 *** ** 10</td>
                </tr>
                <tr>
                    <td>Садвакасова Рауза</td>
                    <td>Ёлка</td>
                    <td>+7 707 *** ** 85</td>
                </tr>
                <tr>
                    <td>Жексембекова Айгерим</td>
                    <td>Ёлка</td>
                    <td>+7 705 *** ** 73</td>
                </tr>
                <tr>
                    <td>Беликова Татьяна</td>
                    <td>Ёлка</td>
                    <td>+7 777 *** ** 88</td>
                </tr>
                <!-- Камера -->
                <tr>
                    <td>Байназаров Сагындык</td>
                    <td>Камера</td>
                    <td>+7 707 *** ** 69</td>
                </tr>
                <!-- Посуда -->
                <tr>
                    <td>Кушкинбаева Манат</td>
                    <td>Посуда</td>
                    <td>+7 777 *** ** 17</td>
                </tr>
                <tr>
                    <td>Кумарбеков Алиби</td>
                    <td>Посуда</td>
                    <td>+7 705 *** ** 50</td>
                </tr>
                <tr>
                    <td>Ожогина Галина</td>
                    <td>Посуда</td>
                    <td>+7 707 *** ** 14</td>
                </tr>
                <!-- Coca-Cola -->
                <tr>
                    <td>Аманжол Аяна</td>
                    <td>Coca-Cola</td>
                    <td>+7 747 *** ** 52</td>
                </tr>
                <tr>
                    <td>Адилбек Анар</td>
                    <td>Coca-Cola</td>
                    <td>+7 747 *** ** 81</td>
                </tr>
                <tr>
                    <td>Паримбекова Мадина</td>
                    <td>Coca-Cola</td>
                    <td>+7 702 *** ** 73</td>
                </tr>
                <tr>
                    <td>Самбаева Шолпан</td>
                    <td>Coca-Cola</td>
                    <td>+7 701 *** ** 95</td>
                </tr>
                <tr>
                    <td>Нусиппаева Гаухар</td>
                    <td>Coca-Cola</td>
                    <td>+7 701 *** ** 16</td>
                </tr>
                <tr>
                    <td>Жадикова Мария</td>
                    <td>Coca-Cola</td>
                    <td>+7 771 *** ** 04</td>
                </tr>
                <tr>
                    <td>Габбасова Гайнижамал</td>
                    <td>Coca-Cola</td>
                    <td>+7 701 *** ** 43</td>
                </tr>
                <tr>
                    <td>Әбдіғапбар Ару</td>
                    <td>Coca-Cola</td>
                    <td>+7 747 *** ** 24</td>
                </tr>
                <tr>
                    <td>Темирбаев Мейржан</td>
                    <td>Coca-Cola</td>
                    <td>+7 702 *** ** 84</td>
                </tr>
                <tr>
                    <td>Найда Римма</td>
                    <td>Coca-Cola</td>
                    <td>+7 700 *** ** 90</td>
                </tr>
                <!-- Сертификат 20 000 -->
                <tr>
                    <td>Мусаиф Тунгыш</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 747 *** ** 72</td>
                </tr>
                <tr>
                    <td>Джамбаев Алмас</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 707 *** ** 38</td>
                </tr>
                <tr>
                    <td>Байтурсынов Ерлан</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 777 *** ** 16</td>
                </tr>
                <tr>
                    <td>Жантлеуов Темирбек</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 747 *** ** 80</td>
                </tr>
                <tr>
                    <td>Каримова Айман</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 776 *** ** 12</td>
                </tr>
                <tr>
                    <td>Омаров Алексей</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 705 *** ** 74</td>
                </tr>
                <tr>
                    <td>Ильясова Индира</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 771 *** ** 76</td>
                </tr>
                <tr>
                    <td>Анфилофьева Диана</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 707 *** ** 59</td>
                </tr>
                <tr>
                    <td>Уалиева Акбота</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 705 *** ** 88</td>
                </tr>
                <tr>
                    <td>Мусиенко Алена</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 747 *** ** 66</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="header-accordion">
    <div class="accordion-header" onclick="toggleAccordion(this)">
        <span class="accordion-title">Победители 05.01.2026</span>
        <span class="accordion-arrow">▼</span>
    </div>
    <div class="accordion-content">
        <div class="winners-table">
            <table>
                <thead>
                <tr>
                    <th data-lang="winners.name" style="width:40%">Имя</th>
                    <th data-lang="winners.prize" style="width:20%">Приз</th>
                    <th data-lang="winners.phone" style="width:40%">Номер</th>
                </tr>
                </thead>
                <tbody>
                <!-- Камера -->
                <tr>
                    <td>Жузбаев Али</td>
                    <td>Камера</td>
                    <td>+7 702 *** ** 66</td>
                </tr>
                <!-- Посуда -->
                <tr>
                    <td>Айткалиева Акмарал</td>
                    <td>Посуда</td>
                    <td>+7 705 *** ** 76</td>
                </tr>
                <tr>
                    <td>Skorodumov Danil</td>
                    <td>Посуда</td>
                    <td>+7 747 *** ** 32</td>
                </tr>
                <tr>
                    <td>Хальбаева Асунта</td>
                    <td>Посуда</td>
                    <td>+7 777 *** ** 77</td>
                </tr>
                <!-- Coca-Cola -->
                <tr>
                    <td>Магзомова Аида</td>
                    <td>Coca-Cola</td>
                    <td>+7 701 *** ** 43</td>
                </tr>
                <tr>
                    <td>Отежан Нурай</td>
                    <td>Coca-Cola</td>
                    <td>+7 771 *** ** 86</td>
                </tr>
                <tr>
                    <td>Торешова Жадыра</td>
                    <td>Coca-Cola</td>
                    <td>+7 747 *** ** 89</td>
                </tr>
                <tr>
                    <td>Арикян Самвел</td>
                    <td>Coca-Cola</td>
                    <td>+7 706 *** ** 01</td>
                </tr>
                <tr>
                    <td>Бадамбек Аяру</td>
                    <td>Coca-Cola</td>
                    <td>+7 775 *** ** 57</td>
                </tr>
                <tr>
                    <td>Ермаханова Алмагуль</td>
                    <td>Coca-Cola</td>
                    <td>+7 775 *** ** 20</td>
                </tr>
                <tr>
                    <td>Мырзагали Жанель</td>
                    <td>Coca-Cola</td>
                    <td>+7 708 *** ** 97</td>
                </tr>
                <tr>
                    <td>Абильдаева Гульнур</td>
                    <td>Coca-Cola</td>
                    <td>+7 707 *** ** 16</td>
                </tr>
                <tr>
                    <td>Килибаева Айнаш</td>
                    <td>Coca-Cola</td>
                    <td>+7 707 *** ** 85</td>
                </tr>
                <tr>
                    <td>Мусин Муслим</td>
                    <td>Coca-Cola</td>
                    <td>+7 705 *** ** 67</td>
                </tr>
                <!-- Сертификат 20 000 -->
                <tr>
                    <td>Турдахун Жаркынай</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 776 *** ** 45</td>
                </tr>
                <tr>
                    <td>Боранбай Шадияр</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 771 *** ** 28</td>
                </tr>
                <tr>
                    <td>Сулейменова Элмира</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 771 *** ** 41</td>
                </tr>
                <tr>
                    <td>Налитенко Максим</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 705 *** ** 90</td>
                </tr>
                <tr>
                    <td>Углиманова Лаура</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 775 *** ** 73</td>
                </tr>
                <tr>
                    <td>Жашарбек Диана</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 707 *** ** 22</td>
                </tr>
                <tr>
                    <td>Конысбаева Айгерим</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 777 *** ** 88</td>
                </tr>
                <tr>
                    <td>Мухиден Ботакоз</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 702 *** ** 83</td>
                </tr>
                <tr>
                    <td>Айдынбаева Аида</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 701 *** ** 36</td>
                </tr>
                <tr>
                    <td>Оразбаева Алия</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 701 *** ** 28</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
	<div class="header-accordion">
    <div class="accordion-header" onclick="toggleAccordion(this)">
        <span class="accordion-title">Победители 16.01.2026</span>
        <span class="accordion-arrow">▼</span>
    </div>
    <div class="accordion-content">
        <div class="winners-table">
            <table>
                <thead>
                <tr>
                    <th data-lang="winners.name" style="width:40%">Имя</th>
                    <th data-lang="winners.prize" style="width:20%">Приз</th>
                    <th data-lang="winners.phone" style="width:40%">Номер</th>
                </tr>
                </thead>
                <tbody>
                <!-- Тур -->
                <tr>
                    <td>Алиаскар Шерхан</td>
                    <td>Тур на двоих</td>
                    <td>+7 707 *** ** 96</td>
                </tr>
                <tr>
                    <td>Бержанов Руслан</td>
                    <td>Тур на двоих</td>
                    <td>+7 707 *** ** 87</td>
                </tr>
                <tr>
                    <td>Сарбасова Рената</td>
                    <td>Тур на двоих</td>
                    <td>+7 771 *** ** 70</td>
                </tr>
                <!-- Камера -->
                <tr>
                    <td>Боранбаева Айгерим</td>
                    <td>Камера</td>
                    <td>+7 702 *** ** 00</td>
                </tr>
                <!-- Посуда -->
                <tr>
                    <td>Орынбаева Перизат</td>
                    <td>Посуда</td>
                    <td>+7 707 *** ** 72</td>
                </tr>
                <tr>
                    <td>Галиакберова Зульфия</td>
                    <td>Посуда</td>
                    <td>+7 771 *** ** 49</td>
                </tr>
                <tr>
                    <td>Ивановна Марина</td>
                    <td>Посуда</td>
                    <td>+7 776 *** ** 23</td>
                </tr>
                <!-- Coca-Cola -->
                <tr>
                    <td>Какенова Сандия</td>
                    <td>Coca-Cola</td>
                    <td>+7 700 *** ** 70</td>
                </tr>
                <tr>
                    <td>Тайман Багдат</td>
                    <td>Coca-Cola</td>
                    <td>+7 707 *** ** 87</td>
                </tr>
                <tr>
                    <td>Шамшадин Мухамед</td>
                    <td>Coca-Cola</td>
                    <td>+7 707 *** ** 87</td>
                </tr>
                <tr>
                    <td>Сейтказин Замир</td>
                    <td>Coca-Cola</td>
                    <td>+7 775 *** ** 50</td>
                </tr>
                <tr>
                    <td>Жаксылык Гулбахрам</td>
                    <td>Coca-Cola</td>
                    <td>+7 747 *** ** 75</td>
                </tr>
                <tr>
                    <td>Габасова Гайнижамал</td>
                    <td>Coca-Cola</td>
                    <td>+7 701 *** ** 43</td>
                </tr>
                <tr>
                    <td>Селюков Владислав</td>
                    <td>Coca-Cola</td>
                    <td>+7 707 *** ** 04</td>
                </tr>
                <tr>
                    <td>Лялька Алена</td>
                    <td>Coca-Cola</td>
                    <td>+7 777 *** ** 23</td>
                </tr>
                <tr>
                    <td>Бай Абулхаир</td>
                    <td>Coca-Cola</td>
                    <td>+7 708 *** ** 48</td>
                </tr>
                <tr>
                    <td>Ауелбек Эльмира</td>
                    <td>Coca-Cola</td>
                    <td>+7 747 *** ** 68</td>
                </tr>
                <!-- Сертификат 20 000 -->
                <tr>
                    <td>Подлеснов Игорь</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 777 *** ** 54</td>
                </tr>
                <tr>
                    <td>Хабибулов Заман</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 747 *** ** 13</td>
                </tr>
                <tr>
                    <td>Мендыбаева Алма</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 777 *** ** 72</td>
                </tr>
                <tr>
                    <td>Жолбаева Калияш</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 747 *** ** 19</td>
                </tr>
                <tr>
                    <td>Шамшадин Мадина</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 778 *** ** 69</td>
                </tr>
                <tr>
                    <td>Абдикеримова Асель</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 701 *** ** 10</td>
                </tr>
                <tr>
                    <td>Абдулла Аслан</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 707 *** ** 24</td>
                </tr>
                <tr>
                    <td>Зайнутлинов Ринат</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 707 *** ** 69</td>
                </tr>
                <tr>
                    <td>Ахметов Ануар</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 700 *** ** 06</td>
                </tr>
                <tr>
                    <td>Искаков Асет</td>
                    <td>Сертификат 20 000 ₸</td>
                    <td>+7 747 *** ** 58</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>		
			<!-- Ёлка -->

        </div>
    </section>

<footer>
    <div class="main">
        <div class="footer-content">
            <img class="footerLogo" src="assets/cocacola.png" alt="">

            <a class="footerContentText" href="/upload/Правила_и_условия_участия_Сделай новый год ярче с Coca-Cola рус + каз.pdf" data-lang="footer.rules">Полные правила акции</a>
			<p class="footerContactInfo">
				Есть вопросы? Пишите: <a href="tel:+77070345012">+7 707 034 5012</a>
			</p>
            <p class="footerContentUnder">2025 - 2026 Сoca-Cola x Small</p>
        </div>
    </div>
</footer>

</div>
<script src="script/index.js"></script>
<style>
/* Чекбокс согласия с правилами */
.footerContactInfo {
    font-family: 'Roboto', sans-serif;
    font-size: 14px;
    color: rgba(255, 255, 255, 0.7);
    text-align: center;
}

.footerContactInfo a {
    color: #fff;
    text-decoration: none;
    font-weight: 500;
}

.footerContactInfo a:hover {
    text-decoration: underline;
}
.agree-checkbox {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    cursor: pointer;
    font-family: 'Roboto', sans-serif;
    font-size: 14px;
    color: #333;
}

.agree-checkbox input[type="checkbox"] {
    width: 20px;
    height: 20px;
    margin-top: 2px;
    cursor: pointer;
    accent-color: #910101;
}

.agree-checkbox span {
    line-height: 1.4;
}

.agree-checkbox a {
    color: #910101;
    text-decoration: underline;
}

.agree-checkbox a:hover {
    color: #b10202;
}

/* Неактивная кнопка */
.auth-submit:disabled {
    background: #ccc;
    cursor: not-allowed;
}


@media (min-width: 768px) {
    .main {
            max-width: 100%;
    }
	.mainBlockImgWolna, .coloText, .linePrize, .linePrizeTotal, .lineParticipate, .lineParticipate{
		display:none;
	}
	.mainBlock{
		    background: radial-gradient(circle at 50% 45%, rgba(255, 0, 0, 0.8) 0%, transparent 0%), radial-gradient(circle at 70% 60%, rgba(255, 0, 0, 0.6) 0%, transparent 0%), radial-gradient(circle at center, #910101 0%, #910101 0%);
	}
	.santa{
		text-align:center;
	}
	.winer .main{
		width:450px;
	}
	.mainBlockText{
		font-size: 158px;
	}
	.mainBlockTitel, .mainBlockDescription{
		    font-size: 55px;
	}
}

/* Fallback секция */
.fallback-section {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.fallback-text {
    font-family: 'Roboto', sans-serif;
    font-size: 16px;
    color: #333;
    text-align: center;
    margin-bottom: 15px;
}

.fallback-section input {
    width: 100%;
    padding: 15px 20px;
    border: none;
    background: #6666662e;
    border-radius: 8px;
    font-family: 'Roboto', sans-serif;
    font-size: 20px;
    text-align: center;
    margin-bottom: 15px;
}
/* Ссылка "Не пришло SMS?" */
.no-sms-link {
    display: block;
    text-align: center;
    color: #910101;
    text-decoration: none;
    font-family: 'K_TCCCUnity', sans-serif;
    font-size: 20px;
    font-weight: 600;
    margin-top: 30px;
    padding: 15px;
    background: rgba(145, 1, 1, 0.1);
    border-radius: 10px;
    transition: all 0.3s ease;
}

.no-sms-link:hover {
    background: rgba(145, 1, 1, 0.2);
    color: #b10202;
}
</style>
</body>
</html>