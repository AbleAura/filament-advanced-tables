<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    | The model to use for user views ownership.
    */
    'user_model' => \App\Models\User::class,

    /*
    |--------------------------------------------------------------------------
    | Authentication Guard
    |--------------------------------------------------------------------------
    */
    'auth_guard' => 'web',

    /*
    |--------------------------------------------------------------------------
    | Approval System
    |--------------------------------------------------------------------------
    | When enabled, User Views must be approved by an admin before they
    | appear for other users (public views only).
    */
    'approval_required' => false,

    /*
    |--------------------------------------------------------------------------
    | Allow Public Views
    |--------------------------------------------------------------------------
    | Allow users to mark their views as public so other users can use them.
    */
    'allow_public_views' => true,

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    | The favorites bar theme. Options:
    | filament | github | links | links-simple | tabs | tabs-simple
    */
    'theme' => 'filament',

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    | Enable or disable individual features of the plugin.
    */
    'features' => [
        'user_views'             => true,
        'preset_views'           => true,
        'favorites_bar'          => true,
        'quick_save'             => true,
        'view_manager'           => true,
        'managed_default_views'  => true,
        'multi_sort'             => true,
        'quick_filters'          => true,
        'advanced_search'        => true,
        'advanced_filter_builder' => true,
        'user_views_resource'    => true,
        'loading_skeleton'       => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Advanced Search Operators
    |--------------------------------------------------------------------------
    */
    'search_operators' => [
        'contains'      => 'Contains',
        'starts_with'   => 'Starts with',
        'ends_with'     => 'Ends with',
        'equals'        => 'Equals',
        'not_contains'  => 'Does not contain',
    ],

    /*
    |--------------------------------------------------------------------------
    | Multi-Tenancy
    |--------------------------------------------------------------------------
    | Configure multi-tenancy support. Set 'enabled' to true and define
    | the tenant relationship on the UserView model.
    */
    'tenancy' => [
        'enabled' => false,
        'model'   => null,
        'column'  => 'tenant_id',
    ],
];
