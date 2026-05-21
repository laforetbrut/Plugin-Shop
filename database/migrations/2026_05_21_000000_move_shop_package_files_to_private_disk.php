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
     * Move existing package files from the web-accessible "public" disk
     * to the private "local" disk.
     */
    public function up(): void
    {
        $this->moveFiles(Storage::disk('public'), Storage::disk('local'));
    }

    /**
     * Reverse the migration: move the files back to the "public" disk.
     */
    public function down(): void
    {
        $this->moveFiles(Storage::disk('local'), Storage::disk('public'));
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
