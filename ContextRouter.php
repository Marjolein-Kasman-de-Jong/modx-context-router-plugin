<?php
/**
 * ContextRouter (robust longest-prefix match)
 * ------------------------------------------
 * - Switches MODX contexts based on URL path prefixes.
 * - Prioritizes the longest prefix (e.g. /planets-fact-site/nl/ over /planets-fact-site/).
 * - Normalizes the 'q' parameter so Friendly URLs resolve correctly inside each context.
 *
 * Configuration: only edit the CONFIG block below.
 */

/* ======================= CONFIG ======================= */

// One or more hostnames allowed to use this router.
// Leave empty [] to disable host checking.
$domains = ['marjolein.dev']; // Example: ['example.com', 'www.example.com'] or []

// Base folder of your MODX installation (with or without slashes; normalized automatically).
$basePath = '/planets-fact-site/';

// Contexts and their subpaths relative to the base folder.
// NOTE: Use an empty string '' for the default context (usually 'web').
$contexts = [
    'web' => '',     // => /planets-fact-site/
    'nl'  => 'nl/',  // => /planets-fact-site/nl/
];

/* ==================== END CONFIG ====================== */

if ($modx->context && $modx->context->key === 'mgr') {
    return; // Skip the manager context
}

$host = $_SERVER['HTTP_HOST'] ?? '';
$uri  = strtok($_SERVER['REQUEST_URI'] ?? '', '?'); // Remove query string

// Optional host validation
if (!empty($domains) && !in_array($host, $domains, true)) {
    return; // Host not in whitelist; do nothing
}

// Normalize basePath to format '/xxx/' (or just '/')
$basePath = '/' . trim($basePath, '/');
if ($basePath !== '/') { 
    $basePath .= '/'; 
}

// Normalize subpaths in $contexts to '' or 'xxx/'
$normalized = [];
foreach ($contexts as $key => $sub) {
    $sub = trim(str_replace('\\', '/', (string)$sub), '/');
    $normalized[$key] = ($sub === '') ? '' : $sub . '/';
}
$contexts = $normalized;

// Build a prefix map for each context: '/base/' + 'sub/'
$prefixes = [];
foreach ($contexts as $key => $sub) {
    $prefixes[$key] = ($basePath === '/' ? '/' : $basePath) . $sub;
    // Special case: if basePath == '/' and sub == '', keep prefix '/'
    if ($prefixes[$key] === '') $prefixes[$key] = '/';
}

// If the URI is not within the basePath, exit early
if (strpos($uri, rtrim($basePath, '/')) !== 0) {
    return;
}

// Select the context with the LONGEST matching prefix
$selectedContext = null;
$selectedPrefix  = null;
$bestLen = -1;

foreach ($prefixes as $ctx => $pref) {
    if (strpos($uri, $pref) === 0) {
        $len = strlen($pref);
        if ($len > $bestLen) {
            $bestLen = $len;
            $selectedContext = $ctx;
            $selectedPrefix  = $pref;
        }
    }
}

// If no match is found, fall back to the first context (usually 'web')
if ($selectedContext === null) {
    $keys = array_keys($contexts);
    $selectedContext = $keys[0] ?? 'web';
    $selectedPrefix  = $prefixes[$selectedContext] ?? $basePath;
}

// Switch to the selected context
$modx->switchContext($selectedContext);

// Normalize 'q' relative to the selected prefix
$q = ltrim(substr($uri, strlen($selectedPrefix)), '/');
$_GET['q'] = $_REQUEST['q'] = $q;
