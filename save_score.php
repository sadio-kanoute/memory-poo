<?php
// Endpoint pour sauvegarder un score envoyé depuis le client (fetch)
$cfg = require __DIR__ . '/config.php';
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
if(!$data){ http_response_code(400); echo json_encode(['error'=>'Invalid request']); exit; }
$pairs = (int)($data['pairs'] ?? 0);
$moves = (int)($data['moves'] ?? 0);
if($pairs < 3 || $pairs > 12 || $moves <= 0){ http_response_code(422); echo json_encode(['error'=>'Invalid data']); exit; }

// require logged in player
$player_id = $_SESSION['player_id'] ?? null;
if(!$player_id){ http_response_code(401); echo json_encode(['error'=>'Unauthorized - please login']); exit; }

// simple rate-limit: prevent spamming scores (2s)
if(isset($_SESSION['last_score_time']) && (time() - $_SESSION['last_score_time']) < 2){ http_response_code(429); echo json_encode(['error'=>'Too many requests']); exit; }

try{
    $cfg = require __DIR__ . '/config.php';
    $pdo = new PDO($cfg['dsn'], $cfg['db_user'], $cfg['db_pass'], $cfg['pdo_options']);

    // ensure player exists
    $stmt = $pdo->prepare('SELECT id FROM players WHERE id = :id');
    $stmt->execute([':id'=>$player_id]);
    $exists = $stmt->fetchColumn();
    if(!$exists){ http_response_code(400); echo json_encode(['error'=>'Player not found']); exit; }

    $score = $moves / max(1,$pairs);

    // insert within transaction
    $pdo->beginTransaction();
    $stmt = $pdo->prepare('INSERT INTO scores (player_id,pairs,moves,score) VALUES (:pid,:pairs,:moves,:score)');
    $stmt->execute([':pid'=>$player_id,':pairs'=>$pairs,':moves'=>$moves,':score'=>$score]);
    $pdo->commit();

    $_SESSION['last_score_time'] = time();
    echo json_encode(['ok'=>true,'score'=>$score]);
}catch(Exception $e){
    if(isset($pdo) && $pdo->inTransaction()){ $pdo->rollBack(); }
    http_response_code(500);
    echo json_encode(['error'=>$e->getMessage()]);
}
