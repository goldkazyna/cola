<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="style/main.css">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<script src="{{ asset('script/sms-auth.js') }}"></script>
	<link rel="stylesheet" href="{{ asset('style/receipts.css') }}">
	<script src="{{ asset('script/receipts.js') }}"></script>
    <title>Document</title>
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
                <a href="#" class="auth-link">АВТОРИЗАЦИЯ</a>
                <a href="#" class="checks-link">МОИ ЧЕКИ</a>
                <a href="#" class="upload-link">ЗАГРУЗИТЬ ЧЕК</a>
                <div class="hamburgerUser">
                    <p class="hamburgerUserText">КАЗАКША</p>
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
                        <p class="auth-formText">Укажите номер телефона</p>
						
						<p class="auth-error" id="auth-error" style="display: none; color: #ff4444; text-align: center; margin-bottom: 15px; font-family: 'Roboto', sans-serif;"></p>
                        <input type="tel" placeholder="+7 (000) 000-00-00" required id="phone-input">
                        <button type="submit" class="auth-submit">ПОЛУЧИТЬ SMS КОД</button>
                    </form>
                    <div class="auth-links">
                        <p class="auth-links-text">
                            Мы отправим смс с кодом на <br>
                            номер +7 (000) 000-00-00
                        </p>
                    </div>
                </div>
            </div>

            <!-- Окно верификации -->
            <div class="auth-verification">
                <div class="auth-header">
                    <h2>ВЕРИФИКАЦИЯ</h2>
                </div>
                <div class="verification-content">
                    <p class="verification-text">Укажите Код из SMS</p>
					
					<p class="verification-error" id="verification-error" style="display: none; color: #ff4444; text-align: center; margin-bottom: 15px; font-family: 'Roboto', sans-serif;"></p>
                    <form class="verification-form" id="verification-form">
                        <div class="code-inputs">
                            <input type="text" maxlength="1" class="code-input" data-index="0">
                            <input type="text" maxlength="1" class="code-input" data-index="1">
                            <input type="text" maxlength="1" class="code-input" data-index="2">
                            <input type="text" maxlength="1" class="code-input" data-index="3">
                        </div>
                        <button type="submit" class="verification-submit">Войти</button>
                    </form>
                    <div class="verification-links">
                        <a class="no-code" href="">Не получили код?</a>
                        <a href="#" class="resend-code" id="resend-code">Отправить код повторно</a>
                    </div>
                </div>
            </div>

            <!-- Окно Мои чеки -->
            <div class="auth-checks">
                <div class="auth-header">
                    <h2>МОИ ЧЕКИ</h2>
                </div>
                <div class="checks-content">
                    <!-- Количество шансов -->
                    <div class="chances-count">
                        <p class="chances-number">0</p>
                        <p class="chances-text">КОЛИЧЕСТВО ВАШИХ ШАНСОВ</p>
                    </div>

                    <!-- Сетка чеков -->
                    <div class="checks-grid">
						<!-- Чеки загружаются динамически -->
					</div>

                    <div class="checks-info">
                        <div class="blockDown">
                                <a class="downChecks" href="">Загрузить чек <img src="assets/arrowWhiteR.png"> </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="auth-checks-add">
                <div class="auth-header">
                    <h2>ЗАГРУЗИТЬ ЧЕК</h2>
                </div>
                <div class="checks-add-content">
                    <form class="upload-form" id="upload-form">
                        <div class="upload-area" id="upload-area">
							<input type="file" id="file-input" accept="image/*" style="display: none;">
							<input type="file" id="camera-input" accept="image/*" capture="environment" style="display: none;">
							
							<div class="upload-buttons">
								<button type="button" class="upload-option" id="gallery-btn">
									<img src="assets/upload-icon.png" alt="Галерея">
									<span>Галерея</span>
								</button>
								<button type="button" class="upload-option" id="camera-btn">
									<img src="assets/upload-icon.png" alt="Камера">
									<span>Камера</span>
								</button>
							</div>
						</div>

                        <div class="uploaded-previews" id="uploaded-previews">
                        </div>

                        <button type="submit" class="upload-submit" disabled>ОТПРАВИТЬ </button>
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
                    <p class="success-title">Ваш чек <br> отправлен на проверку</p>
                    <button class="success-button" id="success-button">УРА!</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="main">



    <section class="mainBlock">
    <div class="main">
        <div class="mainContainer">
            <h2 class="mainBlockText">СДЕЛАЙ НОВЫЙ ГОД</h2>
            <div class="mainBlockTextImg">
                <h2 class="mainBlockText">ЯРЧЕ С </h2>
                <img class="mainBlockImg" src="assets/cocacola.png" alt="small">
            </div>
        </div>


        <p class="mainBlockTitel">Покупай продукцию Coca Cola и участвуй <br>
            в розыгрыше еженедельных и главного приза!</p>

        <p class="mainBlockDescription">8 декабря 2025 — 11 января 2026</p>

        <img class="coloText" src="assets/coloText.png" alt="small">
        <div>
            <img class="mainBlockImgTwo" src="img/santa.png" alt="small">
            <img src="assets/wolna.png" alt="" class="mainBlockImgWolna">
        </div>
        <div class="blockDown">
            <a class="downCheck upload-link" href="">Загрузить чек <img src="assets/arrowRight.png"> </a>
        </div>
    </div>

    </section>


<section class="prize">
    <div class="main">
        <h2 class="prizeMainText">ПРИЗЫ</h2>

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

            <h2 class="prizeUnderText">Призы могут отличаться от изображений</h2>
        </div>

    </div>
    <img class="linePrize" src="assets/linePrize.png" alt="">
</section>

<section class="totalPrize">
<div class="main">
    <h2 class="totalPrizeTextt">ГЛАВНЫЙ ПРИЗ</h2>

    <div class="blockPrize">
        <img class="blockPrizeImg" src="assets/greenShar.png" alt="small">
        <img class="redShar" src="assets/redShar.png" alt="small">
        <img class="mainBlockImgTotal" src="img/totalPrize.png" alt="small">
        <img class="blockPrizeImgColaRed" src="assets/sharCola.png" alt="small">
        <img class="greenSharColaPrize" src="assets/greenSharCola.png" alt="small">
        <div class="blocktotalPrize">
            <p class="blocktotalPrizeText">
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
        <h2 class="participateText">Как участвовать?</h2>

        <div class="blockParticipate">

            <div class="blockParticipateInfo">
                <div class="blockParticipateImg">
                    <img class="blockParticipateImgs" src="assets/bulet.png" alt="">
                </div>
                <div class="blockParticipateText">
                    <h2 class="blockParticipateTextTitel">Купи 2 бутылки </h2>
                    <p class="blockParticipateTextDescription">2 литра продукции Coca Cola в Small</p>
                </div>
            </div>

            <div class="blockParticipateInfo">
                <div class="blockParticipateImg">
                    <img class="blockParticipateImgs" src="assets/check.png" alt="">
                </div>
                <div class="blockParticipateText">
                    <h2 class="blockParticipateTextTitel">Сфотографируй чек </h2>
                </div>
            </div>

            <div class="blockParticipateInfo">
                <div class="blockParticipateImg">
                    <img class="blockParticipateImgs" src="assets/gift.png" alt="">
                </div>
                <div class="blockParticipateText">
                    <h2 class="blockParticipateTextTitel">Загрузи на сайт </h2>
                    <p class="blockParticipateTextDescription">SMALL-COCACOLA.kz</p>
                </div>
            </div>
        </div>

        <div class="blockDown">
            <a class="downCheck" href="">Загрузить чек <img src="assets/arrowRight.png"> </a>
        </div>

    </div>

        <img class="lineParticipate" src="assets/linePrize.png" alt="">
    </section>


    <section class="yslovia">
        <div class="main">
            <h2 class="ysloviaText">Условия акции</h2>
            <h2 class="ysloviaTextTitle">Участвующие товары</h2>
            <p class="ysloviaTextDescription">Coca-Cola, Coca-Cola Zero Sugar, Fanta, Sprite (2L)</p>

            <h2 class="ysloviaTextTitle">Минимальная покупка</h2>
            <p class="ysloviaTextDescription">две бутылки объемом 2 литра любой комбинации</p>

            <div class="blockDown">
                <a class="checkPrav" href="">ПОЛНЫЕ ПРАВИЛА АКЦИИ <img src="assets/pdf.png" alt=""> </a>
            </div>
        </div>
        <img class="lineParticipate" src="assets/lineTotalPrize.png" alt="">
    </section>

    <section class="winer">
        <div class="main">
            <h2 class="winerText">ПОБЕДИТЕЛИ</h2>


            <div class="header-accordion">
                <div class="accordion-header" onclick="toggleAccordion(this)">
                    <span class="accordion-title">Победители 20.20.2021</span>
                    <span class="accordion-arrow">▼</span>
                </div>

                <div class="accordion-content">
                    <div class="winners-table">
                        <table>
                            <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Приз</th>
                                <th>Номер</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>20.10.2021</td>
                                <td>iPhone 13</td>
                                <td>+7 XXX XXX-XX-XX</td>
                            </tr>
                            <tr>
                                <td>15.10.2021</td>
                                <td>MacBook Pro</td>
                                <td>+7 XXX XXX-XX-XX</td>
                            </tr>
                            <tr>
                                <td>10.10.2021</td>
                                <td>AirPods Pro</td>
                                <td>+7 XXX XXX-XX-XX</td>
                            </tr>
                            <tr>
                                <td>05.10.2021</td>
                                <td>PlayStation 5</td>
                                <td>+7 XXX XXX-XX-XX</td>
                            </tr>
                            <tr>
                                <td>01.10.2021</td>
                                <td>Apple Watch</td>
                                <td>+7 XXX XXX-XX-XX</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="header-accordion">
                <div class="accordion-header" onclick="toggleAccordion(this)">
                    <span class="accordion-title">Победители 20.20.2025</span>
                    <span class="accordion-arrow">▼</span>
                </div>

                <div class="accordion-content">
                    <div class="winners-table">
                        <table>
                            <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Приз</th>
                                <th>Номер</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>20.10.2021</td>
                                <td>iPhone 13</td>
                                <td>+7 XXX XXX-XX-XX</td>
                            </tr>
                            <tr>
                                <td>15.10.2021</td>
                                <td>MacBook Pro</td>
                                <td>+7 XXX XXX-XX-XX</td>
                            </tr>
                            <tr>
                                <td>10.10.2021</td>
                                <td>AirPods Pro</td>
                                <td>+7 XXX XXX-XX-XX</td>
                            </tr>
                            <tr>
                                <td>05.10.2021</td>
                                <td>PlayStation 5</td>
                                <td>+7 XXX XXX-XX-XX</td>
                            </tr>
                            <tr>
                                <td>01.10.2021</td>
                                <td>Apple Watch</td>
                                <td>+7 XXX XXX-XX-XX</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="header-accordion">
                <div class="accordion-header" onclick="toggleAccordion(this)">
                    <span class="accordion-title">Победители 20.20.2025</span>
                    <span class="accordion-arrow">▼</span>
                </div>

                <div class="accordion-content">
                    <div class="winners-table">
                        <table>
                            <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Приз</th>
                                <th>Номер</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>20.10.2021</td>
                                <td>iPhone 13</td>
                                <td>+7 XXX XXX-XX-XX</td>
                            </tr>
                            <tr>
                                <td>15.10.2021</td>
                                <td>MacBook Pro</td>
                                <td>+7 XXX XXX-XX-XX</td>
                            </tr>
                            <tr>
                                <td>10.10.2021</td>
                                <td>AirPods Pro</td>
                                <td>+7 XXX XXX-XX-XX</td>
                            </tr>
                            <tr>
                                <td>05.10.2021</td>
                                <td>PlayStation 5</td>
                                <td>+7 XXX XXX-XX-XX</td>
                            </tr>
                            <tr>
                                <td>01.10.2021</td>
                                <td>Apple Watch</td>
                                <td>+7 XXX XXX-XX-XX</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="header-accordion">
                <div class="accordion-header" onclick="toggleAccordion(this)">
                    <span class="accordion-title">Победители 20.20.2025</span>
                    <span class="accordion-arrow">▼</span>
                </div>

                <div class="accordion-content">
                    <div class="winners-table">
                        <table>
                            <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Приз</th>
                                <th>Номер</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>20.10.2021</td>
                                <td>iPhone 13</td>
                                <td>+7 XXX XXX-XX-XX</td>
                            </tr>
                            <tr>
                                <td>15.10.2021</td>
                                <td>MacBook Pro</td>
                                <td>+7 XXX XXX-XX-XX</td>
                            </tr>
                            <tr>
                                <td>10.10.2021</td>
                                <td>AirPods Pro</td>
                                <td>+7 XXX XXX-XX-XX</td>
                            </tr>
                            <tr>
                                <td>05.10.2021</td>
                                <td>PlayStation 5</td>
                                <td>+7 XXX XXX-XX-XX</td>
                            </tr>
                            <tr>
                                <td>01.10.2021</td>
                                <td>Apple Watch</td>
                                <td>+7 XXX XXX-XX-XX</td>
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

            <a class="footerContentText" href="">Полные правила акции</a>

            <p class="footerContentUnder">2025 - 2026 Сoca-Cola x Small</p>
        </div>
    </div>
</footer>

</div>
<script src="script/index.js"></script>


</body>
</html>