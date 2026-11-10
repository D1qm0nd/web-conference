console.log('script loaded');

function init() {
    var form = document.getElementById('reg-form');
    var phoneInput = document.getElementById('phone');
    var topicWrap = document.getElementById('topic-wrap');
    var refreshBtn = document.getElementById('refresh-btn');

    if (!form) return;

    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            var v = phoneInput.value;
            var plus = v.charAt(0) === '+';
            var digits = v.replace(/[^0-9]/g, '');
            if (digits.charAt(0) === '8') digits = '7' + digits.slice(1);
            if (digits.length > 11) digits = digits.slice(0, 11);
            if (digits.charAt(0) === '7' && digits.length === 11) {
                phoneInput.value = '+' + digits;
            } else {
                phoneInput.value = (plus ? '+' : '') + digits;
            }
        });
    }

    var radios = document.querySelectorAll('input[name="has_report"]');
    for (var i = 0; i < radios.length; i++) {
        radios[i].addEventListener('change', function() {
            if (document.getElementById('has-report').checked) {
                topicWrap.style.display = 'block';
            } else {
                topicWrap.style.display = 'none';
            }
        });
    }

    if (refreshBtn) refreshBtn.addEventListener('click', loadParticipants);

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        clearErrors();
        var errors = validateForm(form);
        var count = 0;
        for (var key in errors) count++;
        if (count > 0) {
            showErrors(errors);
            return;
        }
        submitForm(form);
    });

    loadParticipants();
    setInterval(loadParticipants, 15000);
}

function validateForm(form) {
    var errors = {};

    var fullName = form.elements['full_name'].value.trim();
    var phone = form.elements['phone'].value.trim();
    var email = form.elements['email'].value.trim();
    var section = form.elements['section'].value;
    var birthDate = form.elements['birth_date'].value;
    var checked = form.querySelector('input[name="has_report"]:checked');
    var hasReport = checked && checked.value === '1';
    var reportTopic = hasReport ? form.elements['report_topic'].value.trim() : '';

    var words = fullName.split(/\s+/);
    var ok = words.length === 3;
    if (ok) {
        for (var i = 0; i < words.length; i++) {
            if (!/^[А-ЯЁа-яё-]+$/.test(words[i])) ok = false;
        }
    }
    if (!ok) errors.full_name = 'Укажите фамилию, имя и отчество русскими буквами';

    var digits = phone.replace(/[^0-9]/g, '');
    if (digits.charAt(0) === '8') digits = '7' + digits.slice(1);
    if (!/^\+7\d{10}$/.test(phone) || digits.length !== 11) {
        errors.phone = 'Телефон: +7 и ровно 11 цифр, например +79991234567';
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        errors.email = 'Некорректный e-mail';
    }

    if (!section) errors.section = 'Выберите секцию';

    if (birthDate && !/^\d{4}-\d{2}-\d{2}$/.test(birthDate)) {
        errors.birth_date = 'Формат даты: ГГГГ-ММ-ДД';
    }

    if (hasReport && reportTopic.length < 3) {
        errors.report_topic = 'Укажите тему доклада (минимум 3 символа)';
    }

    return errors;
}

function clearErrors() {
    var spans = document.querySelectorAll('.err');
    for (var i = 0; i < spans.length; i++) spans[i].textContent = '';
    var inputs = document.querySelectorAll('.input-error');
    for (var j = 0; j < inputs.length; j++) inputs[j].classList.remove('input-error');
}

function showErrors(errors) {
    for (var field in errors) {
        var span = document.getElementById('err-' + field);
        if (span) span.textContent = errors[field];
        var input = document.getElementById(field);
        if (input) input.classList.add('input-error');
    }
}

function submitForm(form) {
    fetch('save.php', {
        method: 'POST',
        body: new FormData(form)
    })
    .then(function(res) { return res.json(); })
    .then(function(json) {
        if (json.ok) {
            showResult(json.participant);
            loadParticipants();
        } else {
            if (json.errors) showErrors(json.errors);
            else alert('Ошибка сохранения');
        }
    })
    .catch(function() {
        alert('Ошибка сети');
    });
}

function showResult(data) {
    var card = document.getElementById('result-card');
    var rows = [
        ['ФИО', data.full_name],
        ['Телефон', data.phone],
        ['E-mail', data.email],
        ['Дата рождения', data.birth_date || 'не указана'],
        ['Секция', data.section_name],
        ['Доклад', data.has_report ? 'есть' : 'нет'],
        ['Тема доклада', data.has_report ? data.report_topic : '-']
    ];
    var html = '<h3>Регистрация выполнена</h3>';
    for (var i = 0; i < rows.length; i++) {
        html += '<p><b>' + rows[i][0] + ':</b> ' + escapeHtml(rows[i][1]) + '</p>';
    }
    card.innerHTML = html;
    card.style.display = 'block';
    card.scrollIntoView({ behavior: 'smooth' });
}

function loadParticipants() {
    fetch('participants.php')
    .then(function(res) { return res.json(); })
    .then(function(json) {
        var tbody = document.getElementById('participants-tbody');
        if (!tbody) return;
        tbody.innerHTML = '';
        var list = json.participants || [];
        for (var i = 0; i < list.length; i++) {
            var p = list[i];
            var tr = document.createElement('tr');
            tr.innerHTML =
                '<td>' + escapeHtml(p.full_name) + '</td>' +
                '<td>' + escapeHtml(p.phone) + '</td>' +
                '<td>' + escapeHtml(p.email) + '</td>' +
                '<td>' + (p.birth_date ? escapeHtml(p.birth_date) : '-') + '</td>' +
                '<td>' + escapeHtml(p.section_name) + '</td>' +
                '<td>' + (p.has_report ? 'Да' : 'Нет') + '</td>' +
                '<td>' + (p.has_report && p.report_topic ? escapeHtml(p.report_topic) : '-') + '</td>';
            tbody.appendChild(tr);
        }
    })
    .catch(function() {});
}

function escapeHtml(s) {
    if (!s) return '';
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

if (document.readyState !== 'loading') init();
else document.addEventListener('DOMContentLoaded', init);
