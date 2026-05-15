# MB Divi Integration — Agent Guide

## Project

WordPress plugin bridging [Meta Box](https://metabox.io) custom fields with Divi Builder via dynamic content and two custom Divi modules (Meta Box Field, Meta Box Cloneable).

## Key structure

- `mb-divi-integrator.php` — entrypoint; requires `vendor/autoload.php`, instantiates `MBDI\Main`
- `src/` — PSR-4 namespace `MBDI\`; main PHP source (Main.php, Extension.php, FieldQuery.php, Output.php, Templates/)
- `includes/` — **generated Divi module files** (PHP + JSX). These are built by `divi-scripts`. Do not hand-edit JSX here; edit via `divi-scripts` (see below).
- `scripts/`, `styles/` — build artifacts (minified bundles). Do not hand-edit.

## Build (JS assets)

- `divi-scripts` 1.0.3 (CRA-like wrapper), requires **Node.js 14.x exactly** (higher versions break).
- Use `nvm use 14` / `fnm use 14` before running npm commands.
- `npm run build` — production build (writes to `scripts/`, `styles/`, `includes/`).
- `npm run start` — watch mode for development.
- `npm run zip` — build then zip for distribution.

## PHP

- Lint: `vendor/bin/phpcs` — checks `src/` only (config: `phpcs.xml`). Uses WordPress + PHPCompatibilityWP rules.
- No test framework found in repo.

## Release

- Tagged pushes trigger `.github/workflows/release.yml` which delegates to `wpmetabox/meta-box`'s WordPress.org SVN deploy workflow.
- `.distignore` controls what ships to WordPress.org (excludes dev files, images, composer/package manifests, README.md).

## Conventions

- PHP follows PSR-4 autoloading, but code style is WordPress with several exclusions (short arrays allowed, no yoda, minimal PHPDoc).
- `mbdi` textdomain for translations.
