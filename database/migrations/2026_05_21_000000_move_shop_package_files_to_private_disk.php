<?php

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * The base path of the package files, relative to the disk root.
     */
    private const BASE_PATH = 'shop/packages';

    /**
     * Move existing package files from the web-accessible default disk
     * to the private "local" disk.
     */
    public function up(): void
    {
        if (config('filesystems.default') === 'local') {
            return; // Files are already stored on the private disk.
        }

        $this->moveFiles(Storage::disk(), Storage::disk('local'));
    }

    /**
     * Reverse the migration: move the files back to the default disk.
     */
    public function down(): void
    {
        if (config('filesystems.default') === 'local') {
            return;
        }

        $this->moveFiles(Storage::disk('local'), Storage::disk());
    }

    private function moveFiles(Filesystem $from, Filesystem $to): void
    {
        if (! $from->exists(self::BASE_PATH)) {
            return;
        }

        foreach ($from->files(self::BASE_PATH) as $file) {
            if (! $to->exists($file)) {
                $stream = $from->readStream($file);
                $to->writeStream($file, $stream);

                if (is_resource($stream)) {
                    fclose($stream);
                }
            }

            $from->delete($file);
        }
    }
};
