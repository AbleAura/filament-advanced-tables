# Changelog

All notable changes to `ableaura/filament-advanced-tables` will be documented in this file.

---

## v1.0.0 — Initial Release

### Added
- **User Views** — users save filters, sort, toggled columns, column order, and search into named views
- **Preset Views** — developer-defined views deployed via `getPresetViews()` on the list page
- **Favorites Bar** — pin views above the table for one-click access; supports 6 themes
- **Quick Save** — save or update the current view in a single click
- **View Manager** — modal UI to rename, favorite, set-default, and delete views
- **Managed Default Views** — each user can choose a default view per resource
- **Multi-Sort** — sort by multiple columns simultaneously via a dropdown UI
- **Quick Filters** — pre-configured shortcut buttons that instantly apply filter sets
- **Advanced Search** — search across specific columns with operators (contains, starts_with, equals, ends_with, not_contains)
- **Advanced Filter Builder** — build AND/OR multi-condition queries at runtime
- **User Views Resource** — admin panel resource to approve, manage, and pin global favorites
- **Approval System** — optional gate requiring admin approval before public views go live
- **Global Favorites** — admins can pin a view for all users
- **Policy Integration** — full Laravel policy support for view access, creation, and management
- **Loading Skeleton** — animated overlay during Livewire table reloads
- **Multi-Tenancy** — config-driven support for Filament tenancy, Spatie Multi-tenancy, and Tenancy for Laravel
- `MakePresetViewCommand` — scaffold preset view classes via `artisan advanced-tables:make-preset-view`
- `PruneUserViewsCommand` — clean up soft-deleted and unapproved views via `artisan advanced-tables:prune-views`
- Full translation support (`resources/lang/en/advanced-tables.php`)
- Dark mode support across all components
- Filament 3.x and 4.x compatibility
