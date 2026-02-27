<?php

namespace App\Http\Controllers;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;



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

        $status = $request->input('action') === 'Published' ? 'published' : 'draft';
        $blog = Blog::create([
            'user_id' => Auth::id(),
            'thumbnail' => $thumbnailPath,
            'tags' => $request->input('tags', []),
            'blog_title' => $request->input('title'),
            'blog_content' => $request->input('content'),
            'blog_status' => $status,
        ]);

        $message = $status === 'draft' ? 'Draft Saved Successfully' : 'Blog created successfully!';
        return redirect()->back()->with('success', $message);
    }
    public function showHome()
    {
        $latestBlog = Blog::where('blog_status', 'published')->latest()->first();
        return view('main', compact('latestBlog'));
    }
    // AJAX image upload for editor
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // max 5MB
        ]);

        $file = $request->file('image');
        $filename = uniqid('blog_', true) . '.webp';
        $galleryDir = storage_path('app/public/blog_gallery');
        if (!file_exists($galleryDir)) {
            mkdir($galleryDir, 0777, true);
        }
        $image = Image::read($file);
        $image->toWebp(90)->save($galleryDir . '/' . $filename);
        $url = asset('storage/blog_gallery/' . $filename);

        return response()->json(['url' => $url, 'filename' => $filename]);
    }
    public function removeImage(Request $request)
    {
        $request->validate([
            'filename' => 'required|string'
        ]);
        $filePath = 'blog_gallery/' . $request->input('filename');
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'File not found.'], 404);
    }
}
