---
name: tailwind-v4-to-v3-migration
description: Pattern for migrating from Tailwind CSS v4 (CSS-first @theme config) to v3 (JS config + postcss) with @fontsource font bundling in a Laravel + Vite project
source: auto-skill
extracted_at: '2026-06-09T05:30:55.364Z'
---

# Migrating Tailwind v4 to v3 in Laravel + Vite

This project went from Tailwind v4 (`@tailwindcss/vite` plugin + `@theme` CSS directives) to v3 (`postcss` + `tailwind.config.js` + `@tailwind` directives) because the user preferred the traditional v3 setup.

## Key lessons from this migration

### 1. Package swap
```bash
npm uninstall tailwindcss @tailwindcss/vite
npm install -D tailwindcss@^3 postcss autoprefixer @tailwindcss/forms @tailwindcss/container-queries
```
- `@tailwindcss/vite` is a v4-only plugin — must remove it from `vite.config.js`
- v3 plugins (`forms`, `container-queries`) are `require()` in `tailwind.config.js`, NOT CSS `@plugin` imports

### 2. Create postcss.config.js (v3 requires PostCSS)
```js
export default {
  plugins: {
    tailwindcss: {},
    autoprefixer: {},
  },
}
```

### 3. Create tailwind.config.js with all design tokens
- Move all tokens from the v4 `@theme {}` block and inline `<script>` config into `tailwind.config.js`
- Content paths must include blade files: `"./resources/**/*.blade.php"`
- Plugins use `require()`: `plugins: [require("@tailwindcss/forms"), require("@tailwindcss/container-queries")]`

### 4. Update vite.config.js
- Remove `import tailwindcss from '@tailwindcss/vite'` and the `tailwindcss()` plugin
- Remove `import { bunny } from 'laravel-vite-plugin/fonts'` and font config (fonts handled by @fontsource now)
- Keep only `laravel-vite-plugin` with input array

### 5. Rewrite app.css
- Replace `@import 'tailwindcss'` with `@tailwind base; @tailwind components; @tailwind utilities;`
- Replace `@theme {}` block — all values now in `tailwind.config.js`
- **@import must come before @tailwind directives** per CSS spec — font imports go first:
```css
@import '@fontsource/public-sans/400.css';
@import '@fontsource/public-sans/600.css';
@import '@fontsource-variable/material-symbols-outlined';

@tailwind base;
@tailwind components;
@tailwind utilities;
```

### 6. Use @fontsource packages instead of manual font downloads
- `npm install @fontsource/public-sans @fontsource-variable/material-symbols-outlined`
- Import CSS directly in `app.css` — Vite resolves `url(./files/...)` references and bundles the .woff2 files
- No need to manually download font files to `public/fonts/`
- **Important**: fontsource variable packages use different font-family names. E.g., Material Symbols uses `'Material Symbols Outlined Variable'` not `'Material Symbols Outlined'`. Update your CSS class accordingly.

### 7. Clean blade layouts
- Remove all CDN `<link>` and `<script>` tags
- Remove inline `<script id="tailwind-config">` blocks (now in tailwind.config.js)
- Remove duplicate `<style>` blocks (now in app.css)
- Add `@vite(['resources/css/app.css'])` directive
- Keep `@livewireStyles` and `@livewireScripts`

### 8. Build verification
- Run `npm run build` after every major change
- Watch for `@import must precede all other statements` warnings — fix by reordering imports before @tailwind directives
- Verify font-family names match between @fontsource imports and your CSS usage