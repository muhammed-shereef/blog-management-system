@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Blog Management</h2>

    <form id="blog-form">
        @csrf
        <input type="hidden" name="blog_id" id="blog_id">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" class="form-control" name="title" id="title" placeholder="Enter title">
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" name="description" id="description" placeholder="Enter description"></textarea>
        </div>
        <div class="form-group">
            <label for="content">Content:</label>
            <textarea class="form-control" name="content" id="content" placeholder="Enter content"></textarea>
        </div>

        <div class="form-group">
            <label for="image">Image:</label>
            <input type="file" class="form-control" name="image" id="image">
        </div>
        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="status" name="status">
            <label class="form-check-label" for="status">Enable</label>
        </div>
        <button type="submit" class="btn btn-primary" id="save-btn">Save Blog</button>
    </form>

    <hr>

    <h3>Blogs List</h3>
    <table class="table table-bordered" id="blogs-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Status</th>
                <th>Images</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($blogs as $blog)
            <tr id="blog-{{ $blog->id }}">
                <td>{{ $blog->title }}</td>
                <td>{{ $blog->description }}</td>
                <td>{{ $blog->status ? 'Enabled' : 'Disabled' }}</td>
                <td>
                    @if($blog->image_url)
                        <img src="{{ $blog->image_url }}" alt="Blog Image" width="50" height="50">
                    @else
                        No Image
                    @endif
                </td>
                <td>
                    <button class="btn btn-warning btn-sm edit-btn" data-id="{{ $blog->id }}">Edit</button>
                    <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $blog->id }}">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this blog post?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete-btn">Delete</button>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).on('click', '.edit-btn', function() {
        var blogId = $(this).data('id');

        $.get('/admin/blogs/' + blogId + '/edit', function(data) {
            $('#title').val(data.title);
            $('#description').val(data.description);
            $('#content').val(data.content);
            tinymce.get('content').setContent(data.content);
            $('#status').prop('checked', data.status);
            $('#blog_id').val(data.id);
            $('#save-btn').text('Update Blog');
        });
    });

    var blogIdToDelete = null;
    $(document).on('click', '.delete-btn', function() {
        blogIdToDelete = $(this).data('id');
        $('#deleteModal').modal('show');
    });

    $('#confirm-delete-btn').on('click', function() {
        if (blogIdToDelete) {
            $.ajax({
                url: '/admin/blogs/' + blogIdToDelete,
                type: 'DELETE',
                data: {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                },
                success: function(response) {
                    if (response.success) {
                        $('#blog-' + blogIdToDelete).remove();
                    }
                    $('#deleteModal').modal('hide');
                },
                error: function(xhr) {
                    console.log(xhr.responseJSON.errors);
                    $('#deleteModal').modal('hide');
                }
            });
        }
    });

    $('#blog-form').on('submit', function(e) {
        e.preventDefault();

        $('.error').remove();

        var title = $('#title').val();
        if (title === '') {
            $('#title').after('<div class="error text-danger">Title is required.</div>');
            return;
        }

        var description = $('#description').val();
        if (description === '') {
            $('#description').after('<div class="error text-danger">Description is required.</div>');
            return;
        }

        var content = tinymce.get('content').getContent();
        if (content === '') {
            $('#content').after('<div class="error text-danger">Content is required.</div>');
            return;
        }

        var fileInput = $('#image')[0].files[0];
        if (fileInput) {
            var fileName = fileInput.name;
            var fileExtension = fileName.split('.').pop().toLowerCase();
            var validExtensions = ['jpg', 'jpeg', 'png'];
            if (!validExtensions.includes(fileExtension)) {
                $('#image').after('<div class="error text-danger">Invalid image format. Please upload a jpg, jpeg, or png file.</div>');
                return;
            }
        }

        var formData = new FormData(this);
        var editorContent = tinymce.get('content').getContent();
        formData.append('content', editorContent);

        var isChecked = $('#status').is(':checked') ? 1 : 0;
        formData.append('status', isChecked);

        formData.forEach(function(value, key) {
            if (value instanceof File) {
                console.log(key + ": " + value.name);
                console.log(key + " size: " + value.size + " bytes");
            } else {
                console.log(key + ": " + value);
            }
        });

        var blogId = $('#blog_id').val();
        var url = blogId ? '/admin/blogs/' + blogId : '/admin/blogs';

        if (blogId) {
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.id) {
                    if (blogId) {
                        $('#blog-' + blogId).replaceWith(`
                            <tr id="blog-${response.id}">
                                <td>${response.title}</td>
                                <td>${response.description}</td>
                                <td>${Number(response.status) === 1 ? 'Enabled' : 'Disabled'}</td>
                                <td>${response.image_url ? `<img src="${response.image_url}" width="50" height="50">` : 'No Image'}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm edit-btn" data-id="${response.id}">Edit</button>
                                    <button class="btn btn-danger btn-sm delete-btn" data-id="${response.id}">Delete</button>
                                </td>
                            </tr>
                        `);
                    } else {
                        $('#blogs-table tbody').prepend(`
                            <tr id="blog-${response.id}">
                                <td>${response.title}</td>
                                <td>${response.description}</td>
                                <td>${Number(response.status) === 1 ? 'Enabled' : 'Disabled'}</td>
                                <td>${response.image_url ? `<img src="${response.image_url}" width="50" height="50">` : 'No Image'}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm edit-btn" data-id="${response.id}">Edit</button>
                                    <button class="btn btn-danger btn-sm delete-btn" data-id="${response.id}">Delete</button>
                                </td>
                            </tr>
                        `);
                    }
                }

                $('#blog-form')[0].reset();
                $('#save-btn').text('Save Blog');
                $('#blog_id').val('');
            },
            error: function(xhr) {
                console.log(xhr.responseJSON.errors);
            }
        });
    });
});
</script>

<script>
    tinymce.init({
        selector: '#content',
        height: 300,
        plugins: 'lists link image code table',
        toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image code',
        menubar: false,
    });
</script>

@endsection
