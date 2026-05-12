const { useState, useEffect } = React;
const API = 'https://api.gorgeous.ai-info.ru';
const POST_ID = 1;

function ItemList() {
    const [items, setItems] = useState([]);
    const [text, setText] = useState(''); // Для новой записи
    const [editId, setEditId] = useState(null); // Для редактирования
    const [editText, setEditText] = useState('');

    const load = async () => {
        try {
            const res = await fetch(`${API}/comments`);
            const data = await res.json();
            setItems(data.comments || []);
        } catch (e) { console.error("Ошибка загрузки:", e); }
    };

    const add = async () => {
        if (!text.trim()) return;
        await fetch(`${API}/posts/${POST_ID}/comments`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ body: text })
        });
        setText('');
        load();
    };

    const save = async (id) => {
        await fetch(`${API}/comments/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ body: editText })
        });
        setEditId(null);
        load();
    };

    const del = async (id) => {
        if (!confirm('Удалить?')) return;
        await fetch(`${API}/comments/${id}`, { method: 'DELETE' });
        load();
    };

    useEffect(() => { load(); }, []);

    return (
        <div className="container mt-4">
            {/* Форма добавления */}
            <div className="input-group mb-4">
                <input
                    className="form-control"
                    placeholder="Новый комментарий"
                    value={text}
                    onChange={e => setText(e.target.value)}
                />
                <button className="btn btn-primary" onClick={add}>Отправить</button>
            </div>

            {/* Список */}
            {items.map(item => (
                <div key={item.id} className="card mb-2">
                    <div className="card-body">
                        <strong>Автор ID: {item.author_id}</strong>

                        {editId === item.id ? (
                            <div className="input-group mt-2">
                                <input
                                    className="form-control"
                                    value={editText}
                                    onChange={e => setEditText(e.target.value)}
                                />
                                <button className="btn btn-success" onClick={() => save(item.id)}>✅</button>
                                <button className="btn btn-secondary" onClick={() => setEditId(null)}>✖</button>
                            </div>
                        ) : (
                            <div className="d-flex justify-content-between align-items-center">
                                <p className="mb-0">{item.body}</p>
                                <div>
                                    <button
                                        className="btn btn-sm btn-outline-secondary me-2"
                                        onClick={() => { setEditId(item.id); setEditText(item.body); }}
                                    >✏️</button>
                                    <button className="btn btn-sm btn-outline-danger" onClick={() => del(item.id)}>🗑️</button>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            ))}
        </div>
    );
}


ReactDOM.createRoot(document.getElementById('app')).render(<ItemList />)