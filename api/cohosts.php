<?php
// ============================================================
//  api/cohosts.php
//  GET  → returns all co-hosts (newest first, max 100)
//  POST → adds a new co-host listing
// ============================================================

require_once __DIR__ . '/config.php';
setCorsHeaders();

$method = $_SERVER['REQUEST_METHOD'];

// ── GET: list co-hosts ─────────────────────────────────────
if ($method === 'GET') {
    $niche = $_GET['niche'] ?? null;
    $tz    = $_GET['tz']    ?? null;

    $sql    = 'SELECT id, handle, followers, niche, timezone, availability, created_at FROM cohosts WHERE 1=1';
    $params = [];

    if ($niche) { $sql .= ' AND niche = ?';    $params[] = $niche; }
    if ($tz)    { $sql .= ' AND timezone = ?'; $params[] = $tz;    }

    $sql .= ' ORDER BY created_at DESC LIMIT 100';

    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);
    jsonResponse(['cohosts' => $stmt->fetchAll()]);
}

// ── POST: add a co-host ────────────────────────────────────
if ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);
    if (!$body) jsonError('Invalid JSON body.');

    $handle       = trim($body['handle']       ?? '');
    $followers    = (int) ($body['followers']  ?? 0);
    $niche        = trim($body['niche']        ?? 'General / Lifestyle');
    $timezone     = trim($body['timezone']     ?? 'EST (UTC-5)');
    $availability = trim($body['availability'] ?? 'Open');

    // Validation
    if (!$handle)             jsonError('handle is required.');
    if (strlen($handle) > 100) jsonError('handle too long.');
    if ($followers < 0)       jsonError('followers must be >= 0.');

    // Prefix @ if missing
    if ($handle[0] !== '@') $handle = '@' . $handle;

    // Rate limit: max 3 listings per handle
    $count = getDB()->prepare('SELECT COUNT(*) FROM cohosts WHERE handle = ?');
    $count->execute([$handle]);
    if ($count->fetchColumn() >= 3) jsonError('Max 3 listings per handle.', 429);

    $stmt = getDB()->prepare(
        'INSERT INTO cohosts (handle, followers, niche, timezone, availability) VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([$handle, $followers, $niche, $timezone, $availability]);

    jsonResponse(['success' => true, 'id' => (int) getDB()->lastInsertId()], 201);
}

jsonError('Method not allowed.', 405);
