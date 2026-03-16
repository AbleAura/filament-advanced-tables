<?php

namespace Ableaura\FilamentAdvancedTables\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakePresetViewCommand extends Command
{
    protected $signature = 'advanced-tables:make-preset-view
                            {name : The preset view class name}
                            {--resource= : The Filament resource to add the preset view to}';

    protected $description = 'Scaffold a new preset view for a Filament resource';

    public function handle(): int
    {
        $name     = $this->argument('name');
        $resource = $this->option('resource');

        $stub = $this->buildStub($name);

        $path = app_path("Filament/Tables/PresetViews/{$name}.php");

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        if (file_exists($path)) {
            $this->error("Preset view [{$name}] already exists.");
            return self::FAILURE;
        }

        file_put_contents($path, $stub);

        $this->info("Preset view [{$name}] created successfully at [{$path}].");

        if ($resource) {
            $this->line('');
            $this->line("Add the following to your <comment>{$resource}</comment> resource:");
            $this->line('');
            $this->line("  use App\\Filament\\Tables\\PresetViews\\{$name};");
            $this->line('');
            $this->line('  protected function getPresetViews(): array');
            $this->line('  {');
            $this->line('      return [');
            $this->line("          {$name}::make(),");
            $this->line('      ];');
            $this->line('  }');
        }

        return self::SUCCESS;
    }

    private function buildStub(string $name): string
    {
        $key = Str::snake($name);

        return <<<PHP
<?php

namespace App\Filament\Tables\PresetViews;

use Ableaura\FilamentAdvancedTables\Support\PresetView;

class {$name}
{
    public static function make(): PresetView
    {
        return PresetView::make('{$key}')
            ->label('{$name}')
            ->icon('heroicon-o-funnel')
            ->filters([
                // 'status' => ['value' => 'active'],
            ])
            ->sortBy('created_at', 'desc')
            ->toggleColumns([
                // 'email',
            ]);
    }
}
PHP;
    }
}
