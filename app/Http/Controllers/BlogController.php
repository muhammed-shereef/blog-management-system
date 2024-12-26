<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class BlogController extends Controller
{

    public function list()
    {
        $blogs = Blog::where('status', 1)->get()->map(function ($blog) {
            $blog->image_url = asset('storage/' . $blog->image);
            return $blog;
        });

        return view('blogs.index', compact('blogs'));
    }

    public function index()
    {
        $blogs = Blog::all()->map(function ($blog) {
            $blog->image_url = $blog->image ? asset('storage/' . $blog->image) : null;
            return $blog;
        });

        return view('admin.index', compact('blogs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'nullable|in:0,1',
        ]);

        $blog = new Blog();
        $blog->title = $request->title;
        $blog->description = $request->description;
        $blog->content = $request->content;
        $blog->status = $request->status;

        if ($request->hasFile('image')) {
            $imagePath = $request->image->store('images', 'public');
            $blog->image = $imagePath;
        }

        $blog->save();

        $blog->image_url = asset('storage/' . $blog->image);
        return response()->json($blog);
    }

    public function edit($id)
    {
        $blog = Blog::findOrFail($id);
        return response()->json($blog);
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'content'     => 'required|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status'      => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images', 'public');
        }

        $blog->update($data);

        $blog->image_url = asset('storage/' . $blog->image);
        return response()->json($blog);
    }

    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);

        if ($blog->image) {
            Storage::disk('public')->delete($blog->image);
        }

        $blog->delete();

        return response()->json(['success' => 'Blog and image deleted successfully']);
    }
}
