<?php

namespace App\Http\Controllers;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Log;


class BlogController extends Controller
{
    public function store(Request $request)
    {
        // Sanitize tags: keep only non-empty strings
        $tags = array_filter(
            $request->input('tags', []),
            fn($tag) => is_string($tag) && trim($tag) !== ''
        );

        $request->merge(['tags' => array_values($tags)]); // reindex for validation

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = time() . '.webp';
            $thumbnailDir = storage_path('app/public/thumbnails');
            if (!file_exists($thumbnailDir)) {
                mkdir($thumbnailDir, 0777, true);
            }
            $image = Image::read($file);
            $image->toWebp(90)->save($thumbnailDir . '/' . $filename);
            $thumbnailPath = 'storage/thumbnails/' . $filename;
        }

        $blog = Blog::create([
            'user_id' => Auth::id(),
            'thumbnail' => $thumbnailPath,
            'tags' => $request->input('tags', []),
            'blog_title' => $request->input('title'),
            'blog_content' => $request->input('content'),
        ]);

        return redirect()->back()->with('success', 'Blog created successfully!');
    }

}
