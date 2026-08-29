<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MemberPhotoService;
use Illuminate\Http\Request;
use RuntimeException;

class MemberPhotoController extends Controller
{
    public function __construct(
        protected MemberPhotoService $photoService
    ) {}

    /**
     * Upload a gallery photo.
     */
    public function store(Request $request, int $memberId)
    {
        $request->validate([
            'photo' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ],
        ]);

        try {

            $this->photoService->upload(
                $memberId,
                $request->file('photo')
            );

            return back()->with(
                'success',
                'Photo uploaded successfully. Image processing will continue in the background.'
            );
        } catch (RuntimeException $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'photo' => $e->getMessage(),
                ]);
        }
    }

    /**
     * Set gallery photo as profile photo.
     */
    public function setProfile(
        int $memberId,
        int $photoId
    ) {

        try {

            $this->photoService->setAsProfile(
                $memberId,
                $photoId
            );

            return back()->with(
                'success',
                'Profile photo updated successfully.'
            );
        } catch (RuntimeException $e) {

            return back()->withErrors([
                'photo' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Approve photo.
     */
    public function approve(
        int $memberId,
        int $photoId
    ) {

        try {

            $this->photoService->approve(
                $memberId,
                $photoId
            );

            return back()->with(
                'success',
                'Photo approved successfully.'
            );
        } catch (RuntimeException $e) {

            return back()->withErrors([
                'photo' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Unapprove photo.
     */
    public function unapprove(
        int $memberId,
        int $photoId
    ) {

        try {

            $this->photoService->unapprove(
                $memberId,
                $photoId
            );

            return back()->with(
                'success',
                'Photo approval removed.'
            );
        } catch (RuntimeException $e) {

            return back()->withErrors([
                'photo' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Delete gallery photo.
     */
    public function destroy(
        int $memberId,
        int $photoId
    ) {

        try {

            $this->photoService->delete(
                $memberId,
                $photoId
            );

            return back()->with(
                'success',
                'Photo deleted successfully.'
            );
        } catch (RuntimeException $e) {

            return back()->withErrors([
                'photo' => $e->getMessage(),
            ]);
        }
    }
}
