<?php

$base_url = "http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$currentPage = basename($_SERVER['PHP_SELF'], '.php');


require_once __DIR__ . '/config.php';

$pages = []; 
try {
    $stmt = $pdo->query("SELECT id, page_name, section_name, content, created_at, updated_at FROM pages ORDER BY page_name, id");
    $rows = $stmt->fetchAll();

    foreach ($rows as $row) {
        $pname = $row['page_name'];
        $sname = $row['section_name'];

        $decoded = json_decode($row['content'], true);

        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            $decoded = ['raw' => $row['content']];
        }

        $decoded['_meta'] = [
            'id' => $row['id'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ];

        if (!isset($pages[$pname])) {
            $pages[$pname] = [];
        }

        $pages[$pname][$sname] = $decoded;
    }
} catch (\PDOException $e) {
    error_log("Error fetching pages table: " . $e->getMessage());
    $pages = [];
}

/**
 * Helper: get page data or section data
 * Usage:
 *  pageData('home')             => returns array of sections for 'home' or [] if none
 *  pageData('home','hero')      => returns array of hero content or null if not exists
 */
function pageData(string $pageName, string $sectionName = null) {
    global $pages;
    if ($sectionName === null) {
        return $pages[$pageName] ?? [];
    }
    return $pages[$pageName][$sectionName] ?? null;
}

$settings = [];
try {
    $stmt = $pdo->query("SELECT `key`, `value`, created_at, updated_at FROM settings ORDER BY `key`");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        $settings[$row['key']] = [
            'value' => $row['value'],
            '_meta' => [
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'],
            ],
        ];
    }
} catch (\PDOException $e) {
    error_log("Error fetching settings table: " . $e->getMessage());
    $settings = [];
}

/**
 * Helper: get setting value by key
 * Usage:
 *   setting('address')          => returns the value of 'address' or null if not exists
 *   setting('facebook', true)   => returns array with value and meta if second param true
 */
function setting(string $key, bool $withMeta = false) {
    global $settings;
    if (!isset($settings[$key])) return null;

    return $withMeta ? $settings[$key] : $settings[$key]['value'];
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, minimum-scale=1"
    />
      <title><?= htmlspecialchars((setting('tab_title') ?? 'Dialer') . ' - ' . ucfirst($currentPage)) ?></title>
    <meta
      name="robots"
      content="index, max-snippet:-1, max-image-preview:large, max-video-preview:-1, follow"
    />
    <style>
      img:is([sizes="auto" i], [sizes^="auto," i]) {
        contain-intrinsic-size: 3000px 1500px;
      }
    </style>
    <meta name="description" content="Dialer" />

    <meta property="og:url" content="" />
    <meta
      property="og:site_name"
      content="Dialer"
    />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Home" />
    <meta
      property="og:description"
      content="Dialer"
    />
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:locale" content="en_US" />
    <meta name="twitter:type" content="website" />
    <meta name="twitter:title" content="Home" />
    <meta
      name="twitter:description"
      content="Dialer"
    />
    <meta name="twitter:url" content="" />
    <meta
      name="twitter:site"
      content=" Dialer"
    />
    <link
      rel="alternate"
      type="application/rss+xml"
      title=" Dialer &raquo; Feed"
      href="/feed/"
    />
    <link
      rel="alternate"
      type="application/rss+xml"
      title="Dialer &raquo; Comments Feed"
      href=""
    />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons/css/flag-icons.min.css">

    <script>
      document.documentElement.classList.remove("no-js");
    </script>
    <script>
      window._wpemojiSettings = {
        baseUrl: "https:\/\/s.w.org\/images\/core\/emoji\/16.0.1\/72x72\/",
        ext: ".png",
        svgUrl: "https:\/\/s.w.org\/images\/core\/emoji\/16.0.1\/svg\/",
        svgExt: ".svg",
        source: {
          concatemoji:
            "https:\/\/asiantelecombd.com\/wp-includes\/js\/wp-emoji-release.min.js?ver=6.8.3",
        },
      };
      /*! This file is auto-generated */
      !(function (s, n) {
        var o, i, e;
        function c(e) {
          try {
            var t = { supportTests: e, timestamp: new Date().valueOf() };
            sessionStorage.setItem(o, JSON.stringify(t));
          } catch (e) {}
        }
        function p(e, t, n) {
          e.clearRect(0, 0, e.canvas.width, e.canvas.height),
            e.fillText(t, 0, 0);
          var t = new Uint32Array(
              e.getImageData(0, 0, e.canvas.width, e.canvas.height).data
            ),
            a =
              (e.clearRect(0, 0, e.canvas.width, e.canvas.height),
              e.fillText(n, 0, 0),
              new Uint32Array(
                e.getImageData(0, 0, e.canvas.width, e.canvas.height).data
              ));
          return t.every(function (e, t) {
            return e === a[t];
          });
        }
        function u(e, t) {
          e.clearRect(0, 0, e.canvas.width, e.canvas.height),
            e.fillText(t, 0, 0);
          for (
            var n = e.getImageData(16, 16, 1, 1), a = 0;
            a < n.data.length;
            a++
          )
            if (0 !== n.data[a]) return !1;
          return !0;
        }
        function f(e, t, n, a) {
          switch (t) {
            case "flag":
              return n(
                e,
                "\ud83c\udff3\ufe0f\u200d\u26a7\ufe0f",
                "\ud83c\udff3\ufe0f\u200b\u26a7\ufe0f"
              )
                ? !1
                : !n(
                    e,
                    "\ud83c\udde8\ud83c\uddf6",
                    "\ud83c\udde8\u200b\ud83c\uddf6"
                  ) &&
                    !n(
                      e,
                      "\ud83c\udff4\udb40\udc67\udb40\udc62\udb40\udc65\udb40\udc6e\udb40\udc67\udb40\udc7f",
                      "\ud83c\udff4\u200b\udb40\udc67\u200b\udb40\udc62\u200b\udb40\udc65\u200b\udb40\udc6e\u200b\udb40\udc67\u200b\udb40\udc7f"
                    );
            case "emoji":
              return !a(e, "\ud83e\udedf");
          }
          return !1;
        }
        function g(e, t, n, a) {
          var r =
              "undefined" != typeof WorkerGlobalScope &&
              self instanceof WorkerGlobalScope
                ? new OffscreenCanvas(300, 150)
                : s.createElement("canvas"),
            o = r.getContext("2d", { willReadFrequently: !0 }),
            i = ((o.textBaseline = "top"), (o.font = "600 32px Arial"), {});
          return (
            e.forEach(function (e) {
              i[e] = t(o, e, n, a);
            }),
            i
          );
        }
        function t(e) {
          var t = s.createElement("script");
          (t.src = e), (t.defer = !0), s.head.appendChild(t);
        }
        "undefined" != typeof Promise &&
          ((o = "wpEmojiSettingsSupports"),
          (i = ["flag", "emoji"]),
          (n.supports = { everything: !0, everythingExceptFlag: !0 }),
          (e = new Promise(function (e) {
            s.addEventListener("DOMContentLoaded", e, { once: !0 });
          })),
          new Promise(function (t) {
            var n = (function () {
              try {
                var e = JSON.parse(sessionStorage.getItem(o));
                if (
                  "object" == typeof e &&
                  "number" == typeof e.timestamp &&
                  new Date().valueOf() < e.timestamp + 604800 &&
                  "object" == typeof e.supportTests
                )
                  return e.supportTests;
              } catch (e) {}
              return null;
            })();
            if (!n) {
              if (
                "undefined" != typeof Worker &&
                "undefined" != typeof OffscreenCanvas &&
                "undefined" != typeof URL &&
                URL.createObjectURL &&
                "undefined" != typeof Blob
              )
                try {
                  var e =
                      "postMessage(" +
                      g.toString() +
                      "(" +
                      [
                        JSON.stringify(i),
                        f.toString(),
                        p.toString(),
                        u.toString(),
                      ].join(",") +
                      "));",
                    a = new Blob([e], { type: "text/javascript" }),
                    r = new Worker(URL.createObjectURL(a), {
                      name: "wpTestEmojiSupports",
                    });
                  return void (r.onmessage = function (e) {
                    c((n = e.data)), r.terminate(), t(n);
                  });
                } catch (e) {}
              c((n = g(i, f, p, u)));
            }
            t(n);
          })
            .then(function (e) {
              for (var t in e)
                (n.supports[t] = e[t]),
                  (n.supports.everything =
                    n.supports.everything && n.supports[t]),
                  "flag" !== t &&
                    (n.supports.everythingExceptFlag =
                      n.supports.everythingExceptFlag && n.supports[t]);
              (n.supports.everythingExceptFlag =
                n.supports.everythingExceptFlag && !n.supports.flag),
                (n.DOMReady = !1),
                (n.readyCallback = function () {
                  n.DOMReady = !0;
                });
            })
            .then(function () {
              return e;
            })
            .then(function () {
              var e;
              n.supports.everything ||
                (n.readyCallback(),
                (e = n.source || {}).concatemoji
                  ? t(e.concatemoji)
                  : e.wpemoji && e.twemoji && (t(e.twemoji), t(e.wpemoji)));
            }));
      })((window, document), window._wpemojiSettings);
    </script>

    <link
      rel="stylesheet"
      id="ht_ctc_main_css-css"
      href="assets/css/main.css?ver=4.18"
      media="all"
    />
    <link
      rel="stylesheet"
      id="hfe-widgets-style-css"
      href="assets/css/frontend.css?ver=2.2.0"
      media="all"
    />
    <style id="wp-emoji-styles-inline-css">
      img.wp-smiley,
      img.emoji {
        display: inline !important;
        border: none !important;
        box-shadow: none !important;
        height: 1em !important;
        width: 1em !important;
        margin: 0 0.07em !important;
        vertical-align: -0.1em !important;
        background: none !important;
        padding: 0 !important;
      }
    </style>
    <style id="classic-theme-styles-inline-css">
      /*! This file is auto-generated */
      .wp-block-button__link {
        color: #fff;
        background-color: #32373c;
        border-radius: 9999px;
        box-shadow: none;
        text-decoration: none;
        padding: calc(0.667em + 2px) calc(1.333em + 2px);
        font-size: 1.125em;
      }
      .wp-block-file__button {
        background: #32373c;
        color: #fff;
        text-decoration: none;
      }
    </style>
    <style id="global-styles-inline-css">
      :root {
        --wp--preset--aspect-ratio--square: 1;
        --wp--preset--aspect-ratio--4-3: 4/3;
        --wp--preset--aspect-ratio--3-4: 3/4;
        --wp--preset--aspect-ratio--3-2: 3/2;
        --wp--preset--aspect-ratio--2-3: 2/3;
        --wp--preset--aspect-ratio--16-9: 16/9;
        --wp--preset--aspect-ratio--9-16: 9/16;
        --wp--preset--color--black: #000000;
        --wp--preset--color--cyan-bluish-gray: #abb8c3;
        --wp--preset--color--white: #ffffff;
        --wp--preset--color--pale-pink: #f78da7;
        --wp--preset--color--vivid-red: #cf2e2e;
        --wp--preset--color--luminous-vivid-orange: #ff6900;
        --wp--preset--color--luminous-vivid-amber: #fcb900;
        --wp--preset--color--light-green-cyan: #7bdcb5;
        --wp--preset--color--vivid-green-cyan: #00d084;
        --wp--preset--color--pale-cyan-blue: #8ed1fc;
        --wp--preset--color--vivid-cyan-blue: #0693e3;
        --wp--preset--color--vivid-purple: #9b51e0;
        --wp--preset--color--theme-palette-1: var(--global-palette1);
        --wp--preset--color--theme-palette-2: var(--global-palette2);
        --wp--preset--color--theme-palette-3: var(--global-palette3);
        --wp--preset--color--theme-palette-4: var(--global-palette4);
        --wp--preset--color--theme-palette-5: var(--global-palette5);
        --wp--preset--color--theme-palette-6: var(--global-palette6);
        --wp--preset--color--theme-palette-7: var(--global-palette7);
        --wp--preset--color--theme-palette-8: var(--global-palette8);
        --wp--preset--color--theme-palette-9: var(--global-palette9);
        --wp--preset--gradient--vivid-cyan-blue-to-vivid-purple: linear-gradient(
          135deg,
          rgba(6, 147, 227, 1) 0%,
          rgb(155, 81, 224) 100%
        );
        --wp--preset--gradient--light-green-cyan-to-vivid-green-cyan: linear-gradient(
          135deg,
          rgb(122, 220, 180) 0%,
          rgb(0, 208, 130) 100%
        );
        --wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange: linear-gradient(
          135deg,
          rgba(252, 185, 0, 1) 0%,
          rgba(255, 105, 0, 1) 100%
        );
        --wp--preset--gradient--luminous-vivid-orange-to-vivid-red: linear-gradient(
          135deg,
          rgba(255, 105, 0, 1) 0%,
          rgb(207, 46, 46) 100%
        );
        --wp--preset--gradient--very-light-gray-to-cyan-bluish-gray: linear-gradient(
          135deg,
          rgb(238, 238, 238) 0%,
          rgb(169, 184, 195) 100%
        );
        --wp--preset--gradient--cool-to-warm-spectrum: linear-gradient(
          135deg,
          rgb(74, 234, 220) 0%,
          rgb(151, 120, 209) 20%,
          rgb(207, 42, 186) 40%,
          rgb(238, 44, 130) 60%,
          rgb(251, 105, 98) 80%,
          rgb(254, 248, 76) 100%
        );
        --wp--preset--gradient--blush-light-purple: linear-gradient(
          135deg,
          rgb(255, 206, 236) 0%,
          rgb(152, 150, 240) 100%
        );
        --wp--preset--gradient--blush-bordeaux: linear-gradient(
          135deg,
          rgb(254, 205, 165) 0%,
          rgb(254, 45, 45) 50%,
          rgb(107, 0, 62) 100%
        );
        --wp--preset--gradient--luminous-dusk: linear-gradient(
          135deg,
          rgb(255, 203, 112) 0%,
          rgb(199, 81, 192) 50%,
          rgb(65, 88, 208) 100%
        );
        --wp--preset--gradient--pale-ocean: linear-gradient(
          135deg,
          rgb(255, 245, 203) 0%,
          rgb(182, 227, 212) 50%,
          rgb(51, 167, 181) 100%
        );
        --wp--preset--gradient--electric-grass: linear-gradient(
          135deg,
          rgb(202, 248, 128) 0%,
          rgb(113, 206, 126) 100%
        );
        --wp--preset--gradient--midnight: linear-gradient(
          135deg,
          rgb(2, 3, 129) 0%,
          rgb(40, 116, 252) 100%
        );
        --wp--preset--font-size--small: var(--global-font-size-small);
        --wp--preset--font-size--medium: var(--global-font-size-medium);
        --wp--preset--font-size--large: var(--global-font-size-large);
        --wp--preset--font-size--x-large: 42px;
        --wp--preset--font-size--larger: var(--global-font-size-larger);
        --wp--preset--font-size--xxlarge: var(--global-font-size-xxlarge);
        --wp--preset--spacing--20: 0.44rem;
        --wp--preset--spacing--30: 0.67rem;
        --wp--preset--spacing--40: 1rem;
        --wp--preset--spacing--50: 1.5rem;
        --wp--preset--spacing--60: 2.25rem;
        --wp--preset--spacing--70: 3.38rem;
        --wp--preset--spacing--80: 5.06rem;
        --wp--preset--shadow--natural: 6px 6px 9px rgba(0, 0, 0, 0.2);
        --wp--preset--shadow--deep: 12px 12px 50px rgba(0, 0, 0, 0.4);
        --wp--preset--shadow--sharp: 6px 6px 0px rgba(0, 0, 0, 0.2);
        --wp--preset--shadow--outlined: 6px 6px 0px -3px rgba(255, 255, 255, 1),
          6px 6px rgba(0, 0, 0, 1);
        --wp--preset--shadow--crisp: 6px 6px 0px rgba(0, 0, 0, 1);
      }
      :where(.is-layout-flex) {
        gap: 0.5em;
      }
      :where(.is-layout-grid) {
        gap: 0.5em;
      }
      body .is-layout-flex {
        display: flex;
      }
      .is-layout-flex {
        flex-wrap: wrap;
        align-items: center;
      }
      .is-layout-flex > :is(*, div) {
        margin: 0;
      }
      body .is-layout-grid {
        display: grid;
      }
      .is-layout-grid > :is(*, div) {
        margin: 0;
      }
      :where(.wp-block-columns.is-layout-flex) {
        gap: 2em;
      }
      :where(.wp-block-columns.is-layout-grid) {
        gap: 2em;
      }
      :where(.wp-block-post-template.is-layout-flex) {
        gap: 1.25em;
      }
      :where(.wp-block-post-template.is-layout-grid) {
        gap: 1.25em;
      }
      .has-black-color {
        color: var(--wp--preset--color--black) !important;
      }
      .has-cyan-bluish-gray-color {
        color: var(--wp--preset--color--cyan-bluish-gray) !important;
      }
      .has-white-color {
        color: var(--wp--preset--color--white) !important;
      }
      .has-pale-pink-color {
        color: var(--wp--preset--color--pale-pink) !important;
      }
      .has-vivid-red-color {
        color: var(--wp--preset--color--vivid-red) !important;
      }
      .has-luminous-vivid-orange-color {
        color: var(--wp--preset--color--luminous-vivid-orange) !important;
      }
      .has-luminous-vivid-amber-color {
        color: var(--wp--preset--color--luminous-vivid-amber) !important;
      }
      .has-light-green-cyan-color {
        color: var(--wp--preset--color--light-green-cyan) !important;
      }
      .has-vivid-green-cyan-color {
        color: var(--wp--preset--color--vivid-green-cyan) !important;
      }
      .has-pale-cyan-blue-color {
        color: var(--wp--preset--color--pale-cyan-blue) !important;
      }
      .has-vivid-cyan-blue-color {
        color: var(--wp--preset--color--vivid-cyan-blue) !important;
      }
      .has-vivid-purple-color {
        color: var(--wp--preset--color--vivid-purple) !important;
      }
      .has-black-background-color {
        background-color: var(--wp--preset--color--black) !important;
      }
      .has-cyan-bluish-gray-background-color {
        background-color: var(--wp--preset--color--cyan-bluish-gray) !important;
      }
      .has-white-background-color {
        background-color: var(--wp--preset--color--white) !important;
      }
      .has-pale-pink-background-color {
        background-color: var(--wp--preset--color--pale-pink) !important;
      }
      .has-vivid-red-background-color {
        background-color: var(--wp--preset--color--vivid-red) !important;
      }
      .has-luminous-vivid-orange-background-color {
        background-color: var(
          --wp--preset--color--luminous-vivid-orange
        ) !important;
      }
      .has-luminous-vivid-amber-background-color {
        background-color: var(
          --wp--preset--color--luminous-vivid-amber
        ) !important;
      }
      .has-light-green-cyan-background-color {
        background-color: var(--wp--preset--color--light-green-cyan) !important;
      }
      .has-vivid-green-cyan-background-color {
        background-color: var(--wp--preset--color--vivid-green-cyan) !important;
      }
      .has-pale-cyan-blue-background-color {
        background-color: var(--wp--preset--color--pale-cyan-blue) !important;
      }
      .has-vivid-cyan-blue-background-color {
        background-color: var(--wp--preset--color--vivid-cyan-blue) !important;
      }
      .has-vivid-purple-background-color {
        background-color: var(--wp--preset--color--vivid-purple) !important;
      }
      .has-black-border-color {
        border-color: var(--wp--preset--color--black) !important;
      }
      .has-cyan-bluish-gray-border-color {
        border-color: var(--wp--preset--color--cyan-bluish-gray) !important;
      }
      .has-white-border-color {
        border-color: var(--wp--preset--color--white) !important;
      }
      .has-pale-pink-border-color {
        border-color: var(--wp--preset--color--pale-pink) !important;
      }
      .has-vivid-red-border-color {
        border-color: var(--wp--preset--color--vivid-red) !important;
      }
      .has-luminous-vivid-orange-border-color {
        border-color: var(
          --wp--preset--color--luminous-vivid-orange
        ) !important;
      }
      .has-luminous-vivid-amber-border-color {
        border-color: var(--wp--preset--color--luminous-vivid-amber) !important;
      }
      .has-light-green-cyan-border-color {
        border-color: var(--wp--preset--color--light-green-cyan) !important;
      }
      .has-vivid-green-cyan-border-color {
        border-color: var(--wp--preset--color--vivid-green-cyan) !important;
      }
      .has-pale-cyan-blue-border-color {
        border-color: var(--wp--preset--color--pale-cyan-blue) !important;
      }
      .has-vivid-cyan-blue-border-color {
        border-color: var(--wp--preset--color--vivid-cyan-blue) !important;
      }
      .has-vivid-purple-border-color {
        border-color: var(--wp--preset--color--vivid-purple) !important;
      }
      .has-vivid-cyan-blue-to-vivid-purple-gradient-background {
        background: var(
          --wp--preset--gradient--vivid-cyan-blue-to-vivid-purple
        ) !important;
      }
      .has-light-green-cyan-to-vivid-green-cyan-gradient-background {
        background: var(
          --wp--preset--gradient--light-green-cyan-to-vivid-green-cyan
        ) !important;
      }
      .has-luminous-vivid-amber-to-luminous-vivid-orange-gradient-background {
        background: var(
          --wp--preset--gradient--luminous-vivid-amber-to-luminous-vivid-orange
        ) !important;
      }
      .has-luminous-vivid-orange-to-vivid-red-gradient-background {
        background: var(
          --wp--preset--gradient--luminous-vivid-orange-to-vivid-red
        ) !important;
      }
      .has-very-light-gray-to-cyan-bluish-gray-gradient-background {
        background: var(
          --wp--preset--gradient--very-light-gray-to-cyan-bluish-gray
        ) !important;
      }
      .has-cool-to-warm-spectrum-gradient-background {
        background: var(
          --wp--preset--gradient--cool-to-warm-spectrum
        ) !important;
      }
      .has-blush-light-purple-gradient-background {
        background: var(--wp--preset--gradient--blush-light-purple) !important;
      }
      .has-blush-bordeaux-gradient-background {
        background: var(--wp--preset--gradient--blush-bordeaux) !important;
      }
      .has-luminous-dusk-gradient-background {
        background: var(--wp--preset--gradient--luminous-dusk) !important;
      }
      .has-pale-ocean-gradient-background {
        background: var(--wp--preset--gradient--pale-ocean) !important;
      }
      .has-electric-grass-gradient-background {
        background: var(--wp--preset--gradient--electric-grass) !important;
      }
      .has-midnight-gradient-background {
        background: var(--wp--preset--gradient--midnight) !important;
      }
      .has-small-font-size {
        font-size: var(--wp--preset--font-size--small) !important;
      }
      .has-medium-font-size {
        font-size: var(--wp--preset--font-size--medium) !important;
      }
      .has-large-font-size {
        font-size: var(--wp--preset--font-size--large) !important;
      }
      .has-x-large-font-size {
        font-size: var(--wp--preset--font-size--x-large) !important;
      }
      :where(.wp-block-post-template.is-layout-flex) {
        gap: 1.25em;
      }
      :where(.wp-block-post-template.is-layout-grid) {
        gap: 1.25em;
      }
      :where(.wp-block-columns.is-layout-flex) {
        gap: 2em;
      }
      :where(.wp-block-columns.is-layout-grid) {
        gap: 2em;
      }
      :root :where(.wp-block-pullquote) {
        font-size: 1.5em;
        line-height: 1.6;
      }
    </style>
    <link rel='stylesheet' id='wp-block-library-css' href='assets/css/style.min.css?ver=6.8.3' media='all' />
    <link rel="stylesheet" id="elementor-post-78-css" href="assets/css/post-78.css?ver=1759375109" media="all">
    <link rel="stylesheet" id="elementor-post-75-css" href="assets/css/post-75.css?ver=1759318906" media="all">
    <link rel="stylesheet" id="elementor-post-72-css" href="assets/css/post-72.css?ver=1759613698" media="all">
    <link rel="stylesheet" id="elementor-post-69-css" href="assets/css/post-69.css?ver=1759810884" media="all">
    <link rel='stylesheet' id='elementor-post-66-css' href='assets/css/post-66.css?ver=1759560654' media='all' />

        <link
      rel="stylesheet"
      id="elementor-post-47-css"
      href="assets/css/post-47.css?ver=1759272020"
      media="all"
    />
      <link
      rel="stylesheet"
      id="elementor-post-46-css"
      href="assets/css/post-46.css?ver=1759272019"
      media="all"
    />
    <link
      rel="stylesheet"
      id="contact-form-7-css"
      href="assets/css/styles.css?ver=6.0.5"
      media="all"
    />
    <link
      rel="stylesheet"
      id="hfe-style-css"
      href="assets/css/header-footer-elementor.css?ver=2.2.0"
      media="all"
    />
    <link
      rel="stylesheet"
      id="elementor-frontend-css"
      href="assets/css/frontend.min.css?ver=3.27.6"
      media="all"
    />
    <link
      rel="stylesheet"
      id="elementor-post-62-css"
      href="assets/css/post-62.css?ver=1759272017"
      media="all"
    />
    <link
      rel="stylesheet"
      id="widget-heading-css"
      href="assets/css/widget-heading.min.css?ver=3.27.6"
      media="all"
    />
    <link
      rel="stylesheet"
      id="swiper-css"
      href="assets/css/swiper.min.css?ver=8.4.5"
      media="all"
    />
    <link
      rel="stylesheet"
      id="e-swiper-css"
      href="assets/css/e-swiper.min.css?ver=3.27.6"
      media="all"
    />
    <link
      rel="stylesheet"
      id="widget-text-editor-css"
      href="assets/css/widget-text-editor.min.css?ver=3.27.6"
      media="all"
    />
    <link
      rel="stylesheet"
      id="widget-image-css"
      href="assets/css/widget-image.min.css?ver=3.27.6"
      media="all"
    />
    <link
      rel="stylesheet"
      id="e-animation-float-css"
      href="assets/css/e-animation-float.min.css?ver=3.27.6"
      media="all"
    />
    <link
      rel="stylesheet"
      id="e-animation-fadeInUp-css"
      href="assets/css/fadeInUp.min.css?ver=3.27.6"
      media="all"
    />
    <link
      rel="stylesheet"
      id="e-animation-fadeInLeft-css"
      href="assets/css/fadeInLeft.min.css?ver=3.27.6"
      media="all"
    />
    <link
      rel="stylesheet"
      id="widget-social-icons-css"
      href="assets/css/widget-social-icons.min.css?ver=3.27.6"
      media="all"
    />
    <link
      rel="stylesheet"
      id="e-apple-webkit-css"
      href="assets/css/apple-webkit.min.css?ver=3.27.6"
      media="all"
    />
    <link
      rel="stylesheet"
      id="e-animation-fadeInRight-css"
      href="assets/css/fadeInRight.min.css?ver=3.27.6"
      media="all"
    />
    <link
      rel="stylesheet"
      id="elementor-post-49-css"
      href="assets/css/post-49.css?ver=1759272018"
      media="all"
    />
    <link
      rel="stylesheet"
      id="elementor-post-119-css"
      href="assets/css/post-119.css?ver=1759272018"
      media="all"
    />
    <link
      rel="stylesheet"
      id="kadence-global-css"
      href="assets/css/global.min.css?ver=1.2.16"
      media="all"
    />
    <style id="kadence-global-inline-css">
      /* Kadence Base CSS */
      :root {
        --global-palette1: #303ae8;
        --global-palette2: #215387;
        --global-palette3: #1a202c;
        --global-palette4: #2d3748;
        --global-palette5: #4a5568;
        --global-palette6: #718096;
        --global-palette7: #edf2f7;
        --global-palette8: #f7fafc;
        --global-palette9: #ffffff;
        --global-palette9rgb: 255, 255, 255;
        --global-palette-highlight: var(--global-palette1);
        --global-palette-highlight-alt: var(--global-palette2);
        --global-palette-highlight-alt2: var(--global-palette9);
        --global-palette-btn-bg: var(--global-palette1);
        --global-palette-btn-bg-hover: var(--global-palette2);
        --global-palette-btn: var(--global-palette9);
        --global-palette-btn-hover: var(--global-palette9);
        --global-body-font-family: -apple-system, BlinkMacSystemFont, "Segoe UI",
          Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif,
          "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        --global-heading-font-family: inherit;
        --global-primary-nav-font-family: inherit;
        --global-fallback-font: sans-serif;
        --global-display-fallback-font: sans-serif;
        --global-content-width: 1290px;
        --global-content-narrow-width: 842px;
        --global-content-edge-padding: 1.5rem;
        --global-content-boxed-padding: 2rem;
        --global-calc-content-width: calc(
          1290px - var(--global-content-edge-padding) -
            var(--global-content-edge-padding)
        );
        --wp--style--global--content-size: var(--global-calc-content-width);
      }
      .wp-site-blocks {
        --global-vw: calc(100vw - (0.5 * var(--scrollbar-offset)));
      }
      :root body.kadence-elementor-colors {
        --e-global-color-kadence1: var(--global-palette1);
        --e-global-color-kadence2: var(--global-palette2);
        --e-global-color-kadence3: var(--global-palette3);
        --e-global-color-kadence4: var(--global-palette4);
        --e-global-color-kadence5: var(--global-palette5);
        --e-global-color-kadence6: var(--global-palette6);
        --e-global-color-kadence7: var(--global-palette7);
        --e-global-color-kadence8: var(--global-palette8);
        --e-global-color-kadence9: var(--global-palette9);
      }
      body {
        background: var(--global-palette8);
      }
      body,
      input,
      select,
      optgroup,
      textarea {
        font-weight: 400;
        font-size: 17px;
        line-height: 1.6;
        font-family: var(--global-body-font-family);
        color: var(--global-palette4);
      }
      .content-bg,
      body.content-style-unboxed .site {
        background: var(--global-palette9);
      }
      h1,
      h2,
      h3,
      h4,
      h5,
      h6 {
        font-family: var(--global-heading-font-family);
      }
      h1 {
        font-weight: 700;
        font-size: 32px;
        line-height: 1.5;
        color: var(--global-palette3);
      }
      h2 {
        font-weight: 700;
        font-size: 28px;
        line-height: 1.5;
        color: var(--global-palette3);
      }
      h3 {
        font-weight: 700;
        font-size: 24px;
        line-height: 1.5;
        color: var(--global-palette3);
      }
      h4 {
        font-weight: 700;
        font-size: 22px;
        line-height: 1.5;
        color: var(--global-palette4);
      }
      h5 {
        font-weight: 700;
        font-size: 20px;
        line-height: 1.5;
        color: var(--global-palette4);
      }
      h6 {
        font-weight: 700;
        font-size: 18px;
        line-height: 1.5;
        color: var(--global-palette5);
      }
      .entry-hero .kadence-breadcrumbs {
        max-width: 1290px;
      }
      .site-container,
      .site-header-row-layout-contained,
      .site-footer-row-layout-contained,
      .entry-hero-layout-contained,
      .comments-area,
      .alignfull > .wp-block-cover__inner-container,
      .alignwide > .wp-block-cover__inner-container {
        max-width: var(--global-content-width);
      }
      .content-width-narrow .content-container.site-container,
      .content-width-narrow .hero-container.site-container {
        max-width: var(--global-content-narrow-width);
      }
      @media all and (min-width: 1520px) {
        .wp-site-blocks .content-container .alignwide {
          margin-left: -115px;
          margin-right: -115px;
          width: unset;
          max-width: unset;
        }
      }
      @media all and (min-width: 1102px) {
        .content-width-narrow .wp-site-blocks .content-container .alignwide {
          margin-left: -130px;
          margin-right: -130px;
          width: unset;
          max-width: unset;
        }
      }
      .content-style-boxed .wp-site-blocks .entry-content .alignwide {
        margin-left: calc(-1 * var(--global-content-boxed-padding));
        margin-right: calc(-1 * var(--global-content-boxed-padding));
      }
      .content-area {
        margin-top: 5rem;
        margin-bottom: 5rem;
      }
      @media all and (max-width: 1024px) {
        .content-area {
          margin-top: 3rem;
          margin-bottom: 3rem;
        }
      }
      @media all and (max-width: 767px) {
        .content-area {
          margin-top: 2rem;
          margin-bottom: 2rem;
        }
      }
      @media all and (max-width: 1024px) {
        :root {
          --global-content-boxed-padding: 2rem;
        }
      }
      @media all and (max-width: 767px) {
        :root {
          --global-content-boxed-padding: 1.5rem;
        }
      }
      .entry-content-wrap {
        padding: 2rem;
      }
      @media all and (max-width: 1024px) {
        .entry-content-wrap {
          padding: 2rem;
        }
      }
      @media all and (max-width: 767px) {
        .entry-content-wrap {
          padding: 1.5rem;
        }
      }
      .entry.single-entry {
        box-shadow: 0px 15px 15px -10px rgba(0, 0, 0, 0.05);
      }
      .entry.loop-entry {
        box-shadow: 0px 15px 15px -10px rgba(0, 0, 0, 0.05);
      }
      .loop-entry .entry-content-wrap {
        padding: 2rem;
      }
      @media all and (max-width: 1024px) {
        .loop-entry .entry-content-wrap {
          padding: 2rem;
        }
      }
      @media all and (max-width: 767px) {
        .loop-entry .entry-content-wrap {
          padding: 1.5rem;
        }
      }
      button,
      .button,
      .wp-block-button__link,
      input[type="button"],
      input[type="reset"],
      input[type="submit"],
      .fl-button,
      .elementor-button-wrapper .elementor-button,
      .wc-block-components-checkout-place-order-button,
      .wc-block-cart__submit {
        box-shadow: 0px 0px 0px -7px rgba(0, 0, 0, 0);
      }
      button:hover,
      button:focus,
      button:active,
      .button:hover,
      .button:focus,
      .button:active,
      .wp-block-button__link:hover,
      .wp-block-button__link:focus,
      .wp-block-button__link:active,
      input[type="button"]:hover,
      input[type="button"]:focus,
      input[type="button"]:active,
      input[type="reset"]:hover,
      input[type="reset"]:focus,
      input[type="reset"]:active,
      input[type="submit"]:hover,
      input[type="submit"]:focus,
      input[type="submit"]:active,
      .elementor-button-wrapper .elementor-button:hover,
      .elementor-button-wrapper .elementor-button:focus,
      .elementor-button-wrapper .elementor-button:active,
      .wc-block-cart__submit:hover {
        box-shadow: 0px 15px 25px -7px rgba(0, 0, 0, 0.1);
      }
      .kb-button.kb-btn-global-outline.kb-btn-global-inherit {
        padding-top: calc(px - 2px);
        padding-right: calc(px - 2px);
        padding-bottom: calc(px - 2px);
        padding-left: calc(px - 2px);
      }
      @media all and (min-width: 1025px) {
        .transparent-header .entry-hero .entry-hero-container-inner {
          padding-top: calc(0px + 80px);
        }
      }
      @media all and (max-width: 1024px) {
        .mobile-transparent-header .entry-hero .entry-hero-container-inner {
          padding-top: 80px;
        }
      }
      @media all and (max-width: 767px) {
        .mobile-transparent-header .entry-hero .entry-hero-container-inner {
          padding-top: 80px;
        }
      }
      .entry-hero.page-hero-section .entry-header {
        min-height: 200px;
      }
      /* Kadence Header CSS */
      @media all and (max-width: 1024px) {
        .mobile-transparent-header #masthead {
          position: absolute;
          left: 0px;
          right: 0px;
          z-index: 100;
        }
        .kadence-scrollbar-fixer.mobile-transparent-header #masthead {
          right: var(--scrollbar-offset, 0);
        }
        .mobile-transparent-header #masthead,
        .mobile-transparent-header
          .site-top-header-wrap
          .site-header-row-container-inner,
        .mobile-transparent-header
          .site-main-header-wrap
          .site-header-row-container-inner,
        .mobile-transparent-header
          .site-bottom-header-wrap
          .site-header-row-container-inner {
          background: transparent;
        }
        .site-header-row-tablet-layout-fullwidth,
        .site-header-row-tablet-layout-standard {
          padding: 0px;
        }
      }
      @media all and (min-width: 1025px) {
        .transparent-header #masthead {
          position: absolute;
          left: 0px;
          right: 0px;
          z-index: 100;
        }
        .transparent-header.kadence-scrollbar-fixer #masthead {
          right: var(--scrollbar-offset, 0);
        }
        .transparent-header #masthead,
        .transparent-header
          .site-top-header-wrap
          .site-header-row-container-inner,
        .transparent-header
          .site-main-header-wrap
          .site-header-row-container-inner,
        .transparent-header
          .site-bottom-header-wrap
          .site-header-row-container-inner {
          background: transparent;
        }
      }
      .site-branding a.brand img {
        max-width: 220px;
      }
      .site-branding a.brand img.svg-logo-image {
        width: 220px;
      }
      .site-branding {
        padding: 0px 0px 0px 0px;
      }
      #masthead,
      #masthead
        .kadence-sticky-header.item-is-fixed:not(.item-at-start):not(.site-header-row-container):not(.site-main-header-wrap),
      #masthead
        .kadence-sticky-header.item-is-fixed:not(.item-at-start)
        > .site-header-row-container-inner {
        background: #ffffff;
      }
      .site-main-header-wrap .site-header-row-container-inner {
        background: #eaeaff;
      }
      .site-main-header-inner-wrap {
        min-height: 80px;
      }
      .site-top-header-wrap .site-header-row-container-inner {
        background: #303ae8;
      }
      .site-top-header-inner-wrap {
        min-height: 0px;
      }
      .site-top-header-wrap .site-header-row-container-inner > .site-container {
        padding: 5px 0px 5px 0px;
      }
      .header-navigation[class*="header-navigation-style-underline"]
        .header-menu-container.primary-menu-container
        > ul
        > li
        > a:after {
        width: calc(100% - 2.94em);
      }
      .main-navigation .primary-menu-container > ul > li.menu-item > a {
        padding-left: calc(2.94em / 2);
        padding-right: calc(2.94em / 2);
        padding-top: 0em;
        padding-bottom: 0em;
        color: #000000;
      }
      .main-navigation
        .primary-menu-container
        > ul
        > li.menu-item
        .dropdown-nav-special-toggle {
        right: calc(2.94em / 2);
      }
      .main-navigation .primary-menu-container > ul > li.menu-item > a:hover {
        color: var(--global-palette2);
      }
      .main-navigation
        .primary-menu-container
        > ul
        > li.menu-item.current-menu-item
        > a {
        color: var(--global-palette3);
      }
      .header-navigation .header-menu-container ul ul.sub-menu,
      .header-navigation .header-menu-container ul ul.submenu {
        background: var(--global-palette1);
        box-shadow: 0px 2px 13px 0px rgba(0, 0, 0, 0.1);
      }
      .header-navigation .header-menu-container ul ul li.menu-item,
      .header-menu-container
        ul.menu
        > li.kadence-menu-mega-enabled
        > ul
        > li.menu-item
        > a {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      }
      .header-navigation .header-menu-container ul ul li.menu-item > a {
        width: 213px;
        padding-top: 1.09em;
        padding-bottom: 1.09em;
        color: var(--global-palette8);
        font-style: normal;
        font-weight: 400;
        font-size: 16px;
        line-height: 0.872;
      }
      .header-navigation .header-menu-container ul ul li.menu-item > a:hover {
        color: var(--global-palette9);
        background: var(--global-palette4);
      }
      .header-navigation
        .header-menu-container
        ul
        ul
        li.menu-item.current-menu-item
        > a {
        color: var(--global-palette9);
        background: var(--global-palette4);
      }
      .mobile-toggle-open-container .menu-toggle-open,
      .mobile-toggle-open-container .menu-toggle-open:focus {
        color: var(--global-palette5);
        padding: 0.4em 0.6em 0.4em 0.6em;
        font-size: 14px;
      }
      .mobile-toggle-open-container
        .menu-toggle-open.menu-toggle-style-bordered {
        border: 1px solid currentColor;
      }
      .mobile-toggle-open-container .menu-toggle-open .menu-toggle-icon {
        font-size: 20px;
      }
      .mobile-toggle-open-container .menu-toggle-open:hover,
      .mobile-toggle-open-container .menu-toggle-open:focus-visible {
        color: var(--global-palette-highlight);
      }
      .mobile-navigation ul li {
        font-size: 14px;
      }
      .mobile-navigation ul li a {
        padding-top: 1em;
        padding-bottom: 1em;
      }
      .mobile-navigation ul li > a,
      .mobile-navigation ul li.menu-item-has-children > .drawer-nav-drop-wrap {
        color: var(--global-palette8);
      }
      .mobile-navigation ul li.current-menu-item > a,
      .mobile-navigation
        ul
        li.current-menu-item.menu-item-has-children
        > .drawer-nav-drop-wrap {
        color: var(--global-palette-highlight);
      }
      .mobile-navigation ul li.menu-item-has-children .drawer-nav-drop-wrap,
      .mobile-navigation ul li:not(.menu-item-has-children) a {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      }
      .mobile-navigation:not(.drawer-navigation-parent-toggle-true)
        ul
        li.menu-item-has-children
        .drawer-nav-drop-wrap
        button {
        border-left: 1px solid rgba(255, 255, 255, 0.1);
      }
      #mobile-drawer .drawer-header .drawer-toggle {
        padding: 0.6em 0.15em 0.6em 0.15em;
        font-size: 24px;
      }
      #main-header .header-button {
        border-radius: 25px 25px 25px 25px;
        color: #050000;
        background: #ffffff;
        border: 2px none transparent;
        box-shadow: 5px -10px 0px -7px rgba(0, 0, 0, 0);
      }
      #main-header .header-button:hover {
        box-shadow: 0px 15px 25px -7px rgba(0, 0, 0, 0.1);
      }
      .header-html {
        font-style: normal;
        color: #ffffff;
      }
      .header-social-wrap .header-social-inner-wrap {
        font-size: 1em;
        gap: 0.49em;
      }
      .header-social-wrap .header-social-inner-wrap .social-button {
        color: var(--global-palette4);
        border: 2px none transparent;
        border-radius: 11px;
      }
      /* Kadence Footer CSS */
      .site-bottom-footer-inner-wrap {
        padding-top: 30px;
        padding-bottom: 30px;
        grid-column-gap: 30px;
      }
      .site-bottom-footer-inner-wrap .widget {
        margin-bottom: 30px;
      }
      .site-bottom-footer-inner-wrap
        .site-footer-section:not(:last-child):after {
        right: calc(-30px / 2);
      }
    </style>
    <link
      rel="stylesheet"
      id="kadence-simplelightbox-css-css"
      href="assets/css/simplelightbox.min.css?ver=1.2.16"
      media="all"
    />
    <link
      rel="stylesheet"
      id="kadence-header-css"
      href="assets/css/header.min.css?ver=1.2.16"
      media="all"
    />
    <link
      rel="stylesheet"
      id="kadence-content-css"
      href="assets/css/content.min.css?ver=1.2.16"
      media="all"
    />
    <link
      rel="stylesheet"
      id="kadence-footer-css"
      href="assets/css/footer.min.css?ver=1.2.16"
      media="all"
    />
    <link
      rel="stylesheet"
      id="hfe-elementor-icons-css"
      href="assets/css/elementor-icons.min.css?ver=5.34.0"
      media="all"
    />
    <link
      rel="stylesheet"
      id="hfe-icons-list-css"
      href="assets/css/widget-icon-list.min.css?ver=3.24.3"
      media="all"
    />
    <link
      rel="stylesheet"
      id="hfe-social-icons-css"
      href="assets/css/widget-social-icons.min.css?ver=3.24.0"
      media="all"
    />
    <link
      rel="stylesheet"
      id="hfe-social-share-icons-brands-css"
      href="assets/css/brands.css?ver=5.15.3"
      media="all"
    />
    <link
      rel="stylesheet"
      id="hfe-social-share-icons-fontawesome-css"
      href="assets/css/fontawesome.css?ver=5.15.3"
      media="all"
    />
    <link
      rel="stylesheet"
      id="hfe-nav-menu-icons-css"
      href="assets/css/solid.css?ver=5.15.3"
      media="all"
    />
    <link
      rel="stylesheet"
      id="google-fonts-1-css"
      href="https://fonts.googleapis.com/css?family=Roboto%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CRoboto+Slab%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic&#038;display=swap&#038;ver=6.8.3"
      media="all"
    />
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
    <script
      src="assets/js/jquery.min.js?ver=3.7.1"
      id="jquery-core-js"
    ></script>
    <script
      src="assets/js/jquery-migrate.min.js?ver=3.4.1"
      id="jquery-migrate-js"
    ></script>
    <script id="jquery-js-after">
      !(function ($) {
        "use strict";
        $(document).ready(function () {
          $(this).scrollTop() > 100 &&
            $(".hfe-scroll-to-top-wrap").removeClass("hfe-scroll-to-top-hide"),
            $(window).scroll(function () {
              $(this).scrollTop() < 100
                ? $(".hfe-scroll-to-top-wrap").fadeOut(300)
                : $(".hfe-scroll-to-top-wrap").fadeIn(300);
            }),
            $(".hfe-scroll-to-top-wrap").on("click", function () {
              $("html, body").animate({ scrollTop: 0 }, 300);
              return !1;
            });
        });
      })(jQuery);
    </script>
    <link rel="https://api.w.org/" href="https://asiantelecombd.com/wp-json/" />
    <link
      rel="alternate"
      title="JSON"
      type="application/json"
      href="https://asiantelecombd.com/wp-json/wp/v2/pages/49"
    />
    <link
      rel="EditURI"
      type="application/rsd+xml"
      title="RSD"
      href="https://asiantelecombd.com/xmlrpc.php?rsd"
    />
    <meta name="generator" content="WordPress 6.8.3" />
    <link rel="shortlink" href="https://asiantelecombd.com/" />
    <link
      rel="alternate"
      title="oEmbed (JSON)"
      type="application/json+oembed"
      href="https://asiantelecombd.com/wp-json/oembed/1.0/embed?url=https%3A%2F%2Fasiantelecombd.com%2F"
    />
    <link
      rel="alternate"
      title="oEmbed (XML)"
      type="text/xml+oembed"
      href="https://asiantelecombd.com/wp-json/oembed/1.0/embed?url=https%3A%2F%2Fasiantelecombd.com%2F&#038;format=xml"
    />
    <meta
      name="generator"
      content="Elementor 3.27.6; features: e_font_icon_svg, additional_custom_breakpoints, e_element_cache; settings: css_print_method-external, google_font-enabled, font_display-swap"
    />
    <script
      async
      src="https://www.googletagmanager.com/gtag/js?id=G-RE8R3C2EY6"
    ></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag() {
        dataLayer.push(arguments);
      }
      gtag("js", new Date());
      gtag("config", "G-RE8R3C2EY6");
    </script>
    <!-- Google tag (gtag.js) -->
    <script
      async
      src="https://www.googletagmanager.com/gtag/js?id=G-RE8R3C2EY6"
    ></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag() {
        dataLayer.push(arguments);
      }
      gtag("js", new Date());

      gtag("config", "G-RE8R3C2EY6");
    </script>
    <style>
      .e-con.e-parent:nth-of-type(n + 4):not(.e-lazyloaded):not(.e-no-lazyload),
      .e-con.e-parent:nth-of-type(n + 4):not(.e-lazyloaded):not(.e-no-lazyload)
        * {
        background-image: none !important;
      }
      @media screen and (max-height: 1024px) {
        .e-con.e-parent:nth-of-type(n
            + 3):not(.e-lazyloaded):not(.e-no-lazyload),
        .e-con.e-parent:nth-of-type(n
            + 3):not(.e-lazyloaded):not(.e-no-lazyload)
          * {
          background-image: none !important;
        }
      }
      @media screen and (max-height: 640px) {
        .e-con.e-parent:nth-of-type(n
            + 2):not(.e-lazyloaded):not(.e-no-lazyload),
        .e-con.e-parent:nth-of-type(n
            + 2):not(.e-lazyloaded):not(.e-no-lazyload)
          * {
          background-image: none !important;
        }
      }
    </style>
    <link
      rel="icon"
      href="<?= setting('favicon') 
        ? 'admin/' . setting('favicon') 
        : 'assets/img/cropped-asiantelecom-favicon.png'; ?>"
      sizes="32x32"
    />
    <link
      rel="icon"
      href="<?= setting('favicon') 
        ? 'admin/' . setting('favicon') 
        : 'assets/img/cropped-asiantelecom-favicon.png'; ?>"
      sizes="192x192"
    />
    <link
      rel="apple-touch-icon"
      href="<?= setting('favicon') 
        ? 'admin/' . setting('favicon') 
        : 'assets/img/cropped-asiantelecom-favicon.png'; ?>"
    />
    <meta
      name="msapplication-TileImage"
      content="<?= setting('favicon') 
        ? 'admin/' . setting('favicon') 
        : 'assets/img/cropped-asiantelecom-favicon.png'; ?>"
    />
  </head>

  <body
    class="home wp-singular page-template page-template-elementor_header_footer page page-id-49 wp-custom-logo wp-embed-responsive wp-theme-kadence ehf-footer ehf-template-kadence ehf-stylesheet-kadence footer-on-bottom hide-focus-outline link-style-standard content-title-style-hide content-width-fullwidth content-style-unboxed content-vertical-padding-hide non-transparent-header mobile-non-transparent-header kadence-elementor-colors elementor-default elementor-template-full-width elementor-kit-62 elementor-page elementor-page-49"
  >
    <div id="wrapper" class="site wp-site-blocks">
      <a class="skip-link screen-reader-text scroll-ignore" href="#main"
        >Skip to content</a
      >
<header
        id="masthead"
        class="site-header"
        role="banner"
        itemtype="https://schema.org/WPHeader"
        itemscope
      >
        <div id="main-header" class="site-header-wrap">
          <div class="site-header-inner-wrap">
            <div class="site-header-upper-wrap">
              <div class="site-header-upper-inner-wrap">
                <div
                  class="site-top-header-wrap site-header-row-container site-header-focus-item site-header-row-layout-standard"
                  data-section="kadence_customizer_header_top"
                >
                  <div class="site-header-row-container-inner">
                    <div class="site-container">
                      <div
                        class="site-top-header-inner-wrap site-header-row site-header-row-has-sides site-header-row-center-column"
                      >
                        <div
                          class="site-header-top-section-left site-header-section site-header-section-left"
                        >
                          <div
                            class="site-header-item site-header-focus-item"
                            data-section="kadence_customizer_header_html"
                          >
                            <div class="header-html inner-link-style-normal">
                              <div class="header-html-inner">
                                <p>
                                    <?= setting('address') ? setting('address') : 'Dhaka, Bangladesh'; ?>
                                </p>

                              </div>
                            </div>
                          </div>
                          <!-- data-section="header_html" -->
                          <div
                            class="site-header-top-section-left-center site-header-section site-header-section-left-center"
                          ></div>
                        </div>
                        <div
                          class="site-header-top-section-center site-header-section site-header-section-center"
                        >
                          <div
                            class="site-header-item site-header-focus-item"
                            data-section="kadence_customizer_header_button"
                          >
                            <div class="header-button-wrap">
                              <div class="header-button-inner-wrap">
                                <a
                                  href=""
                                  target="_self"
                                  class="button header-button button-size-small button-style-filled"
                                  > <?= setting('email') ?? 'support@nanoratech.com'; ?></a
                                >
                              </div>
                            </div>
                          </div>
                          <!-- data-section="header_button" -->
                        </div>
                        <div
                          class="site-header-top-section-right site-header-section site-header-section-right"
                        >
                          <div
                            class="site-header-top-section-right-center site-header-section site-header-section-right-center"
                          ></div>
                          <div
                            class="site-header-item site-header-focus-item"
                            data-section="kadence_customizer_header_social"
                          >
                            <div class="header-social-wrap">
                              <div
                                class="header-social-inner-wrap element-social-inner-wrap social-show-label-false social-style-filled"
                              >
                                <a
                                  href="<?= setting('facebook') ?? '#' ?>"
                                  aria-label="Facebook"
                                  target="_blank"
                                  rel="noopener noreferrer"
                                  class="social-button header-social-item social-link-facebook"
                                  ><span class="kadence-svg-iconset"
                                    ><svg
                                      class="kadence-svg-icon kadence-facebook-svg"
                                      fill="currentColor"
                                      version="1.1"
                                      xmlns="http://www.w3.org/2000/svg"
                                      width="32"
                                      height="32"
                                      viewBox="0 0 32 32"
                                    >
                                      <title>Facebook</title>
                                      <path
                                        d="M31.997 15.999c0-8.836-7.163-15.999-15.999-15.999s-15.999 7.163-15.999 15.999c0 7.985 5.851 14.604 13.499 15.804v-11.18h-4.062v-4.625h4.062v-3.525c0-4.010 2.389-6.225 6.043-6.225 1.75 0 3.581 0.313 3.581 0.313v3.937h-2.017c-1.987 0-2.607 1.233-2.607 2.498v3.001h4.437l-0.709 4.625h-3.728v11.18c7.649-1.2 13.499-7.819 13.499-15.804z"
                                      ></path></svg></span></a
                                ><a
                                  href="<?= setting('twitter') ?? '#' ?>"
                                  aria-label="X"
                                  target="_blank"
                                  rel="noopener noreferrer"
                                  class="social-button header-social-item social-link-twitter"
                                  ><span class="kadence-svg-iconset"
                                    ><svg
                                      class="kadence-svg-icon kadence-twitter-x-svg"
                                      fill="currentColor"
                                      version="1.1"
                                      xmlns="http://www.w3.org/2000/svg"
                                      width="23"
                                      height="24"
                                      viewBox="0 0 23 24"
                                    >
                                      <title>X</title>
                                      <path
                                        d="M13.969 10.157l8.738-10.157h-2.071l-7.587 8.819-6.060-8.819h-6.989l9.164 13.336-9.164 10.651h2.071l8.012-9.313 6.4 9.313h6.989l-9.503-13.831zM11.133 13.454l-8.316-11.895h3.181l14.64 20.941h-3.181l-6.324-9.046z"
                                      ></path></svg></span></a
                                ><a
                                  href="<?= setting('instagram') ?? '#' ?>"
                                  aria-label="Instagram"
                                  target="_blank"
                                  rel="noopener noreferrer"
                                  class="social-button header-social-item social-link-instagram"
                                  ><span class="kadence-svg-iconset"
                                    ><svg
                                      class="kadence-svg-icon kadence-instagram-alt-svg"
                                      fill="currentColor"
                                      version="1.1"
                                      xmlns="http://www.w3.org/2000/svg"
                                      width="24"
                                      height="24"
                                      viewBox="0 0 24 24"
                                    >
                                      <title>Instagram</title>
                                      <path
                                        d="M7 1c-1.657 0-3.158 0.673-4.243 1.757s-1.757 2.586-1.757 4.243v10c0 1.657 0.673 3.158 1.757 4.243s2.586 1.757 4.243 1.757h10c1.657 0 3.158-0.673 4.243-1.757s1.757-2.586 1.757-4.243v-10c0-1.657-0.673-3.158-1.757-4.243s-2.586-1.757-4.243-1.757zM7 3h10c1.105 0 2.103 0.447 2.828 1.172s1.172 1.723 1.172 2.828v10c0 1.105-0.447 2.103-1.172 2.828s-1.723 1.172-2.828 1.172h-10c-1.105 0-2.103-0.447-2.828-1.172s-1.172-1.723-1.172-2.828v-10c0-1.105 0.447-2.103 1.172-2.828s1.723-1.172 2.828-1.172zM16.989 11.223c-0.15-0.972-0.571-1.857-1.194-2.567-0.754-0.861-1.804-1.465-3.009-1.644-0.464-0.074-0.97-0.077-1.477-0.002-1.366 0.202-2.521 0.941-3.282 1.967s-1.133 2.347-0.93 3.712 0.941 2.521 1.967 3.282 2.347 1.133 3.712 0.93 2.521-0.941 3.282-1.967 1.133-2.347 0.93-3.712zM15.011 11.517c0.122 0.82-0.1 1.609-0.558 2.227s-1.15 1.059-1.969 1.18-1.609-0.1-2.227-0.558-1.059-1.15-1.18-1.969 0.1-1.609 0.558-2.227 1.15-1.059 1.969-1.18c0.313-0.046 0.615-0.042 0.87-0.002 0.74 0.11 1.366 0.47 1.818 0.986 0.375 0.428 0.63 0.963 0.72 1.543zM17.5 7.5c0.552 0 1-0.448 1-1s-0.448-1-1-1-1 0.448-1 1 0.448 1 1 1z"
                                      ></path></svg></span></a
                                ><a
                                  href="<?= setting('youtube') ?? '#' ?>"
                                  aria-label="YouTube"
                                  target="_blank"
                                  rel="noopener noreferrer"
                                  class="social-button header-social-item social-link-youtube"
                                  ><span class="kadence-svg-iconset"
                                    ><svg
                                      class="kadence-svg-icon kadence-youtube-svg"
                                      fill="currentColor"
                                      version="1.1"
                                      xmlns="http://www.w3.org/2000/svg"
                                      width="28"
                                      height="28"
                                      viewBox="0 0 28 28"
                                    >
                                      <title>YouTube</title>
                                      <path
                                        d="M11.109 17.625l7.562-3.906-7.562-3.953v7.859zM14 4.156c5.891 0 9.797 0.281 9.797 0.281 0.547 0.063 1.75 0.063 2.812 1.188 0 0 0.859 0.844 1.109 2.781 0.297 2.266 0.281 4.531 0.281 4.531v2.125s0.016 2.266-0.281 4.531c-0.25 1.922-1.109 2.781-1.109 2.781-1.062 1.109-2.266 1.109-2.812 1.172 0 0-3.906 0.297-9.797 0.297v0c-7.281-0.063-9.516-0.281-9.516-0.281-0.625-0.109-2.031-0.078-3.094-1.188 0 0-0.859-0.859-1.109-2.781-0.297-2.266-0.281-4.531-0.281-4.531v-2.125s-0.016-2.266 0.281-4.531c0.25-1.937 1.109-2.781 1.109-2.781 1.062-1.125 2.266-1.125 2.812-1.188 0 0 3.906-0.281 9.797-0.281v0z"
                                      ></path></svg></span></a
                                ><a
                                  href="<?= setting('linkedin') ?? '#' ?>"
                                  aria-label="Phone"
                                  class="social-button header-social-item social-link-phone"
                                  ><span class="kadence-svg-iconset"
                                    ><svg
                                      class="kadence-svg-icon kadence-phone-svg"
                                      fill="currentColor"
                                      version="1.1"
                                      xmlns="http://www.w3.org/2000/svg"
                                      width="12"
                                      height="28"
                                      viewBox="0 0 12 28"
                                    >
                                      <title>Phone</title>
                                      <path
                                        d="M7.25 22c0-0.688-0.562-1.25-1.25-1.25s-1.25 0.562-1.25 1.25 0.562 1.25 1.25 1.25 1.25-0.562 1.25-1.25zM10.5 19.5v-11c0-0.266-0.234-0.5-0.5-0.5h-8c-0.266 0-0.5 0.234-0.5 0.5v11c0 0.266 0.234 0.5 0.5 0.5h8c0.266 0 0.5-0.234 0.5-0.5zM7.5 6.25c0-0.141-0.109-0.25-0.25-0.25h-2.5c-0.141 0-0.25 0.109-0.25 0.25s0.109 0.25 0.25 0.25h2.5c0.141 0 0.25-0.109 0.25-0.25zM12 6v16c0 1.094-0.906 2-2 2h-8c-1.094 0-2-0.906-2-2v-16c0-1.094 0.906-2 2-2h8c1.094 0 2 0.906 2 2z"
                                      ></path></svg></span
                                ></a>
                              </div>
                            </div>
                          </div>
                          <!-- data-section="header_social" -->
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div
                  class="site-main-header-wrap site-header-row-container site-header-focus-item site-header-row-layout-standard kadence-sticky-header"
                  data-section="kadence_customizer_header_main"
                  data-reveal-scroll-up="false"
                  data-shrink="false"
                >
                  <div class="site-header-row-container-inner">
                    <div class="site-container">
                      <div
                        class="site-main-header-inner-wrap site-header-row site-header-row-has-sides site-header-row-no-center"
                      >
                        <div
                          class="site-header-main-section-left site-header-section site-header-section-left"
                        >
                          <div
                            class="site-header-item site-header-focus-item"
                            data-section="title_tagline"
                          >
                            <div
                              class="site-branding branding-layout-standard site-brand-logo-only"
                            >
                            
                              <a
                                class="brand has-logo-image"
                                href="<?php echo $base_url; ?>"
                                rel="home"
                                ><img 
                                     src="<?= setting('header_logo') 
                                    ? 'admin/' . setting('header_logo') 
                                    : 'assets/img/Asian-Telecom-white-logo-PNG-01.png'; ?>"
                                    alt="Header Logo"
                                     
                                    width="8001"
                                    height="2918"
                                    class="custom-logo"
                                    alt="Audio phone system"
                                    decoding="async"
                                />
                                </a>
                            </div>
                          </div>
                          <!-- data-section="title_tagline" -->
                        </div>
                        <div
                          class="site-header-main-section-right site-header-section site-header-section-right"
                        >
                          <div
                            class="site-header-item site-header-focus-item site-header-item-main-navigation header-navigation-layout-stretch-false header-navigation-layout-fill-stretch-false"
                            data-section="kadence_customizer_primary_navigation"
                          >
                            <nav
                              id="site-navigation"
                              class="main-navigation header-navigation nav--toggle-sub header-navigation-style-standard header-navigation-dropdown-animation-none"
                              role="navigation"
                              aria-label="Primary Navigation"
                            >
                              <div
                                class="primary-menu-container header-menu-container"
                              >
                                <ul id="primary-menu" class="menu">
                                  <li
                                    id="menu-item-52"
                                    class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-49 current_page_item menu-item-52"
                                  >
                                    <a
                                      href="<?php echo $base_url; ?>"
                                      aria-current="page"
                                      >Home</a
                                    >
                                  </li>
                                  <li
                                    id="menu-item-54"
                                    class="menu-item menu-item-type-post_type menu-item-object-page menu-item-54"
                                  >
                                    <a href="<?php echo $base_url; ?>/about.php">About Us</a>
                                  </li>
                                  <li
                                    id="menu-item-56"
                                    class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-56"
                                  >
                                    <span class="nav-drop-title-wrap"
                                        >Services<span
                                          class="dropdown-nav-toggle"
                                          ><span
                                            class="kadence-svg-iconset svg-baseline"
                                            ><svg
                                              aria-hidden="true"
                                              class="kadence-svg-icon kadence-arrow-down-svg"
                                              fill="currentColor"
                                              version="1.1"
                                              xmlns="http://www.w3.org/2000/svg"
                                              width="24"
                                              height="24"
                                              viewBox="0 0 24 24"
                                            >
                                              <title>Expand</title>
                                              <path
                                                d="M5.293 9.707l6 6c0.391 0.391 1.024 0.391 1.414 0l6-6c0.391-0.391 0.391-1.024 0-1.414s-1.024-0.391-1.414 0l-5.293 5.293-5.293-5.293c-0.391-0.391-1.024-0.391-1.414 0s-0.391 1.024 0 1.414z"
                                              ></path></svg></span></span></span
                                    >
                                    <ul class="sub-menu">
                                      <!-- <li
                                        id="menu-item-64"
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-64"
                                      >
                                        <a
                                          href="<?php echo $base_url; ?>/reseller.php"
                                          >Reseller</a
                                        >
                                      </li> -->
                                      <li
                                        id="menu-item-67"
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-67"
                                      >
                                        <a
                                          href="<?php echo $base_url; ?>/calling-card.php"
                                          >Calling Card</a
                                        >
                                      </li>
                                      <li
                                        id="menu-item-70"
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-70"
                                      >
                                        <a
                                          href="<?php echo $base_url; ?>/calling-card-print.php"
                                          >Calling Card Print</a
                                        >
                                      </li>
                                      <li
                                        id="menu-item-73"
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-73"
                                      >
                                        <a
                                          href="<?php echo $base_url; ?>/a2z-voip-route.php"
                                          >A2Z Voip Route</a
                                        >
                                      </li>
                                      <li
                                        id="menu-item-76"
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-76"
                                      >
                                        <a
                                          href="<?php echo $base_url; ?>/cc-route.php"
                                          >CC Route</a
                                        >
                                      </li>
                                      <li
                                        id="menu-item-79"
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-79"
                                      >
                                        <a
                                          href="<?php echo $base_url; ?>/sms-route.php"
                                          >SMS Route</a
                                        >
                                      </li>
                                    </ul>
                                  </li>
                                  <li
                                    id="menu-item-53"
                                    class="menu-item menu-item-type-post_type menu-item-object-page menu-item-53"
                                  >
                                    <a href="<?php echo $base_url; ?>/blog.php"
                                      >Blog</a
                                    >
                                  </li>
                                  <li
                                    id="menu-item-53"
                                    class="menu-item menu-item-type-post_type menu-item-object-page menu-item-53"
                                  >
                                    <a href="<?php echo $base_url; ?>/rates.php"
                                      >Rates</a
                                    >
                                  </li>
                                  <li
                                    id="menu-item-53"
                                    class="menu-item menu-item-type-post_type menu-item-object-page menu-item-53"
                                  >
                                    <a href="<?php echo $base_url; ?>/download.php"
                                      >Download</a
                                    >
                                  </li>
                                  <li
                                    id="menu-item-55"
                                    class="menu-item menu-item-type-post_type menu-item-object-page menu-item-55"
                                  >
                                    <a
                                      href="<?php echo $base_url; ?>/contact.php"
                                      >Contact Us</a
                                    >
                                  </li>
                                </ul>
                              </div>
                            </nav>
                            <style>
                              

                              .header-login-btn {
                                      padding: 5px 20px;
                                      color: #fff; 
                                      background: linear-gradient(to right, #009dffff, #4e09eeff);
                                      border-radius: 4px;
                                      text-decoration: none;
                                      font-weight: 500;
                                      transition: all 0.3s ease;
                                  }
                                  .header-login-btn:hover {
                                      opacity: 0.85;
                                      color: #000000ff;
                                  }

                            </style>
                            <a href="http://167.17.69.158/billing" class="header-login-btn">Login</a>

                            <!-- #site-navigation -->
                          </div>
                          <!-- data-section="primary_navigation" -->
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div id="mobile-header" class="site-mobile-header-wrap">
          <div class="site-header-inner-wrap">
            <div class="site-header-upper-wrap">
              <div class="site-header-upper-inner-wrap">
                <div
                  class="site-main-header-wrap site-header-focus-item site-header-row-layout-standard site-header-row-tablet-layout-default site-header-row-mobile-layout-default"
                >
                  <div class="site-header-row-container-inner">
                    <div class="site-container">
                      <div
                        class="site-main-header-inner-wrap site-header-row site-header-row-has-sides site-header-row-no-center"
                      >
                        <div
                          class="site-header-main-section-left site-header-section site-header-section-left"
                        >
                          <div
                            class="site-header-item site-header-focus-item"
                            data-section="title_tagline"
                          >
                            <div
                              class="site-branding mobile-site-branding branding-layout-standard branding-tablet-layout-inherit site-brand-logo-only branding-mobile-layout-inherit"
                            >
                              <a
                                class="brand has-logo-image"
                                href="/"
                                rel="home"
                                ><img
                                  width="8001"
                                  height="2918"
                                  src="<?= setting('header_logo') 
                                    ? 'admin/' . setting('header_logo') 
                                    : 'assets/img/Asian-Telecom-white-logo-PNG-01.png'; ?>"
                                  class="custom-logo"
                                  alt="Asian Telecom is the Best Calling Card Mobile Dialer"
                                  decoding="async"
                                  
                                  sizes="(max-width: 8001px) 100vw, 8001px"
                              /></a>
                            </div>
                          </div>
                          <!-- data-section="title_tagline" -->
                        </div>
                        <div
                          class="site-header-main-section-right site-header-section site-header-section-right"
                        >
                          <div
                            class="site-header-item site-header-focus-item site-header-item-navgation-popup-toggle"
                            data-section="kadence_customizer_mobile_trigger"
                          >
                            <div class="mobile-toggle-open-container">
                              <button
                                id="mobile-toggle"
                                class="menu-toggle-open drawer-toggle menu-toggle-style-default"
                                aria-label="Open menu"
                                data-toggle-target="#mobile-drawer"
                                data-toggle-body-class="showing-popup-drawer-from-right"
                                aria-expanded="false"
                                data-set-focus=".menu-toggle-close"
                              >
                                <span class="menu-toggle-icon"
                                  ><span class="kadence-svg-iconset"
                                    ><svg
                                      aria-hidden="true"
                                      class="kadence-svg-icon kadence-menu-svg"
                                      fill="currentColor"
                                      version="1.1"
                                      xmlns="http://www.w3.org/2000/svg"
                                      width="24"
                                      height="24"
                                      viewBox="0 0 24 24"
                                    >
                                      <title>Toggle Menu</title>
                                      <path
                                        d="M3 13h18c0.552 0 1-0.448 1-1s-0.448-1-1-1h-18c-0.552 0-1 0.448-1 1s0.448 1 1 1zM3 7h18c0.552 0 1-0.448 1-1s-0.448-1-1-1h-18c-0.552 0-1 0.448-1 1s0.448 1 1 1zM3 19h18c0.552 0 1-0.448 1-1s-0.448-1-1-1h-18c-0.552 0-1 0.448-1 1s0.448 1 1 1z"
                                      ></path></svg></span
                                ></span>
                              </button>
                            </div>
                          </div>
                          <!-- data-section="mobile_trigger" -->
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </header>
      <!-- #masthead -->