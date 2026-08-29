<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BlogPostController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Blog Posts List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {

        $search = $request->input('search');

        $posts = Post::query()
            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('excerpt', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.blog-posts.index', [
            'posts' => $posts,
            'search' => $search,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Create Blog Post
    |--------------------------------------------------------------------------
    */

    public function create()
    {

        return view('admin.blog-posts.create');
    }

    /*
    |--------------------------------------------------------------------------
    | Store Blog Post
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('site.posts', 'slug'),
            ],

            'content' => [
                'required',
                'string',
            ],

            'excerpt' => [
                'nullable',
                'string',
            ],

            'meta_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'meta_description' => [
                'nullable',
                'string',
                'max:255',
            ],

            'featured_image' => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:5120',
            ],

            'is_published' => [
                'nullable',
                'boolean',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Featured Image
        |--------------------------------------------------------------------------
        */

        $featuredImage = null;

        if ($request->hasFile('featured_image')) {

            $file = $request->file('featured_image');

            $filename =
                'blog-'.
                time().
                '-'.
                Str::random(10).
                '.'.
                $file->getClientOriginalExtension();

            $file->storeAs(
                'blog',
                $filename,
                'public'
            );

            $featuredImage = $filename;
        }

        /*
        |--------------------------------------------------------------------------
        | Publication
        |--------------------------------------------------------------------------
        */

        $isPublished = $request->boolean('is_published');

        $publishedAt = $isPublished
            ? now()
            : null;

        /*
        |--------------------------------------------------------------------------
        | Create Post
        |--------------------------------------------------------------------------
        */

        Post::create([

            'title' => trim($validated['title']),

            'slug' => trim($validated['slug']),

            'content' => $validated['content'],

            'excerpt' => ! empty($validated['excerpt'])
                ? trim($validated['excerpt'])
                : null,

            'meta_title' => ! empty($validated['meta_title'])
                ? trim($validated['meta_title'])
                : null,

            'meta_description' => ! empty($validated['meta_description'])
                ? trim($validated['meta_description'])
                : null,

            'featured_image' => $featuredImage,

            'is_published' => $isPublished,

            'published_at' => $publishedAt,
        ]);

        return redirect()
            ->route('admin.blog-posts.index')
            ->with(
                'success',
                'Blog post created successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Blog Post
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $post = Post::findOrFail($id);

        return view('admin.blog-posts.edit', compact('post'));
    }

    /*
    |--------------------------------------------------------------------------
    | Update Blog Post
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        $id
    ) {

        $post = Post::findOrFail($id);

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
            ],

            'content' => [
                'required',
                'string',
            ],

            'excerpt' => [
                'nullable',
                'string',
            ],

            'meta_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'meta_description' => [
                'nullable',
                'string',
                'max:255',
            ],

            'featured_image' => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:5120',
            ],

            'is_published' => [
                'nullable',
                'boolean',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Featured Image
        |--------------------------------------------------------------------------
        */

        $featuredImage = $post->featured_image;

        if ($request->hasFile('featured_image')) {

            /*
             * Delete old image
             */
            if (
                $post->featured_image &&
                Storage::disk('public')->exists(
                    'blog/'.$post->featured_image
                )
            ) {
                Storage::disk('public')->delete(
                    'blog/'.$post->featured_image
                );
            }

            $file = $request->file('featured_image');

            $filename =
                'blog-'.
                time().
                '-'.
                Str::random(10).
                '.'.
                $file->getClientOriginalExtension();

            $file->storeAs(
                'blog',
                $filename,
                'public'
            );

            $featuredImage = $filename;
        }

        /*
        |--------------------------------------------------------------------------
        | Publication
        |--------------------------------------------------------------------------
        */

        $isPublished = $request->boolean('is_published');

        /*
         * If publishing for the first time,
         * set published_at.
         *
         * If already published, keep the
         * existing published date.
         */
        if ($isPublished) {

            $publishedAt = $post->published_at ?? now();
        } else {

            $publishedAt = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Update Post
        |--------------------------------------------------------------------------
        */

        $post->update([

            'title' => trim($validated['title']),

            'slug' => trim($validated['slug']),

            'content' => $validated['content'],

            'excerpt' => ! empty($validated['excerpt'])
                ? trim($validated['excerpt'])
                : null,

            'meta_title' => ! empty($validated['meta_title'])
                ? trim($validated['meta_title'])
                : null,

            'meta_description' => ! empty($validated['meta_description'])
                ? trim($validated['meta_description'])
                : null,

            'featured_image' => $featuredImage,

            'is_published' => $isPublished,

            'published_at' => $publishedAt,
        ]);

        return redirect()
            ->route('admin.blog-posts.index')
            ->with(
                'success',
                'Blog post updated successfully.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Toggle Publish
    |--------------------------------------------------------------------------
    */

    public function togglePublish($id)
    {
        $post = Post::findOrFail($id);
        if ($post->is_published) {

            $post->update([
                'is_published' => false,
                'published_at' => null,
            ]);

            $message = 'Blog post unpublished successfully.';
        } else {

            $post->update([
                'is_published' => true,
                'published_at' => $post->published_at ?? now(),
            ]);

            $message = 'Blog post published successfully.';
        }

        return redirect()
            ->route('admin.blog-posts.index')
            ->with(
                'success',
                $message
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Blog Post
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        /*
         * Delete featured image
         */
        if (
            $post->featured_image &&
            Storage::disk('public')->exists(
                'blog/'.$post->featured_image
            )
        ) {
            Storage::disk('public')->delete(
                'blog/'.$post->featured_image
            );
        }

        $post->delete();

        return redirect()
            ->route('admin.blog-posts.index')
            ->with(
                'success',
                'Blog post deleted successfully.'
            );
    }
}
