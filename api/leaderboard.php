<?php
// ============================================================
//  api/leaderboard.php
//  GET  → returns top 50 leaderboard entries this week
//  POST → submits a battle result to the global leaderboard
// ============================================================

require_once __DIR__ . '/config.php';
setCorsHeaders();

$method = $_SERVER['REQUEST_METHOD'];

// ── GET: fetch leaderboard ─────────────────────────────────
if ($method === 'GET') {
    $period = $_GET['period'] ?? 'week'; // week | alltime

    if ($period === 'alltime') {
        $sql = 'SELECT handle, coins, earned, opponent, result, battle_date
                FROM leaderboard
                ORDER BY earned DESC LIMIT 50';
        $params = [];
    } else {
        // Default: current week
        $sql = 'SELECT handle, coins, earned, opponent, result, battle_date
                FROM leaderboard
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                ORDER BY earned DESC LIMIT 50';
        $params = [];
    }

    $stmt = getDB()->prepare($sql);
    $stmt->execute($params);
    jsonResponse(['entries' => $stmt->fetchAll(), 'period' => $period]);
}

// ── POST: submit to leaderboard ────────────────────────────
if ($method === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);
    if (!$body) jsonError('Invalid JSON body.');

    $handle      = trim($body['handle']      ?? 'Anonymous');
    $coins       = (int)   ($body['coins']   ?? 0);
    $earned      = (float) ($body['earned']  ?? 0);
    $opponent    = trim($body['opponent']    ?? 'Unknown');
    $result      = $body['result'] === 'loss' ? 'loss' : 'win';
    $battle_date = $body['date']   ?? date('Y-m-d');

    // Validation
    if ($coins  <= 0)       jsonError('coins must be positive.');
    if ($earned <= 0)       jsonError('earned must be positive.');
    if ($earned > 10000)    jsonError('earned value out of range.');
    if (strlen($handle) > 100) jsonError('handle too long.');

    // Sanitise date
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $battle_date)) {
        $battle_date = date('Y-m-d');
    }

    // Prefix @ if missing
    if ($handle !== 'Anonymous' && $handle[0] !== '@') $handle = '@' . $handle;

    // Rate limit: max 10 submissions per handle per day
    $check = getDB()->prepare(
        'SELECT COUNT(*) FROM leaderboard WHERE handle = ? AND DATE(created_at) = CURDATE()'
    );
    $check->execute([$handle]);
    if ($check->fetchColumn() >= 10) jsonError('Daily submission limit reached.', 429);

    $stmt = getDB()->prepare(
        'INSERT INTO leaderboard (handle, coins, earned, opponent, result, battle_date) VALUES (?,?,?,?,?,?)'
    );
    $stmt->execute([$handle, $coins, $earned, $opponent, $result, $battle_date]);

    jsonResponse(['success' => true, 'id' => (int) getDB()->lastInsertId()], 201);
}

jsonError('Method not allowed.', 405);
