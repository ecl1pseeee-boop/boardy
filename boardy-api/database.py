# database.py — подключение к MySQL (aiomysql)
#
# aiomysql — асинхронный драйвер.
# await — не блокирует event loop при запросе к БД.
# Обычный mysql.connector заблокировал бы, как time.sleep.

import aiomysql

DB_CONFIG = {
    'host': 'mysql',
    'port': 3306,
    'user': 'boardy',
    'password': 'changeme',
    'db': 'boardy_api',
    'charset': 'utf8mb4',
}

async def get_db():
    return await aiomysql.connect(**DB_CONFIG)

async def db_query(query: str, *args):
    conn = await get_db()
    try:
        async with conn.cursor(aiomysql.DictCursor) as cur:
            await cur.execute(query, args)
            return await cur.fetchall()
    finally:
        conn.close()


async def db_query_one(query: str, *args):
    conn = await get_db()
    try:
        async with conn.cursor(aiomysql.DictCursor) as cur:
            await cur.execute(query, args)
            return await cur.fetchone()
    finally:
        conn.close()


async def db_insert(query: str, *args) -> int:
    conn = await get_db()
    try:
        async with conn.cursor() as cur:
            await cur.execute(query, args)
            insert_id = cur.lastrowid
            await conn.commit()
            return insert_id
    finally:
        conn.close()


async def db_execute(query: str, *args) -> None:
    conn = await get_db()
    try:
        async with conn.cursor() as cur:
            await cur.execute(query, args)
            await conn.commit()
    finally:
        conn.close()