<?php
require_once __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Конференция "Наука и технологии"</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <nav>
        <a href="#register">Регистрация</a>
        <a href="#participants">Участники</a>
        <a href="#about">О конференции</a>
    </nav>
</header>

<main class="container">
    <h1>Конференция «Наука и технологии»</h1>

    <div id="result-card" class="card"></div>

    <section class="card" id="register">
        <h2>Форма регистрации</h2>
        <form id="reg-form">
            <div class="form-grid">
                <div class="field field-full">
                    <label for="full_name">ФИО *</label>
                    <input type="text" id="full_name" name="full_name" placeholder="Иванов Иван Иванович">
                    <span class="err" id="err-full_name"></span>
                </div>

                <div class="field">
                    <label for="phone">Контактный телефон *</label>
                    <input type="tel" id="phone" name="phone" placeholder="+79991234567">
                    <span class="err" id="err-phone"></span>
                </div>

                <div class="field">
                    <label for="email">Электронная почта *</label>
                    <input type="email" id="email" name="email" placeholder="ivanov@mail.ru">
                    <span class="err" id="err-email"></span>
                </div>

                <div class="field">
                    <label for="section">Секция конференции *</label>
                    <select id="section" name="section">
                        <option value="">— выберите —</option>
                        <option value="math">Математика</option>
                        <option value="physics">Физика</option>
                        <option value="informatics">Информатика</option>
                    </select>
                    <span class="err" id="err-section"></span>
                </div>

                <div class="field">
                    <label for="birth_date">Дата рождения</label>
                    <input type="date" id="birth_date" name="birth_date">
                    <span class="err" id="err-birth_date"></span>
                </div>

                <div class="field field-full">
                    <label>Доклад *</label>
                    <div class="radios">
                        <label><input type="radio" name="has_report" id="has-report" value="1"> есть доклад</label>
                        <label><input type="radio" name="has_report" value="0" checked> без доклада</label>
                    </div>
                    <span class="err" id="err-has_report"></span>
                </div>

                <div class="field field-full" id="topic-wrap" style="display:none">
                    <label for="report_topic">Тема доклада</label>
                    <input type="text" id="report_topic" name="report_topic" placeholder="Название темы">
                    <span class="err" id="err-report_topic"></span>
                </div>

                <div class="field field-full">
                    <button type="submit">Отправить</button>
                </div>
            </div>
        </form>
    </section>

    <section class="card" id="participants">
        <div class="head-row">
            <h2>Зарегистрированные участники</h2>
            <button id="refresh-btn" class="btn-refresh" type="button">Обновить</button>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ФИО</th>
                        <th>Телефон</th>
                        <th>E-mail</th>
                        <th>Дата рождения</th>
                        <th>Секция</th>
                        <th>Доклад</th>
                        <th>Тема доклада</th>
                    </tr>
                </thead>
                <tbody id="participants-tbody"></tbody>
            </table>
        </div>
    </section>

    <section class="card" id="about">
        <h2>О конференции</h2>
        <p>Ежегодная научная конференция студентов и аспирантов. Три секции: математика, физика и информатика. Участие свободное, регистрация обязательна.</p>
    </section>
</main>

<script src="script.js"></script>
</body>
</html>
