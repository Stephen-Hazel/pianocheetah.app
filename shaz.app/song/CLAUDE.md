# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

# Workflow Rules
- **PLAN FIRST:** Before modifying any code, you must create a detailed implementation plan (`PLAN.md` or within the chat).
- **NO CODING:** Do not generate code, edit files, or run commands until I approve your plan.
- **STRUCTURE:** Plans must include: 1) Files to be edited, 2) Specific changes, 3) Potential risks, 4) Testing steps [1, 5, 11].
- **CLAUDE CODE:** If a task involves multiple files or ambiguity, initiate Plan Mode (Shift+Tab) [1, 14].

# code formatting
- space between function name and (
- space between array name and [

## What this is

A PHP/jQuery web app that plays MP3s via Google Cast (Chromecast). Files live under `song/song/<dir>/` on the server at `https://shaz.app/song/`.

## Key files

- `index.php` — main page: builds playlist, renders UI, handles Cast SDK integration
- `did.php` — AJAX endpoint; appends a finished song to `did.txt`
- `skip.php` — AJAX endpoint; appends a skipped song to `skip.txt`
- `did.txt` — newline-separated list of already-played songs (used to avoid repeats in shuffle mode); deleted when all songs have been played

## App framework (`../_inc/app.php`)

All PHP pages `require_once ("../_inc/app.php")`. Key helpers:

- `arg($k, $def='')` — safe `$_REQUEST` getter
- `Get($fn)` / `Put($fn, $s)` / `App($fn, $s)` — file read/write/append
- `LstDir($path, 'd'|'f')` — list dirs or files in a path
- `pg_head($title, $css, $js)` / `pg_body($nav)` / `pg_foot()` — page scaffolding; CSS/JS prefix `jqui ` auto-loads jQuery UI
- `table1($id, $hdr, $rows)` — single-column table; rows are HTML strings
- `dbg($s)` — appends timestamped line to `dbg.txt` (debug log)

## JavaScript (`../_js/app.js`)

Shared app.js provides: `dbg` (alias for `console.log`), `mobl()` (true if viewport < 700px), `init()` (called in `$(function(){init();})` to boot nav/tips/etc).

## Playlist logic

- Songs are in `song/song/<dir>/<title>.mp3`; filename format: `artist-extra-title.mp3` (underscores = spaces)
- `$pick` = array of dir indices selected via checkboxes; `$shuf` = Y/N
- In shuffle mode, `did.txt` excludes already-played tracks; when a dir runs out, `did.txt` is deleted and the page redirects
- Playlist is interleaved across picked dirs (round-robin by index)

## Google Cast integration

Uses the Cast Sender SDK loaded via `cast_sender.js?loadCastFramework=1`, which provides both `chrome.cast.*` (legacy) and `cast.framework.*` (CAF) APIs. The app queues up to 50 tracks at a time via `QueueLoadRequest`. Note: `cSess.getSessionObj().queueLoad()` is a deprecated legacy pattern — the CAF equivalent would use the session's queue API directly.

## Song directory layout

```
song/song/
  <dir>/        ← music category dirs (e.g. An, St)
  _z/           ← "scooted" (archived) songs land here
```

The `sc` query param triggers a scoot: moves `song/<dir>/<file>` → `song/_z/<file>`.
