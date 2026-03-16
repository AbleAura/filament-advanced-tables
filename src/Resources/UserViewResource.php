<?php

namespace Ableaura\FilamentAdvancedTables\Resources;

use Ableaura\FilamentAdvancedTables\Models\UserView;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserViewResource extends Resource
{
    protected static ?string $model = UserView::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'User Views';

    protected static ?int $navigationSort = 90;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('resource')
                    ->disabled(),

                Forms\Components\Toggle::make('is_approved')
                    ->label('Approved')
                    ->helperText('Approve this view to make it accessible to the user.'),

                Forms\Components\Toggle::make('is_public')
                    ->label('Public')
                    ->helperText('Allow other users to use this view.'),

                Forms\Components\Toggle::make('is_global_favorite')
                    ->label('Global Favorite')
                    ->helperText('Pin this view for all users.'),

                Forms\Components\Toggle::make('is_default')
                    ->label('Default View')
                    ->helperText('Set as the default view for this resource.'),

                Forms\Components\Select::make('icon')
                    ->label('Icon')
                    ->searchable()
                    ->options(static::getHeroIconOptions()),

                Forms\Components\Select::make('color')
                    ->label('Color')
                    ->options([
                        'gray'    => 'Gray',
                        'red'     => 'Red',
                        'orange'  => 'Orange',
                        'amber'   => 'Amber',
                        'yellow'  => 'Yellow',
                        'lime'    => 'Lime',
                        'green'   => 'Green',
                        'teal'    => 'Teal',
                        'cyan'    => 'Cyan',
                        'sky'     => 'Sky',
                        'blue'    => 'Blue',
                        'indigo'  => 'Indigo',
                        'violet'  => 'Violet',
                        'purple'  => 'Purple',
                        'fuchsia' => 'Fuchsia',
                        'pink'    => 'Pink',
                        'rose'    => 'Rose',
                    ]),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('resource')
                    ->label('Resource')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_approved')
                    ->label('Approved')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_public')
                    ->label('Public')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_global_favorite')
                    ->label('Global Fav')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved')->label('Approved'),
                Tables\Filters\TernaryFilter::make('is_public')->label('Public'),
                Tables\Filters\TernaryFilter::make('is_global_favorite')->label('Global Favorites'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->hidden(fn (UserView $record) => $record->is_approved)
                    ->action(fn (UserView $record) => $record->update(['is_approved' => true])),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->hidden(fn (UserView $record) => ! $record->is_approved)
                    ->action(fn (UserView $record) => $record->update(['is_approved' => false])),

                Tables\Actions\Action::make('makeGlobalFavorite')
                    ->label('Make Global')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->hidden(fn (UserView $record) => $record->is_global_favorite)
                    ->action(fn (UserView $record) => $record->update(['is_global_favorite' => true])),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('approve')
                    ->label('Approve Selected')
                    ->icon('heroicon-o-check-circle')
                    ->action(fn ($records) => $records->each->update(['is_approved' => true])),

                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserViews::route('/'),
            'edit'  => Pages\EditUserView::route('/{record}/edit'),
        ];
    }

    private static function getHeroIconOptions(): array
    {
        return collect([
            'heroicon-o-table-cells', 'heroicon-o-funnel', 'heroicon-o-star',
            'heroicon-o-bookmark', 'heroicon-o-heart', 'heroicon-o-eye',
            'heroicon-o-check-circle', 'heroicon-o-clock', 'heroicon-o-flag',
            'heroicon-o-tag', 'heroicon-o-user', 'heroicon-o-users',
            'heroicon-o-chart-bar', 'heroicon-o-chart-pie', 'heroicon-o-archive-box',
        ])->mapWithKeys(fn ($icon) => [$icon => str($icon)->after('heroicon-o-')->replace('-', ' ')->title()->toString()])
          ->toArray();
    }
}
