"""
Generate 4K lifestyle product mock-ups for NexGizmo
---------------------------------------------------
Before running:
    pip install requests
    export OPENAI_API_KEY="your_api_key_here"   # or set as system variable
Then:
    python generate_products.py
"""

import os, requests

# --- configuration ---
api_key = os.getenv("OPENAI_API_KEY")  # put your key here if not using env variable
save_dir = os.path.dirname(__file__)

products = [
    "smartphone on a dark neon tech-store desk",
    "smartwatch on a glowing table beside a laptop",
    "wireless earbuds on a glass surface with reflections",
    "gaming laptop half open with RGB lights",
    "portable bluetooth speaker under purple lighting",
    "VR headset on black background with cyan glow",
    "wireless keyboard and mouse on neon desk",
    "smart TV mounted in dark room with reflections",
    "tablet on modern workspace with colored backlight",
    "action camera on desk with lens flare",
    "powerbank beside phone under blue light",
    "portable projector in dim room with light beam",
    "wireless charger pad with glowing edges",
    "studio microphone with soft rim light",
    "camera lens on black table with reflection",
    "fitness band on wrist with red backlight",
    "drone hovering indoors with neon reflection",
    "headphones on metallic stand with purple hue",
    "smart home hub on table with ambient light",
    "game controller on reflective dark surface"
]

# --- generation function (OpenAI DALL·E example) ---
def generate_image(prompt, filename):
    url = "https://api.openai.com/v1/images/generations"
    headers = {"Authorization": f"Bearer {api_key}"}
    data = {
        "model": "gpt-image-1",
        "prompt": f"photo-realistic lifestyle product photo, 4K resolution, {prompt}",
        "size": "1024x1024"   # 2048x2048 works on paid tiers; use 1024 to start
    }

    print(f"Generating: {filename}")
    r = requests.post(url, headers=headers, json=data)
    r.raise_for_status()
    img_url = r.json()["data"][0]["url"]

    img_bytes = requests.get(img_url).content
    with open(os.path.join(save_dir, filename), "wb") as f:
        f.write(img_bytes)
    print(" → saved", filename)


for i, prompt in enumerate(products, start=1):
    generate_image(prompt, f"product_{i:02d}_dark.png")

print("\nAll images saved in", save_dir)
