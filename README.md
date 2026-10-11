# Tiktonomics

TikTok (and Bigo Live) creator battle-earnings calculator hub, with an SEO-driven blog, hosted at [tiktonomics.com](https://tiktonomics.com).

## Structure

- `index.html` — the core single-page app (Calculator, Battle Log, Co-host Split, Gift Values, Tax Estimator), with a Bigo Live platform toggle. Pulls translations from `lang/*.json` at runtime.
- `lang/` — `en.json`, `fr.json`, `es.json` translation files.
- `blog/` — all SEO articles as **flat `.html` files** (no folder/index.html routing — this is a deliberate, standardized convention; do not reintroduce trailing-slash folder URLs for new articles).
- `api/` — optional PHP/MySQL backend for the co-host directory and global leaderboard features (`cohosts.php`, `leaderboard.php`, `config.example.php`).
- `tiktonomics_db.sql` — MySQL schema for the `api/` backend (co-hosts + leaderboard tables), importable via phpMyAdmin.
- `sitemap.xml` — sitemap for all pages; every blog URL is a flat `.html` path matching the actual deployed files.
- `legacy/tiktok_battle_earnings_hub.html` — an earlier, superseded single-page prototype, kept for reference only. It is **not** linked from the live site and should not be deployed.

## Deploying the optional backend

1. Create a MySQL database on your host and import `tiktonomics_db.sql`.
2. Copy `api/config.example.php` to `api/config.php` and fill in your real `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`.
3. `api/config.php` is git-ignored — it holds real credentials and must never be committed.

## Conventions

- **Flat-file blog URLs only.** All blog articles are `blog/<slug>.html`. Canonicals, `og:url`, sitemap entries, and all internal links must use the flat path, never a trailing-slash folder path.
- **Accuracy first.** All earnings math (TikTok coin rates, platform cuts, Bigo Diamond/Bean conversion, tax figures) is sourced and verified; see in-page "verified" timestamps. When a figure is uncertain or sources conflict (e.g. Bigo's exact viewer-cost ratio), the site shows a defensible range rather than a single misleading number.
