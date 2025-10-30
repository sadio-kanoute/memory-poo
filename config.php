<?php
// Configuration flexible pour environnements locaux (WAMP) et Plesk.
// Priorité : variables d'environnement > détection APP_ENV > valeurs par défaut.

/**
 * Récupère une variable d'environnement ou $_SERVER, sinon retourne la valeur par défaut.
 */
function env_var(string $name, $default = null)
{
    $v = getenv($name);
    if ($v !== false) return $v;
    if (isset($_SERVER[$name])) return $_SERVER[$name];
    return $default;
}

// Détermine l'environnement : 'local' (par défaut) ou 'plesk' si APP_ENV=plesk
$appEnv = strtolower((string) env_var('APP_ENV', 'local'));

// Valeurs par défaut pour chaque environnement
$defaults = [
    'local' => [
        'db_host' => '127.0.0.1',
        'db_name' => 'memory',
        'db_user' => 'root',
        'db_pass' => '',
        'db_charset' => 'utf8mb4',
    ],
    'plesk' => [
        // Remplacez ces valeurs par celles fournies par Plesk (ou définissez les variables d'environnement)
        'db_host' => env_var('DB_HOST', 'localhost'),
        // par défaut laisser vide ici — fournir via config.local.php ou variables d'environnement
        'db_name' => env_var('DB_NAME', ''),
        'db_user' => env_var('DB_USER', ''),
        'db_pass' => env_var('DB_PASS', ''),
        'db_charset' => env_var('DB_CHARSET', 'utf8mb4'),
    ],
];

// Choisit la configuration initiale selon l'environnement
$cfg = $defaults[$appEnv] ?? $defaults['local'];

// Si un fichier local existe (non versionné), il peut fournir les credentials
$localFile = __DIR__ . '/config.local.php';
if (file_exists($localFile)) {
    $localCfg = include $localFile;
    if (is_array($localCfg)) {
        $cfg = array_merge($cfg, $localCfg);
    }
}

// Autorise l'override via variables d'environnement DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_CHARSET
$cfg['db_host'] = env_var('DB_HOST', $cfg['db_host']);
$cfg['db_name'] = env_var('DB_NAME', $cfg['db_name']);
$cfg['db_user'] = env_var('DB_USER', $cfg['db_user']);
$cfg['db_pass'] = env_var('DB_PASS', $cfg['db_pass']);
$cfg['db_charset'] = env_var('DB_CHARSET', $cfg['db_charset']);

// Options PDO par défaut recommandées
$cfg['pdo_options'] = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

// Fournit aussi un DSN pratique
$cfg['dsn'] = sprintf('mysql:host=%s;dbname=%s;charset=%s', $cfg['db_host'], $cfg['db_name'], $cfg['db_charset']);

return $cfg;
