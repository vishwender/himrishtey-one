<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Imagick;
use ImagickException;

class ProcessMemberPhoto implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Number of attempts.
     */
    public int $tries = 3;

    /**
     * Maximum execution time.
     */
    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $originalPath
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $disk = Storage::disk('public');

        /*
        |--------------------------------------------------------------------------
        | Make sure original exists
        |--------------------------------------------------------------------------
        */

        if (! $disk->exists($this->originalPath)) {
            return;
        }

        $fullPath = $disk->path(
            $this->originalPath
        );

        try {

            $image = new Imagick($fullPath);

            /*
            |--------------------------------------------------------------------------
            | Correct image orientation
            |--------------------------------------------------------------------------
            */

            if (method_exists($image, 'autoOrient')) {

                $image->autoOrient();
            } else {

                $image->autoOrientImage();
            }

            /*
            |--------------------------------------------------------------------------
            | Remove unnecessary metadata
            |--------------------------------------------------------------------------
            */

            $image->stripImage();

            /*
            |--------------------------------------------------------------------------
            | Generate large image
            |--------------------------------------------------------------------------
            */

            $this->createVariant(
                $image,
                'large',
                1200
            );

            /*
            |--------------------------------------------------------------------------
            | Generate medium image
            |--------------------------------------------------------------------------
            */

            $this->createVariant(
                $image,
                'medium',
                600
            );

            /*
            |--------------------------------------------------------------------------
            | Generate thumbnail
            |--------------------------------------------------------------------------
            */

            $this->createVariant(
                $image,
                'thumb',
                250
            );

            /*
            |--------------------------------------------------------------------------
            | Cleanup
            |--------------------------------------------------------------------------
            */

            $image->clear();
            $image->destroy();
        } catch (ImagickException $e) {

            report($e);

            throw $e;
        }
    }

    /**
     * Create a resized WebP variant.
     */
    protected function createVariant(
        Imagick $source,
        string $variant,
        int $width
    ): void {

        $disk = Storage::disk('public');

        /*
        |--------------------------------------------------------------------------
        | Original:
        |
        | members/27692/original/abc123.jpg
        |
        | We need:
        |
        | members/27692/
        |--------------------------------------------------------------------------
        */

        $memberDirectory = dirname(
            dirname($this->originalPath)
        );

        /*
        |--------------------------------------------------------------------------
        | Filename without extension
        |--------------------------------------------------------------------------
        */

        $filename = pathinfo(
            $this->originalPath,
            PATHINFO_FILENAME
        );

        /*
        |--------------------------------------------------------------------------
        | Output path
        |--------------------------------------------------------------------------
        */

        $outputPath =
            "{$memberDirectory}/{$variant}/{$filename}.webp";

        /*
        |--------------------------------------------------------------------------
        | Make directory
        |--------------------------------------------------------------------------
        */

        $disk->makeDirectory(
            dirname($outputPath)
        );

        /*
        |--------------------------------------------------------------------------
        | Clone source
        |--------------------------------------------------------------------------
        */

        $image = clone $source;

        /*
        |--------------------------------------------------------------------------
        | Resize while preserving aspect ratio
        |--------------------------------------------------------------------------
        */

        $image->thumbnailImage(
            $width,
            0
        );

        /*
        |--------------------------------------------------------------------------
        | WebP
        |--------------------------------------------------------------------------
        */

        $image->setImageFormat('webp');

        $image->setImageCompressionQuality(82);

        /*
        |--------------------------------------------------------------------------
        | Write file
        |--------------------------------------------------------------------------
        */

        $image->writeImage(
            $disk->path($outputPath)
        );

        /*
        |--------------------------------------------------------------------------
        | Cleanup
        |--------------------------------------------------------------------------
        */

        $image->clear();
        $image->destroy();
    }
}
