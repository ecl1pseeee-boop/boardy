import jwt
from fastapi import Header, HTTPException

PUBLIC_KEY = open('oauth-public.key').read()

async def get_current_user(authorization: str = Header(None)):
    if not authorization or not authorization.startswith('Bearer '):
        raise HTTPException(status_code=401, detail='Token required')

    token = authorization.split(' ')[1]

    try:
        payload = jwt.decode(
            token, PUBLIC_KEY,
            algorithms=['RS256'],
            options={'verify_aud': False}
        )

        payload['sub'] = int(payload['sub'])
        return payload
    except jwt.ExpiredSignatureError:
        raise HTTPException(status_code=401, detail='Token expired')
    except jwt.InvalidTokenError:
        raise HTTPException(status_code=401, detail='Invalid token')
