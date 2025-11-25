## FMR Automation Guide (AI-Only)

This document instructs autonomous agents how to work inside the FMR theme without upsetting human contributors. Humans should continue to rely on `README.md`.

### Absolute Rules

1. **Use `wp acorn` for all Laravel-style commands**. `php artisan` does nothing in this environment.
2. **Honor PSR-12 + 4-space indentation everywhere** (PHP, JS, SCSS, Blade). Single quotes + semicolons for JS/SCSS.
3. **Never add `dark:` Tailwind classes**. Dark mode is automatic via the theme’s color swapper.
4. **Treat `public/build/` and `vendor/` as read-only**. If assets are needed, run the appropriate npm script instead of editing output.
5. **Do not revert or reformat unrelated files**. Only touch the files required for the task.
6. **All user-facing text must be translatable** via `__('…', 'fmr')` or `wp.i18n.__`.

### Tooling Checklist

- PHP dependencies: `composer install`
- Node deps: `npm install`
- Dev build: `npm run dev`
- Production build: `npm run build`
- Translations: `npm run translate:*`
- Database: `wp acorn migrate`, `wp acorn migrate:rollback`, `wp acorn db:seed`
- Tinker/testing: `wp acorn tinker`

### Architecture Recap

- `app/Core` owns WordPress integration: custom post types, taxonomies, filters, and admin scaffolding.
- `app/Http/Controllers` contains web controllers; `Admin` and `Api` subnamespaces handle dashboards and JSON.
- `app/Models` expose WordPress + custom tables via Eloquent.
- `app/Services` encapsulate domain workflows (exports, anniversaries, field group helpers).
- `app/Livewire` mirrors `resources/views/livewire` for reactive UI.
- `resources/` holds CSS, JS, Blade templates, translations.
- `routes/web.php` and `routes/api.php` are the only place new routes should be registered.
- `database/migrations` and `database/seeders` follow standard Laravel structure but must remain compatible with WordPress core tables.
- `.cursor/rules` enforces style constraints; do not violate them.

### Extending the Backend (Decision Tree)

1. **Need a new custom table or column?**  
   - Scaffold migration via `wp acorn make:migration`.  
   - Edit file under `database/migrations`. Use snake_case table names.  
   - Reference WordPress tables (`posts`, `terms`, etc.) with accurate foreign keys.  
   - Run `wp acorn migrate`.  

2. **Expose new data model?**  
   - Create model in `app/Models`. Extend `Model`, set `$table`, `$primaryKey`, `$timestamps`.  
   - Add scopes for common filters (see `Post::type()` pattern).  

3. **Need reusable business logic?**  
   - Add a service in `app/Services`.  
   - Register bindings (if needed) in `App\Providers\ThemeServiceProvider`.  
   - Keep controllers thin; inject services.

4. **Add/modify admin list or edit screens?**  
   - Extend `App\Core\Admin\Abstracts\EditPage` for CRUD pages or `OptionsPage` for settings.  
   - Compose `FieldGroup`/`OptionsFieldGroup` classes for meta boxes, stored under `app/Core/Admin/FieldGroups`.  
   - For relationships, rely on `RelationHandler` subclasses.  
   - Register new admin modules inside `App\Providers\AdminServiceProvider`.

5. **Need Livewire interactivity?**  
   - `wp acorn make:livewire ComponentName`.  
   - Place PHP class in `app/Livewire`, Blade view in `resources/views/livewire`.  
   - Components should delegate heavy lifting to existing controllers/services.  

6. **Expose routes/APIs?**  
   - Public web: `routes/web.php`, use `General::getRouteSlug()` when referencing slugs used elsewhere.  
   - JSON APIs: `routes/api.php`. All routes must live inside the `ApiKeyMiddleware` group unless explicitly anonymous.  
   - Corresponding controllers reside in `app/Http/Controllers` (web) or `App\Http\Controllers\Api`. Return JSON with `response()->json()`.  

### Admin Abstractions Primer

- **`EditPage`**: Handles submenu registration, nonce verification, publish meta box, and redirect messaging. Child classes must implement `initializeProperties()`, `getCurrentObject()`, `handleSave()`, and success message helpers.
- **`OptionsPage`**: Same as `EditPage` but wired to options storage. Use alongside `OptionsFieldGroup`.
- **`FieldGroup`**: Declarative meta boxes for post types. Provide tabbed layouts, relation dropdowns, and auto-saving via `FieldGroupTrait`.
- **`OptionsFieldGroup`**: Same API as `FieldGroup`; persists via `setting()`/`setSetting()`.
- **`MetaBox`**: Lightweight helper for meta box rendering (see `app/Core/Admin/MetaBoxes`).
- **`RelationHandler`**: Syncs WP posts/terms/pivot tables during `handleSave()`. Call within `EditPage` implementations to keep WP + custom tables consistent.

### Working With Assets

- CSS: Tailwind lives in `resources/css/app.css`; admin SCSS under `resources/css/admin.scss` and `_admin/`.
- JS: `resources/js/app.js` (frontend) and `resources/js/admin.js`. Use Alpine.js; avoid adding heavy dependencies.
- Always run `npm run dev` for local iteration or `npm run build` before shipping. Never commit `public/build` changes without running the proper script.

### Localization Requirements

- PHP/Blade: `__('text', 'fmr')` or `_e`.
- JS: `wp.i18n.__('text', 'fmr')`.
- After adding strings, run translation scripts if requested (`npm run translate:pot`, etc.).

### Testing & Verification

- Prefer `wp acorn tinker` for quick checks.  
- Browser testing should be done against the WordPress instance using Vite dev server (`npm run dev`).  
- When adding exports or Livewire components, confirm they respect feature toggles stored in settings.

### Final Checklist Before Ending a Task

1. Files touched are limited to the needed scope.  
2. Formatting: 4 spaces, PSR-12, no stray `dark:` classes, JS uses single quotes + semicolons.  
3. User-facing strings wrapped in translation helpers.  
4. Migrations/seeds run (if applicable) and no pending schema drift.  
5. Service providers updated for any new bindings.  
6. Summarize changes referencing file paths; cite key code lines when responding to humans.  

Follow this guide and coordinate with the main README as facts change.

