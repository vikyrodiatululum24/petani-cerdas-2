<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

trait DeletesPublicFiles
{
    public static function bootDeletesPublicFiles(): void
    {
        static::updating(function (Model $model): void {
            foreach ($model->publicFileColumns() as $column) {
                if (! $model->isDirty($column)) {
                    continue;
                }

                $oldPath = (string) ($model->getOriginal($column) ?? '');
                $newPath = (string) ($model->{$column} ?? '');

                if ($oldPath !== '' && $oldPath !== $newPath) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        });

        static::deleting(function (Model $model): void {
            foreach ($model->publicFileColumns() as $column) {
                $path = (string) ($model->{$column} ?? '');

                if ($path !== '') {
                    Storage::disk('public')->delete($path);
                }
            }
        });
    }

    private function publicFileColumns(): array
    {
        $constant = static::class.'::PUBLIC_FILE_COLUMNS';

        return defined($constant)
            ? constant($constant)
            : [];
    }
}
