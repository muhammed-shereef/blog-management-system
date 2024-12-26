<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class BlogController extends Controller
{

    public function list()
{
    // Fetch all blogs with image URL
    $blogs = Blog::all()->map(function ($blog) {
        $blog->image_url = asset('storage/' . $blog->image); // Add full image URL
        return $blog;
    });

    return view('blogs.index', compact('blogs')); // Return blogs to the view
}

    public function index()
    {
        // Retrieve all blogs and append image_url to each blog
        $blogs = Blog::all()->map(function ($blog) {
            $blog->image_url = $blog->image ? asset('storage/' . $blog->image) : null;
            return $blog;
        });

        // Pass blogs with image_url to the view
        return view('admin.index', compact('blogs'));
    }


    public function store(Request $request)
{
    // dd($request);
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
    // $blog->status = $request->has('status') ? 1 : 0;
    $blog->status = $request->status;

    if ($request->hasFile('image')) {
        $imagePath = $request->image->store('images', 'public');
        $blog->image = $imagePath;
    }

    $blog->save();

    // Return the full URL for the image
    // In the store or update method
$blog->image_url = asset('storage/' . $blog->image);  // Ensure you return full URL
return response()->json($blog); // Returning the blog with image_url

}

    public function edit($id)
    {
        $blog = Blog::findOrFail($id);
        return response()->json($blog);  // Return blog data for editing
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
            // Store the image and get the path
            $data['image'] = $request->file('image')->store('images', 'public');
        }

        // Update the blog post with validated data
        $blog->update($data);

        // Add the full image URL to the response
       // In the store or update method
$blog->image_url = asset('storage/' . $blog->image);  // Ensure you return full URL
return response()->json($blog); // Returning the blog with image_url

    }







    public function destroy($id)
    {
        // Find the blog by its ID
        $blog = Blog::findOrFail($id);

        // Delete the image if it exists
        if ($blog->image) {
            // Delete the image from the 'public' disk (storage/app/public)
            Storage::disk('public')->delete($blog->image);
        }

        // Delete the blog record
        $blog->delete();

        // Return success response
        return response()->json(['success' => 'Blog and image deleted successfully']);
    }

}
