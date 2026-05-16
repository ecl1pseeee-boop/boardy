from fastapi import APIRouter, WebSocket
from typing import List


router = APIRouter()

class ConnectionManager:

    def __init__(self):
        self.active: List[WebSocket] = []

    async def connect(self, ws: WebSocket):
        await ws.accept()
        self.active.append(ws)

    async def disconnect(self, ws: WebSocket):
        if ws in self.active:
            self.active.remove(ws)

    async def broadcast(self, message: dict):
        import json
        dead_connections = []
        for ws in self.active:
            try:
                await ws.send_text(json.dumps(message))
            except:
                dead_connections.append(ws)

        for ws in dead_connections:
            self.active.remove(ws)

manager = ConnectionManager()
@router.websocket("/ws")
async def websocket_endpoint(ws: WebSocket):
    await manager.connect(ws)
    try:
        while True:
            await ws.receive_text()
    except:
        await manager.disconnect(ws)