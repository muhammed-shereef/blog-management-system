<!DOCTYPE html>
<html lang="en">
<head>
    <title>Blogs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom CSS for consistent image size */
        .card-img-top {
            width: 100%; /* Full width */
            height: 200px; /* Fixed height */
            object-fit: cover; /* Crop and fit image */
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Blogs</h1>
        <div class="row">
            @foreach($blogs as $blog)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <!-- Blog Image -->
                        @if($blog->image_url)
                            <img src="{{ $blog->image_url }}" class="card-img-top" alt="{{ $blog->title }}">
                        @else
                            <img src="https://via.placeholder.com/150" class="card-img-top" alt="No Image">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $blog->title }}</h5>
                            <p class="card-text">{{ $blog->description }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
