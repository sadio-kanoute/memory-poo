<?php
// Endpoint pour sauvegarder un score envoyé depuis le client (fetch)
session_start();
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
if(!$data){ http_response_code(400); echo json_encode(['error'=>'Invalid request']); exit; }
$username = trim($_SESSION['username'] ?? ($data['username'] ?? ''));
$pairs = (int)($data['pairs'] ?? 0);
$moves = (int)($data['moves'] ?? 0);
if($username === '' || $pairs < 3 || $pairs > 12 || $moves <= 0){ http_response_code(422); echo json_encode(['error'=>'Invalid data']); exit; }

try{
    $cfg = require __DIR__ . '/config.php';
    $dsn = "mysql:host={$cfg['db_host']};dbname={$cfg['db_name']};charset={$cfg['db_charset']}";
    $pdo = new PDO($dsn, $cfg['db_user'], $cfg['db_pass'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

    // trouver ou créer le joueur
    $stmt = $pdo->prepare('SELECT id FROM players WHERE username = :u');
    $stmt->execute([':u'=>$username]);
    $player = $stmt->fetchColumn();
    if(!$player){
        $stmt = $pdo->prepare('INSERT INTO players (username) VALUES (:u)');
        $stmt->execute([':u'=>$username]);
        $player = $pdo->lastInsertId();
    }

    $score = $moves / max(1,$pairs);
    $stmt = $pdo->prepare('INSERT INTO scores (player_id,pairs,moves,score) VALUES (:pid,:pairs,:moves,:score)');
    $stmt->execute([':pid'=>$player,':pairs'=>$pairs,':moves'=>$moves,':score'=>$score]);

    echo json_encode(['ok'=>true,'score'=>$score]);
}catch(Exception $e){
    http_response_code(500);
    echo json_encode(['error'=>$e->getMessage()]);
}
