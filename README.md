# Good Neighbor Fence Company Website

Static HTML/CSS website for Good Neighbor Fence Company (GNFC), a commercial fencing company based in Tampa Bay, FL.

## Project Structure

```
gnfc_website/          # Website files (upload to hosting)
├── index.html         # Homepage
├── temporary-fencing.html
├── permanent-fencing.html
├── gates-access.html
├── about.html
├── get-in-touch.html  # Contact form
├── send-quote.php     # PHP form handler (sends email)
├── css/style.css      # Stylesheet
└── assets/            # Images (logos, product photos, industry photos)

extract.py             # Python scraper script (used to pull assets from Macaly)
```

## Setup (Python Scraper)

The `extract.py` script was used to scrape images and content from the Macaly preview site. You only need this if re-scraping.

### 1. Create virtual environment

```bash
python3 -m venv .venv
```

### 2. Activate virtual environment

```bash
source .venv/bin/activate
```

### 3. Install dependencies

```bash
pip install requests beautifulsoup4
```

### 4. Run the scraper

```bash
python extract.py
```

### 5. Deactivate when done

```bash
deactivate
```

## Deployment (GoDaddy)

1. Upload the **contents** of `gnfc_website/` to your GoDaddy `public_html` directory
2. Edit `send-quote.php` line 6 — set `$to_email` to the email that should receive quote requests
3. The PHP `mail()` function works out of the box on GoDaddy shared hosting

## Form Handler

The contact form on `get-in-touch.html` submits to `send-quote.php`, which:
- Validates required fields
- Sends a formatted HTML email with the quote details
- Redirects back with `?status=success` or `?status=error`
- Sets Reply-To to the submitter's email

## Tech Stack

- **HTML5 / CSS3** — Static pages, no framework
- **Google Fonts** — Barlow + Barlow Condensed
- **PHP** — Form handler only (`send-quote.php`)
- **Python 3.9+** — Scraper only (`extract.py`)
  - `requests` — HTTP client
  - `beautifulsoup4` — HTML parser
