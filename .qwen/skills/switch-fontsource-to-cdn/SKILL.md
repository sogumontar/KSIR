---
name: switch-fontsource-to-cdn
description: Switch a font (Material Symbols) from local @fontsource npm package to Google Fonts CDN in a Laravel Vite project
source: auto-skill
extracted_at: '2026-06-10T11:26:22.046Z'
---

## Switching from @fontsource to Google Fonts CDN

When a font loaded via `@fontsource` npm package needs to be moved to a CDN instead.

### Steps

1. **Remove the `@import` from the main CSS file** (`resources/css/app.css`):
   - Replace `@import '@fontsource-variable/<name>';` with a comment indicating CDN loading.

2. **Add the CDN `<link>` tag** in the `<head>` of every layout file that uses the font (e.g. `resources/views/components/layouts/*.blade.php`):
   - Place it before `@vite(['resources/css/app.css'])` so the font loads early.

3. **Remove the npm dependency** from `package.json`.

4. **Uninstall and rebuild:**
   ```bash
   npm uninstall <package-name> && npm run build
   ```

### Key points
- The CSS class (e.g. `.material-symbols-outlined`) that sets `font-family` and `font-variation-settings` stays unchanged — it works identically with CDN-loaded fonts.
- The CDN `<link>` goes in the layout `<head>`, not in CSS `@import`, because Google Fonts serves different formats per browser and `@import` from a CDN CSS file can't adapt.
- The `@fontsource/public-sans` imports for the body font are left untouched since those remain self-hosted.

### Example: Material Symbols

CDN link:
```html
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
```

CSS class (unchanged):
```css
.material-symbols-outlined {
    font-family: 'Material Symbols Outlined Variable';
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    display: inline-block;
    vertical-align: middle;
}
```