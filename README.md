# 🧭 ContextRouter Plugin for MODX

A reusable MODX plugin that automatically switches **contexts** based on the URL path.  
Ideal for multilingual or multi-site setups that share one MODX installation — even when hosted in a subfolder (e.g. `/planets-fact-site/`).

---

## ⚙️ Features

- Automatically detects the correct MODX context from the URL prefix  
- Works with **subfolders** (e.g. `/planets-fact-site/nl/`) or root installs (`/nl/`)  
- Supports multiple domains if needed  
- Uses **longest-prefix matching**, so `/nl/` always wins over `/`  
- Keeps Friendly URLs working by normalizing the `q` parameter  
- Easy to configure for new projects

---

## 🧩 Installation

1. In the MODX Manager, go to **Manage → Elements → Plugins → New Plugin**  
2. Name it **ContextRouter**  
3. Paste the plugin PHP code into the code field  
4. Enable the event **OnHandleRequest**  
5. Mark the plugin as **Enabled**  
6. Clear the MODX cache (**Manage → Clear Cache**)

---

## 🔧 Configuration

At the top of the plugin you’ll find a simple config block:

```php
// One or more allowed hostnames (optional)
$domains = ['marjolein.dev']; // or ['example.com','www.example.com'] or []

// The base folder of your MODX site (use '/' if installed in the webroot)
$basePath = '/planets-fact-site/';

// All contexts and their relative paths
$contexts = [
    'web' => '',     // default context → /planets-fact-site/
    'nl'  => 'nl/',  // Dutch site     → /planets-fact-site/nl/
    // 'en'  => 'en/', // example for English site
];
