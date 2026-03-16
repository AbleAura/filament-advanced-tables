<?php

namespace Ableaura\FilamentAdvancedTables\Commands;

use Ableaura\FilamentAdvancedTables\Models\UserView;
use Illuminate\Console\Command;

class PruneUserViewsCommand extends Command
{
    protected $signature = 'advanced-tables:prune-views
                            {--days=30 : Delete soft-deleted views older than this many days}
                            {--unapproved : Also delete views that were never approved}
                            {--dry-run : Show what would be deleted without actually deleting}';

    protected $description = 'Prune soft-deleted and stale user views';

    public function handle(): int
    {
        $days        = (int) $this->option('days');
        $unapproved  = $this->option('unapproved');
        $dryRun      = $this->option('dry-run');

        // ── Soft-deleted older than N days ────────────────────────────────────────
        $softDeletedQuery = UserView::onlyTrashed()
            ->where('deleted_at', '<', now()->subDays($days));

        $softDeletedCount = $softDeletedQuery->count();

        if ($dryRun) {
            $this->line("Would permanently delete <comment>{$softDeletedCount}</comment> soft-deleted views older than {$days} days.");
        } else {
            $softDeletedQuery->forceDelete();
            $this->info("Permanently deleted <comment>{$softDeletedCount}</comment> soft-deleted views.");
        }

        // ── Unapproved views older than N days ────────────────────────────────────
        if ($unapproved) {
            $unapprovedQuery = UserView::where('is_approved', false)
                ->where('created_at', '<', now()->subDays($days));

            $unapprovedCount = $unapprovedQuery->count();

            if ($dryRun) {
                $this->line("Would delete <comment>{$unapprovedCount}</comment> unapproved views older than {$days} days.");
            } else {
                $unapprovedQuery->delete();
                $this->info("Deleted <comment>{$unapprovedCount}</comment> unapproved views.");
            }
        }

        $this->info('Pruning complete.');

        return self::SUCCESS;
    }
}
