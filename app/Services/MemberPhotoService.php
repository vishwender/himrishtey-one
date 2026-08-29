<?php

namespace App\Services;

use App\Jobs\ProcessMemberPhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class MemberPhotoService
{
    protected string $disk = 'public';

    /**
     * Store a member photo.
     */
    public function upload(
        int $memberId,
        UploadedFile $file,
        bool $setAsProfile = false
    ): object {

        $this->validateImage($file);

        $extension = strtolower(
            $file->getClientOriginalExtension()
        );

        $filename = Str::uuid()->toString().'.'.$extension;

        $directory = "members/{$memberId}";

        /*
    |--------------------------------------------------------------------------
    | Store original
    |--------------------------------------------------------------------------
    */

        $originalPath = $file->storeAs(
            "{$directory}/original",
            $filename,
            $this->disk
        );

        /*
    |--------------------------------------------------------------------------
    | Create gallery record
    |--------------------------------------------------------------------------
    */

        $photoId = DB::connection('site')
            ->table('member_photos')
            ->insertGetId([
                'member_id' => $memberId,
                'photo' => $originalPath,
                'photo_approved' => 'No',
                'photo_privacy' => 1,
            ]);

        /*
    |--------------------------------------------------------------------------
    | Set as profile photo
    |--------------------------------------------------------------------------
    */

        if ($setAsProfile) {

            DB::connection('site')
                ->table('members')
                ->where('id', $memberId)
                ->update([
                    'photo' => $originalPath,
                ]);
        }

        /*
    |--------------------------------------------------------------------------
    | Process image asynchronously
    |--------------------------------------------------------------------------
    */

        ProcessMemberPhoto::dispatch(
            $originalPath
        );

        /*
    |--------------------------------------------------------------------------
    | Return created photo
    |--------------------------------------------------------------------------
    */

        return DB::connection('site')
            ->table('member_photos')
            ->where('id', $photoId)
            ->first();
    }

    /**
     * Set an existing gallery photo as profile photo.
     */
    public function setAsProfile(
        int $memberId,
        int $photoId
    ): void {

        $photo = DB::connection('site')
            ->table('member_photos')
            ->where('id', $photoId)
            ->where('member_id', $memberId)
            ->first();

        if (! $photo) {
            throw new RuntimeException('Photo not found.');
        }

        DB::connection('site')
            ->table('members')
            ->where('id', $memberId)
            ->update([
                'photo' => $photo->photo,
            ]);
    }

    /**
     * Approve a gallery photo.
     */
    public function approve(
        int $memberId,
        int $photoId
    ): void {

        $updated = DB::connection('site')
            ->table('member_photos')
            ->where('id', $photoId)
            ->where('member_id', $memberId)
            ->update([
                'photo_approved' => 'Yes',
            ]);

        if (! $updated) {
            throw new RuntimeException('Photo not found.');
        }
    }

    /**
     * Reject / unapprove a gallery photo.
     */
    public function unapprove(
        int $memberId,
        int $photoId
    ): void {

        $updated = DB::connection('site')
            ->table('member_photos')
            ->where('id', $photoId)
            ->where('member_id', $memberId)
            ->update([
                'photo_approved' => 'No',
            ]);

        if (! $updated) {
            throw new RuntimeException('Photo not found.');
        }
    }

    /**
     * Delete a gallery photo.
     */
    public function delete(
        int $memberId,
        int $photoId
    ): void {

        $db = DB::connection('site');

        $photo = $db->table('member_photos')
            ->where('id', $photoId)
            ->where('member_id', $memberId)
            ->first();

        if (! $photo) {
            throw new RuntimeException('Photo not found.');
        }

        /*
        |--------------------------------------------------------------------------
        | Don't delete the physical file if it is currently
        | being used as the member's profile photo.
        |--------------------------------------------------------------------------
        */

        $member = $db->table('members')
            ->select('photo')
            ->where('id', $memberId)
            ->first();

        $isProfilePhoto =
            $member &&
            $member->photo === $photo->photo;

        /*
        |--------------------------------------------------------------------------
        | Delete database record
        |--------------------------------------------------------------------------
        */

        $db->table('member_photos')
            ->where('id', $photoId)
            ->where('member_id', $memberId)
            ->delete();

        /*
        |--------------------------------------------------------------------------
        | Delete physical file
        |--------------------------------------------------------------------------
        */

        if (! $isProfilePhoto) {

            Storage::disk($this->disk)
                ->delete($photo->photo);
        }
    }

    /**
     * Validate uploaded image.
     */
    protected function validateImage(
        UploadedFile $file
    ): void {

        if (! $file->isValid()) {
            throw new RuntimeException(
                'The uploaded image is invalid.'
            );
        }

        $allowedExtensions = [
            'jpg',
            'jpeg',
            'png',
            'webp',
        ];

        $extension = strtolower(
            $file->getClientOriginalExtension()
        );

        if (! in_array($extension, $allowedExtensions, true)) {

            throw new RuntimeException(
                'Only JPG, JPEG, PNG and WebP images are allowed.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 10 MB maximum
        |--------------------------------------------------------------------------
        */

        if ($file->getSize() > 10 * 1024 * 1024) {

            throw new RuntimeException(
                'The image cannot be larger than 10 MB.'
            );
        }
    }

    /**
     * Get the public URL for a member photo.
     *
     * Supports both:
     *
     * OLD:
     * filename.jpg
     *
     * NEW:
     * members/123/original/filename.jpg
     */
    public function url(?string $photo): ?string
    {
        if (empty($photo)) {
            return null;
        }

        /*
    |--------------------------------------------------------------------------
    | New photo structure
    |--------------------------------------------------------------------------
    */

        if (str_starts_with($photo, 'members/')) {
            return Storage::disk('public')->url($photo);
        }

        /*
    |--------------------------------------------------------------------------
    | Existing photo structure
    |--------------------------------------------------------------------------
    |
    | Existing database records contain only the filename.
    | Keep using the existing public/storage convention.
    |
    */

        return asset('storage/'.ltrim($photo, '/'));
    }
}
