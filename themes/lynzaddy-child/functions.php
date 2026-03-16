<?php

/**
 * Lynzaddy Child Theme — functions.php
 * Child of Hello Elementor
 *
 * What this file does:
 *   1. Enqueues the parent Hello Elementor stylesheet
 *   2. Enqueues this child theme's style.css
 *   3. Registers and enqueues Google Fonts (Playfair Display, DM Sans, JetBrains Mono)
 *   4. Registers brand CSS variables as a global inline style (fallback)
 *   5. Adds a scroll-reveal helper script (tiny, no jQuery dependency)
 *   6. Optional: registers theme support extras
 */

defined('ABSPATH') || exit;

/* ============================================================
   1 & 2. ENQUEUE PARENT + CHILD STYLESHEETS
   ============================================================ */

add_action('wp_enqueue_scripts', 'lynzaddy_enqueue_styles');

function lynzaddy_enqueue_styles()
{
    // Parent theme stylesheet
    wp_enqueue_style(
        'hello-elementor-style',
        get_template_directory_uri() . '/style.css',
        [],
        wp_get_theme('hello-elementor')->get('Version')
    );

    // Child theme stylesheet (your brand CSS)
    // 'elementor-frontend' added as dependency so this loads AFTER
    // Elementor's compiled per-widget CSS — ensures our overrides win.
    wp_enqueue_style(
        'lynzaddy-child-style',
        get_stylesheet_uri(),                  // points to this theme's style.css
        ['hello-elementor-style', 'elementor-frontend'],  // load after parent AND Elementor
        wp_get_theme()->get('Version')
    );

    // Google Fonts
    wp_enqueue_style(
        'lynzaddy-google-fonts',
        'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,800;1,700;1,800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&family=JetBrains+Mono:wght@400;500&display=swap',
        [],
        null   // null = no version string appended (Google handles cache)
    );
}

/* ============================================================
   3. PRECONNECT TO GOOGLE FONTS
      Adds <link rel="preconnect"> in <head> for faster
      font loading — good practice for Core Web Vitals.
   ============================================================ */

add_action('wp_head', 'lynzaddy_preconnect_fonts', 1);

function lynzaddy_preconnect_fonts()
{
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}

/* ============================================================
   4. BRAND CSS VARIABLES — Inline fallback
      Injects :root variables early in <head> so they're
      available to Elementor's own custom CSS fields too.
      (Your style.css also declares these — this is a belt-
      and-suspenders approach for the editor environment.)
   ============================================================ */

add_action('wp_head', 'lynzaddy_css_variables', 5);

function lynzaddy_css_variables()
{
?>
    <style id="lynzaddy-brand-tokens">
        :root {
            --lynz-midnight: #0D1B2A;
            --lynz-steel: #1E3A5F;
            --lynz-anchor: #2C5F8A;
            --lynz-gold: #D4943A;
            --lynz-copper: #B8702A;
            --lynz-cream: #F5F0E8;
            --lynz-ink: #0F0F0F;
            --lynz-ink-soft: #2A2A2A;
            --lynz-slate: #4A5568;
            --lynz-mist: #8C95A1;
            --lynz-rule: #E2E5E9;
            --lynz-light: #EEF2F7;
            --lynz-paper: #F9F8F6;
            --lynz-white: #FFFFFF;
            --lynz-font-display: 'Playfair Display', Georgia, serif;
            --lynz-font-body: 'DM Sans', system-ui, sans-serif;
            --lynz-font-mono: 'JetBrains Mono', monospace;
        }
    </style>
<?php
}

/* ============================================================
   5. SCROLL REVEAL SCRIPT
      Tiny IntersectionObserver — no jQuery, no library.
      Pairs with .lynz-reveal class in style.css.
      To use: Add "lynz-reveal" to any widget's
              Advanced > CSS Classes in Elementor.
              Add "lynz-reveal-delay-1" through "-4" for stagger.
   ============================================================ */

add_action('wp_footer', 'lynzaddy_scroll_reveal_script');

function lynzaddy_scroll_reveal_script()
{
?>
    <script id="lynzaddy-scroll-reveal">
        (function() {
            'use strict';
            if (typeof IntersectionObserver === 'undefined') return;

            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        // Unobserve after reveal so it doesn't un-animate
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -40px 0px'
            });

            // Observe all elements with the reveal class
            document.querySelectorAll('.lynz-reveal').forEach(function(el) {
                observer.observe(el);
            });
        })();
    </script>
<?php
}

/* ============================================================
   6. STICKY HEADER SCROLL CLASS
      Adds .scrolled to <body> when user scrolls past 40px.
      Elementor's Sticky option handles most of this natively,
      but this gives you extra targeting flexibility in CSS.
   ============================================================ */

add_action('wp_footer', 'lynzaddy_sticky_header_script');

function lynzaddy_sticky_header_script()
{
?>
    <script id="lynzaddy-sticky-header">
        (function() {
            'use strict';
            var body = document.body;
            var threshold = 40;

            function toggleScrolled() {
                if (window.scrollY > threshold) {
                    body.classList.add('lynz-scrolled');
                } else {
                    body.classList.remove('lynz-scrolled');
                }
            }

            window.addEventListener('scroll', toggleScrolled, {
                passive: true
            });
            toggleScrolled(); // run on load
        })();
    </script>
<?php
}

/* ============================================================
   7. THEME SUPPORT
   ============================================================ */

add_action('after_setup_theme', 'lynzaddy_theme_support');

function lynzaddy_theme_support()
{
    // Gutenberg editor styles
    add_theme_support('editor-styles');
    add_editor_style('style.css');

    // Wide/full width blocks
    add_theme_support('align-wide');

    // Custom logo
    add_theme_support('custom-logo', [
        'height'      => 80,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ]);

    // Title tag
    add_theme_support('title-tag');

    // Post thumbnails
    add_theme_support('post-thumbnails');

    // HTML5 markup
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'script',
        'style',
    ]);
}

/* ============================================================
   8. ELEMENTOR — CUSTOM FONT REGISTRATION
      Registers Playfair Display, DM Sans, and JetBrains Mono
      inside Elementor's font picker so you can select them
      in Global Settings > Typography without typing manually.
   ============================================================ */

add_action('elementor/fonts/additional_fonts', 'lynzaddy_elementor_fonts');

function lynzaddy_elementor_fonts($additional_fonts)
{
    $additional_fonts['Playfair Display'] = 'googlefonts';
    $additional_fonts['DM Sans']          = 'googlefonts';
    $additional_fonts['JetBrains Mono']   = 'googlefonts';
    return $additional_fonts;
}

/* ============================================================
   9. OPTIONAL UTILITIES
   ============================================================ */

/**
 * Remove Elementor's default hello-elementor styles that can
 * conflict with our child theme resets (safe to enable):
 */
// add_filter( 'hello_elementor_enqueue_style', '__return_false' );


/**
 * Disable comments if you don't need them:
 */
// add_filter( 'comments_open', '__return_false', 20, 2 );
// add_filter( 'pings_open', '__return_false', 20, 2 );