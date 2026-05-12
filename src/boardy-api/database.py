# database.py — подключение к MySQL (aiomysql)
#
# aiomysql — асинхронный драйвер.
# await — не блокирует event loop при запросе к БД.
# Обычный mysql.connector заблокировал бы, как time.sleep.

import aiomysql

DB_CONFIG = {
    'host': '127.0.0.1',
    'port': 3306,
    'user': 'boardy',
    'password': 'student',
    'db': 'boardy',
    'charset': 'utf8mb4',
}

async def get_db():
    return await aiomysql.connect(**DB_CONFIG)
