<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style/main.css?v=1.1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<script src="{{ asset('script/sms-auth.js') }}?v=1.1"></script>
	<link rel="stylesheet" href="{{ asset('style/receipts.css') }}">
	<script src="{{ asset('script/receipts.js') }}"></script>
	<script src="{{ asset('script/lang.js') }}"></script>
    <title>Coca-Cola x Small — Новогодняя акция 2025</title>
	<link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}">
</head>
<body>

<div class="main">
    <div class="header-container">
        <img src="assets/small.png" alt="small">
        <img class="logo" src="assets/logo.png" alt="logo">
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
					<h2 data-lang="auth.title">АВТОРИЗАЦИЯ</h2>
				</div>
				<div class="auth-content">
					<form class="auth-form" id="auth-form">
						<p class="auth-formText" data-lang="auth.subtitle">Укажите номер телефона</p>
						
						<p class="auth-error" id="auth-error" style="display: none; color: #ff4444; text-align: center; margin-bottom: 15px; font-family: 'Roboto', sans-serif;"></p>
						
						<input type="tel" placeholder="+7 (000) 000-00-00" required id="phone-input" inputmode="numeric">
						
						<!-- Второе поле (скрыто по умолчанию) -->
						<div id="phone-confirm-wrapper" style="display: none;">
							<p class="auth-formText" data-lang="auth.confirm">Введите повторно номер телефона</p>
							<input type="tel" placeholder="+7 (000) 000-00-00" id="phone-confirm-input" inputmode="numeric" style="width:100%; padding:15px 0px;">
						</div>
						
						<button type="submit" class="auth-submit" data-lang="auth.button">ПРОДОЛЖИТЬ</button>
					</form>
					<div class="auth-links">
						<p class="auth-links-text" data-lang="auth.hint">
							Введите номер телефона<br>
							для авторизации
						</p>
					</div>
				</div>
			</div>

            <!-- Окно верификации -->
            <div class="auth-verification">
                <div class="auth-header">
                    <h2 data-lang="verify.title">ВЕРИФИКАЦИЯ</h2>
                </div>
                <div class="verification-content">
                    <p class="verification-text" data-lang="verify.subtitle">Укажите Код из SMS</p>
					
					<p class="verification-error" id="verification-error" style="display: none; color: #ff4444; text-align: center; margin-bottom: 15px; font-family: 'Roboto', sans-serif;"></p>
                    <form class="verification-form" id="verification-form">
                        <div class="code-inputs">
                            <input type="tel" inputmode="numeric" maxlength="1" class="code-input" data-index="0">
							<input type="tel" inputmode="numeric" maxlength="1" class="code-input" data-index="1">
							<input type="tel" inputmode="numeric" maxlength="1" class="code-input" data-index="2">
							<input type="tel" inputmode="numeric" maxlength="1" class="code-input" data-index="3">
                        </div>
                        <button type="submit" class="verification-submit" data-lang="verify.button">Войти</button>
                    </form>
                    <div class="verification-links">
                        <a class="no-code" href="" data-lang="verify.nocode">Не получили код?</a>
                        <a href="#" class="resend-code" id="resend-code" data-lang="verify.resend">Отправить код повторно</a>
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

                    <div class="checks-info">
                        <div class="blockDown">
                            <a class="downChecks" href=""><span data-lang="checks.upload">Загрузить чек</span> <img src="assets/arrowWhiteR.png"> </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="auth-checks-add">
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
            </div>

            <div class="auth-upload-success">
                <div class="auth-header">
                    <img src='assets/gerlanda.png' alt="gerlanda">
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
        <div>
            <img class="mainBlockImgTwo" src="img/santa.png" alt="small">
            <img src="assets/wolna.png" alt="" class="mainBlockImgWolna">
        </div>
        <div class="blockDown">
            <a class="downCheck upload-link" href=""><span data-lang="main.upload">Загрузить чек</span> <img src="assets/arrowRight.png"> </a>
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
                        <img class="" src="img/elka.png" alt="">
                    </div>
                </div>

                <div class=" slide">
                    <div class="card">
                        <img class="" src="img/tarelka.png" alt="">
                    </div>
                </div>

                <div class=" slide">
                    <div class="card">
                       <img src="img/zapas.png" alt="" class="">
                    </div>
                </div>

                <div class=" slide">
                    <div class="card">
                        <img class="" src="img/sertifikat.png" alt="">
                    </div>
                </div>

                <div class=" slide">
                    <div class="card">
                        <img class="" src="img/kamera.png" alt="">
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
        <img class="mainBlockImgTotal" src="img/totalPrize.png" alt="small">
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
                    <p class="blockParticipateTextDescription" data-lang="howto.step1.desc">2 литра продукции Coca Cola в Small</p>
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
                    <p class="blockParticipateTextDescription" data-lang="howto.step3.desc">SMALL-COCACOLA.kz</p>
                </div>
            </div>
        </div>

        <div class="blockDown">
            <a class="downCheck" href=""><span data-lang="main.upload">Загрузить чек</span> <img src="assets/arrowRight.png"> </a>
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
            <p class="ysloviaTextDescription" data-lang="terms.minimum.desc">две бутылки объемом 2 литра любой комбинации</p>

            <div class="blockDown">
                <a class="checkPrav" href=""><span data-lang="terms.rules">ПОЛНЫЕ ПРАВИЛА АКЦИИ</span> <img src="assets/pdf.png" alt=""> </a>
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
                                <th data-lang="winners.date">Дата</th>
                                <th data-lang="winners.prize">Приз</th>
                                <th data-lang="winners.phone">Номер</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>—</td>
                                <td>—</td>
                                <td>—</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

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
                                <th data-lang="winners.date">Дата</th>
                                <th data-lang="winners.prize">Приз</th>
                                <th data-lang="winners.phone">Номер</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>—</td>
                                <td>—</td>
                                <td>—</td>
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
                                <th data-lang="winners.date">Дата</th>
                                <th data-lang="winners.prize">Приз</th>
                                <th data-lang="winners.phone">Номер</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>—</td>
                                <td>—</td>
                                <td>—</td>
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
                                <th data-lang="winners.date">Дата</th>
                                <th data-lang="winners.prize">Приз</th>
                                <th data-lang="winners.phone">Номер</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>—</td>
                                <td>—</td>
                                <td>—</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

<footer>
    <div class="main">
        <div class="footer-content">
            <img class="footerLogo" src="assets/cocacola.png" alt="">

            <a class="footerContentText" href="" data-lang="footer.rules">Полные правила акции</a>

            <p class="footerContentUnder">2025 - 2026 Сoca-Cola x Small</p>
        </div>
    </div>
</footer>

</div>
<script src="script/index.js"></script>

</body>
</html>