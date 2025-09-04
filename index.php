<?php // public/notesmitter/index.php ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Notes" />
  <title>Notes</title>

  <!-- Favicons -->
  <link rel="icon" type="image/png" href="assets/images/favicon/favicon-96x96.png" sizes="96x96" />
  <link rel="icon" type="image/svg+xml" href="assets/images/favicon/favicon.svg" />
  <link rel="shortcut icon" href="assets/images/favicon/favicon.ico" />
  <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicon/apple-touch-icon-180.png" />
  <link rel="manifest" href="assets/notesmitter.webmanifest">

  <link rel="apple-touch-startup-image" href="assets/images/bg.png"
        media="(device-width: 320px) and (device-height: 568px)">
  <link rel="stylesheet" href="assets/css/notesmitter.css">
</head>
<body class="notes-app">

  <!-- Top-left (reset) -->
  <div class="corner left">
    <a id="homeReset" href="index.php" aria-label="Home">
      <img id="imgLeft" src="assets/images/top-left.png" alt="">
    </a>
  </div>

  <!-- Top-right (dark toggle) -->
  <div class="corner right">
    <img id="imgRight" src="assets/images/top-right.png" alt="" role="button" aria-pressed="false">
  </div>

  <!-- Editor -->
  <div class="editor-wrap">
    <div id="noteBody" class="note-body" contenteditable="true" data-placeholder="Note"></div>
  </div>

  <script>
    const noteDiv = document.getElementById('noteBody');
    const homeResetLink = document.getElementById('homeReset');
    const imgLeft = document.getElementById('imgLeft');
    const imgRight = document.getElementById('imgRight');

    // --- Config ---
    const WAIT = 1500; // ms (change as you like)
    const THEME_KEY   = 'notesmitter:theme';   // 'dark' | 'light'
    const PENDING_KEY = 'notesmitter:pending'; // queued text when offline

    // --- State ---
    let timer = null;
    let isDark = false;
    let lastSaved = ''; // (3) client-side skip if unchanged

    // --- Helpers ---
    function clearEditor() {
      clearTimeout(timer);
      // ensure truly empty so :empty placeholder shows
      noteDiv.innerHTML = '';
      noteDiv.textContent = '';
    }

    // (8) Save with offline queue + (3) skip if unchanged
    async function save(text) {
      if (text === lastSaved) return; // (3) no-op if unchanged

      if (!navigator.onLine) {
        // queue the latest text and bail
        localStorage.setItem(PENDING_KEY, text);
        return;
      }

      try {
        const res = await fetch('save.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ text })
        });
        // If server accepted (ok or skipped), update lastSaved
        if (res.ok) {
          lastSaved = text;
          // clear any queued value if it matches what we just saved
          const queued = localStorage.getItem(PENDING_KEY);
          if (queued === text) localStorage.removeItem(PENDING_KEY);
        }
      } catch {
        // stay silent; user shouldn't see errors
      }
    }

    function scheduleSave() {
      clearTimeout(timer);
      timer = setTimeout(() => {
        const text = noteDiv.innerText; // or innerHTML if you ever keep formatting
        save(text);
      }, WAIT);
    }

    // (7) Immediate save on Enter (also prevents newline)
    // noteDiv.addEventListener('keydown', (e) => {
    //   if (e.key === 'Enter') {
    //     e.preventDefault();
    //     clearTimeout(timer);
    //     const text = noteDiv.innerText.trim();
    //     save(text);
    //   }
    // });

    // Debounced save after inactivity
    noteDiv.addEventListener('input', scheduleSave);

    // Safety save on blur if not empty
    noteDiv.addEventListener('blur', () => {
      clearTimeout(timer);
      const text = noteDiv.innerText.trim();
      if (text !== '') save(text);
    });

    // Reset on load so refresh starts blank (server reset happens in background)
    clearEditor();
    (async () => { try { await fetch('reset.php', { method: 'POST' }); } catch {} })();

    // Clicking the top-left image: reset then go home
    if (homeResetLink) {
      homeResetLink.addEventListener('click', async (e) => {
        e.preventDefault();
        clearEditor();
        try { await fetch('reset.php', { method: 'POST' }); } catch {}
        window.location.href = homeResetLink.getAttribute('href');
      });
    }

    // Flush any queued save when we come online
    window.addEventListener('online', () => {
      const queued = localStorage.getItem(PENDING_KEY);
      if (queued != null) save(queued);
    });

    // --- Dark-mode toggle (with persistence) ---
    function applyTheme(dark) {
      document.body.classList.toggle('dark', dark);
      imgLeft.src  = dark ? 'assets/images/top-left-dark.png'  : 'assets/images/top-left.png';
      imgRight.src = dark ? 'assets/images/top-right-dark.png' : 'assets/images/top-right.png';
      imgRight.setAttribute('aria-pressed', String(dark));
      isDark = dark;
    }

    // initial theme: saved → OS preference fallback
    (() => {
      const saved = localStorage.getItem(THEME_KEY);
      const startDark = saved
        ? saved === 'dark'
        : (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
      applyTheme(startDark);
    })();

    imgRight.addEventListener('click', () => {
      const next = !isDark;
      applyTheme(next);
      localStorage.setItem(THEME_KEY, next ? 'dark' : 'light');
    });
  </script>
</body>
</html>
