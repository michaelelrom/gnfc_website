import os
import requests
from bs4 import BeautifulSoup
from urllib.parse import urljoin, urlparse

BASE_URL = "https://u33sf7gwbjecatsse6g7gssv.macaly.dev/"
URLS = ["", "temporary-fencing", "permanent-fencing", "gates-access", "about", "get-in-touch"]

PROJECT_DIR = "gnfc_website"
ASSETS_DIR = os.path.join(PROJECT_DIR, "assets")
CSS_DIR = os.path.join(PROJECT_DIR, "css")

os.makedirs(ASSETS_DIR, exist_ok=True)
os.makedirs(CSS_DIR, exist_ok=True)

def download_resource(url, folder):
    """Generic idempotent downloader for images and CSS."""
    filename = os.path.basename(urlparse(url).path)
    if not filename or "." not in filename: return None
    
    local_path = os.path.join(folder, filename)
    relative_path = f"{os.path.basename(folder)}/{filename}"

    if os.path.exists(local_path):
        return relative_path

    try:
        res = requests.get(url, timeout=10)
        if res.status_code == 200:
            with open(local_path, 'wb') as f:
                f.write(res.content)
            return relative_path
    except:
        pass
    return None

def build_pages():
    for path in URLS:
        full_url = urljoin(BASE_URL, path)
        local_filename = "index.html" if path == "" else f"{path}.html"
        print(f"Fixing CSS & Layout for: {local_filename}")
        
        try:
            response = requests.get(full_url)
            soup = BeautifulSoup(response.text, 'html.parser')

            # 1. Grab CSS
            for link in soup.find_all('link', rel='stylesheet'):
                css_url = urljoin(full_url, link.get('href'))
                local_css = download_resource(css_url, CSS_DIR)
                if local_css:
                    link['href'] = local_css

            # 2. Grab Images
            for img in soup.find_all('img'):
                src = img.get('src')
                if src:
                    local_img = download_resource(urljoin(full_url, src), ASSETS_DIR)
                    if local_img:
                        img['src'] = local_img

            # 3. Clean up navigation
            for a in soup.find_all('a', href=True):
                href = a['href']
                if href in ['/', BASE_URL]:
                    a['href'] = 'index.html'
                elif href.startswith('/') and not href.startswith('//'):
                    a['href'] = href.strip('/') + ".html"

            with open(os.path.join(PROJECT_DIR, local_filename), "w", encoding="utf-8") as f:
                f.write(soup.prettify())
        except Exception as e:
            print(f"Error: {e}")

if __name__ == "__main__":
    build_pages()
    print("\nCSS and Images should be local now! Refresh your browser.")