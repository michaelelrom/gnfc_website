# Good Neighbor Fence Company Website

Static HTML/CSS website for Good Neighbor Fence Company (GNFC), a commercial fencing company based in Tampa Bay, FL.

## Project Structure

```
john/
├── .gitignore
├── README.md              # This file
├── extract.py             # Python scraper (pulls assets from Macaly preview)
└── docs/                  # Website files → upload to GoDaddy public_html
    ├── index.html         # Homepage
    ├── temporary-fencing.html
    ├── permanent-fencing.html
    ├── gates-access.html
    ├── about.html
    ├── get-in-touch.html  # Contact form
    ├── send-quote.php     # PHP form handler (sends email or saves locally)
    ├── css/
    │   └── style.css
    └── assets/            # Images (logos, product photos, industry photos)
```

## Local Testing

### Run the site locally with PHP

```bash
php -S localhost:8000 -t docs/
```

Then open [http://localhost:8000](http://localhost:8000) in your browser.

The form will work — since there's no mail server locally, submissions are saved to `docs/submissions.json` instead of emailing. On GoDaddy, it will send actual emails.

## Setup (Python Scraper)

The `extract.py` script was used to scrape images from the Macaly preview site. Only needed if re-scraping.

```bash
python3 -m venv .venv
source .venv/bin/activate
pip install requests beautifulsoup4
python extract.py
deactivate
```

## Deployment (GoDaddy)

1. Upload the **contents** of `docs/` to your GoDaddy `public_html` directory
2. Edit `send-quote.php` line 6 — set `$to_email` to the email that should receive quote requests
3. The PHP `mail()` function works out of the box on GoDaddy shared hosting
4. Delete `submissions.json` if it exists (local testing artifact)

## Form Handler

The contact form on `get-in-touch.html` submits to `send-quote.php`, which:
- Validates required fields
- Sends a formatted HTML email with the quote details
- Falls back to saving `submissions.json` if mail server unavailable (local dev)
- Redirects back with `?status=success` or `?status=error`
- Sets Reply-To to the submitter's email

## Tech Stack

- **HTML5 / CSS3** — Static pages, no framework
- **Google Fonts** — Barlow + Barlow Condensed
- **PHP** — Form handler only (`send-quote.php`)
- **Python 3.9+** — Scraper only (`extract.py`)
  - `requests` — HTTP client
  - `beautifulsoup4` — HTML parser
