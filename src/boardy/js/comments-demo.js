// Паттерн: загрузить массив из API и отрисовать
//
// fetch — HTTP-запрос из браузера (как curl)
// await — ждём ответ от сервера
// .json() — парсим JSON в JS-объект
// .map() — для каждого элемента создаём HTML
// .join('') — склеиваем массив строк в одну
// innerHTML — вставляем в контейнер

const API = 'https://api.gorgeous.ai-info.ru';
const POST_ID = 1;

async function loadItems() {
    const res = await fetch(`${API}/comments`);
    const data = await res.json();
    document.getElementById('list').innerHTML = data.comments.map(item =>
        ` 
        <div> 
            <strong>${esc(item.author_id)}</strong> 
            <p>${esc(item.body)}</p>
        </div> 
        `).join('');
}

loadItems();

// Паттерн: экранирование HTML
// Без этого пользователь может вставить <script>
// и выполнить произвольный JS в браузерах других людей
//
// textContent автоматически экранирует спецсимволы
// innerHTML — вставляет как HTML (опасно!)

document.getElementById('btn').addEventListener('click', async () => {
    const body = document.getElementById('body').value.trim();
    if (!body) return;
    await fetch(`${API}/posts/${POST_ID}/comments`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({body: body})
    });
    document.getElementById('body').value = '';
    loadItems();
});


// Паттерн: экранирование HTML
// Без этого пользователь может вставить <script>
// и выполнить произвольный JS в браузерах других людей
//
// textContent автоматически экранирует спецсимволы
// innerHTML — вставляет как HTML (опасно!)

function esc(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}