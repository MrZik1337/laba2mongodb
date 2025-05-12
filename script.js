function htmlspecialchars(str) {
    if (typeof str !== 'string') {
        return str;
    }
    return str.replace(/&/g, "&amp;")
              .replace(/</g, "&lt;")
              .replace(/>/g, "&gt;")
              .replace(/"/g, "&quot;")
              .replace(/'/g, "&#039;");
}

function saveLastQuery(params) {
    try {
        const dataToStore = {
            params: params,
            timestamp: new Date().toISOString() 
        };
        localStorage.setItem('lastQueryInfo', JSON.stringify(dataToStore));
        console.log('Параметри запиту збережено у localStorage.');
    } catch (e) {
        console.error('Помилка при збереженні у localStorage:', e);
    }
}

function displayLastQueryInfo() {
    const lastQueryInfoDiv = document.getElementById('lastQueryInfo');
    if (!lastQueryInfoDiv) {
        console.error("Елемент #lastQueryInfo не знайдено.");
        return;
    }

    try {
        const storedDataString = localStorage.getItem('lastQueryInfo');

        if (storedDataString) {
            const storedData = JSON.parse(storedDataString);
            const params = storedData.params;
            const timestamp = storedData.timestamp ? new Date(storedData.timestamp).toLocaleString() : 'невідомий час';

            let infoHtml = `Останній запит (з localStorage) від ${timestamp}:<br>`;

            if (params && params.query_type) { 
                switch (params.query_type) {
                    case 'wards_by_nurse':
                        infoHtml += `Тип: Палати медсестри<br>Медсестра: ${htmlspecialchars(params.nurse_name || 'Не вказано')}`;
                        break;
                    case 'nurses_by_department':
                        infoHtml += `Тип: Медсестри відділення<br>Відділення: ${htmlspecialchars(params.department_name || 'Не вказано')}`; // Використовуємо department_name
                        break;
                    case 'duties_by_shift':
                        infoHtml += `Тип: Чергування (зміна/відділення)<br>Зміна: ${htmlspecialchars(params.shift_name || 'Не вказано')}<br>Відділення: ${htmlspecialchars(params.department3_name || 'Не вказано')}`; // Використовуємо department3_name
                        break;
                    default:
                        infoHtml += `Невідомий тип запиту.`;
                }

            } else {
                infoHtml += `Збережені параметри мають невідомий формат.`;
            }

            lastQueryInfoDiv.innerHTML = infoHtml;

        } else {
            lastQueryInfoDiv.innerHTML = "Історія запитів відсутня в localStorage.";
        }
    } catch (e) {
        console.error('Помилка при зчитуванні з localStorage:', e);
        lastQueryInfoDiv.innerHTML = "Помилка при завантаженні історії запитів.";
    }
}
window.onload = displayLastQueryInfo;
