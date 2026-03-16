<?php

namespace Ableaura\FilamentAdvancedTables\Policies;

use Ableaura\FilamentAdvancedTables\Models\UserView;
use Illuminate\Foundation\Auth\User;

class UserViewPolicy
{
    /**
     * Admins can see all views. Users can see their own + public approved ones.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, UserView $view): bool
    {
        return $view->user_id === $user->id
            || ($view->is_public && $view->is_approved)
            || $view->is_global_favorite
            || $user->can('viewAny', UserView::class);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, UserView $view): bool
    {
        return $view->user_id === $user->id;
    }

    public function delete(User $user, UserView $view): bool
    {
        return $view->user_id === $user->id;
    }

    public function restore(User $user, UserView $view): bool
    {
        return $view->user_id === $user->id;
    }

    public function forceDelete(User $user, UserView $view): bool
    {
        return false; // Only via artisan command
    }

    /**
     * Admins only — approve a public view.
     */
    public function approve(User $user, UserView $view): bool
    {
        return $user->can('viewAny', UserView::class);
    }

    /**
     * Admins only — make a view a global favorite.
     */
    public function makeGlobalFavorite(User $user, UserView $view): bool
    {
        return $user->can('viewAny', UserView::class);
    }
}
