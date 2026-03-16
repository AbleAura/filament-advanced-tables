<?php

namespace Ableaura\FilamentAdvancedTables\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserView extends Model
{
    use SoftDeletes;

    protected $table = 'advanced_table_user_views';

    protected $fillable = [
        'user_id',
        'resource',
        'name',
        'state',
        'is_favorite',
        'is_global_favorite',
        'is_public',
        'is_default',
        'is_approved',
        'icon',
        'color',
    ];

    protected $casts = [
        'state'              => 'array',
        'is_favorite'        => 'boolean',
        'is_global_favorite' => 'boolean',
        'is_public'          => 'boolean',
        'is_default'         => 'boolean',
        'is_approved'        => 'boolean',
    ];

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        $model = config('filament-advanced-tables.user_model', \App\Models\User::class);
        return $this->belongsTo($model);
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopeForResource($query, string $resource)
    {
        return $query->where('resource', $resource);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true)->approved();
    }

    public function scopeGlobalFavorites($query)
    {
        return $query->where('is_global_favorite', true);
    }

    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true)
            ->orWhere('is_global_favorite', true);
    }

    // ─── Helpers ───────────────────────────────────────────────────────────────

    public function getFilters(): array
    {
        return $this->state['filters'] ?? [];
    }

    public function getToggledColumns(): array
    {
        return $this->state['toggled_columns'] ?? [];
    }

    public function getSortColumn(): ?string
    {
        return $this->state['sort_column'] ?? null;
    }

    public function getSortDirection(): string
    {
        return $this->state['sort_direction'] ?? 'asc';
    }

    public function getSearch(): string
    {
        return $this->state['search'] ?? '';
    }

    public function getColumnOrder(): array
    {
        return $this->state['column_order'] ?? [];
    }
}
