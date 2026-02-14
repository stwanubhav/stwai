import os
import asyncio
import httpx
from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import List
from dotenv import load_dotenv

# Load environment variables for local development
if os.getenv("VERCEL_ENV") is None:
    load_dotenv()

app = FastAPI()

# CORS configuration
ALLOWED_ORIGINS = ["*"]

app.add_middleware(
    CORSMiddleware,
    allow_origins=ALLOWED_ORIGINS,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

OPENROUTER_API_KEY = os.getenv("OPENROUTER_API_KEY")
OPENROUTER_URL = "https://openrouter.ai/api/v1/chat/completions"

class ChatRequest(BaseModel):
    prompt: str
    models: List[str]

async def fetch_ai_response(client, model: str, prompt: str):
    try:
        response = await client.post(
            OPENROUTER_URL,
            headers={
                "Authorization": f"Bearer {OPENROUTER_API_KEY}",
                "HTTP-Referer": "https://stwai.pages.dev",
                "Content-Type": "application/json",
            },
            json={
                "model": model,
                "messages": [{"role": "user", "content": prompt}]
            },
            timeout=25.0
        )
        
        if response.status_code != 200:
            return model, f"Error: API returned status {response.status_code}"
            
        data = response.json()
        
        if 'choices' in data and len(data['choices']) > 0:
            if 'message' in data['choices'][0] and 'content' in data['choices'][0]['message']:
                return model, data['choices'][0]['message']['content']
            else:
                return model, f"Error: Unexpected response format"
        else:
            return model, f"Error: No choices in response"
            
    except httpx.TimeoutException:
        return model, "Error: Request timed out"
    except Exception as e:
        return model, f"Error: {str(e)}"

@app.get("/api/health")
async def health_check():
    return {"status": "healthy"}

@app.get("/api/test")
async def test_endpoint():
    return {"message": "API is working!"}

@app.post("/api/chat")
async def chat(request: ChatRequest):
    if not OPENROUTER_API_KEY:
        raise HTTPException(status_code=500, detail="API Key not configured")
    
    if len(request.models) > 5:
        raise HTTPException(status_code=400, detail="Maximum 5 models allowed per request")
    
    try:
        async with httpx.AsyncClient(timeout=25.0) as client:
            tasks = [fetch_ai_response(client, model, request.prompt) for model in request.models]
            results = await asyncio.gather(*tasks)
        
        response_data = {model: text for model, text in results}
        return {"responses": response_data}
    
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Server error: {str(e)}")

# For local development
if __name__ == "__main__":
    import uvicorn
    port = int(os.getenv("PORT", 8000))
    uvicorn.run(app, host="0.0.0.0", port=port)
