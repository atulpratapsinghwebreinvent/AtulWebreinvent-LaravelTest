<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atul Pratap Singh | Webreinvent Laravel Test</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <style>
        .comment-list {
            margin-top: 20px;
        }
        .form-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
    </style>
</head>
<body>
<div class="container text-center mt-4">
    <h1>Atul Pratap Singh | Webreinvent Laravel Test</h1>
    <h2 class="text-primary">Post Management Page</h2>
    <div id="message" class="alert alert-success d-none"></div>

    <!-- Button to Open Post Form Modal -->
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#postFormModal" onclick="openPostModal()">Create New Post</button>

    <!-- Post List Table -->
    <div class="container">
        <h2>Post List</h2>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Title</th>
                <th>Content</th>
                <th>Comments</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody id="postList">
            <!-- Post items will be dynamically added here -->
            </tbody>
        </table>
    </div>

    <!-- Comment List Table -->
    <div class="container">
        <h2>Post Comments</h2>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Comment</th>
            </tr>
            </thead>
            <tbody id="commentList">
            <!-- Comment items will be dynamically added here -->
            </tbody>
        </table>
    </div>
</div>

<!-- Post Form Modal -->
<div class="modal fade" id="postFormModal" tabindex="-1" role="dialog" aria-labelledby="postFormModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="postFormModalLabel">Post Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="postForm">
                    @csrf
                    <div class="form-group">
                        <label for="title">Post Title</label>
                        <input type="text" id="title" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="content">Post Content</label>
                        <textarea id="content" class="form-control" rows="5"></textarea>
                    </div>
                    <button type="submit" id="submitBtn" class="btn btn-primary mr-2">Add Post</button>
                    <button type="reset" id="cancelBtn" class="btn btn-secondary">Reset</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Comment Form Modal -->
<div class="modal fade" id="commentFormModal" tabindex="-1" role="dialog" aria-labelledby="commentFormModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentFormModalLabel">Comment Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="commentForm">
                    <div class="form-group">
                        <label for="commentContent">Comment</label>
                        <textarea id="commentContent" class="form-control" required></textarea>
                    </div>
                    <button type="submit" id="commentSubmitBtn" class="btn btn-primary">Add Comment</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const apiBaseUrl = '/api/posts';
    let editMode = false;
    let currentPostId = null;

    const showMessage = (message, type = 'success') => {
        const messageElement = document.getElementById('message');
        messageElement.textContent = message;
        messageElement.className = `alert alert-${type}`;
        messageElement.classList.remove('d-none');
        setTimeout(() => {
            messageElement.classList.add('d-none');
        }, 3000);
    };

    const generateSlug = (title) => {
        return title.toLowerCase();
    };

    const fetchPosts = () => {
        axios.get(apiBaseUrl)
            .then(response => {
                const sortedPosts = response.data.sort((a, b) => b.id - a.id);

                const postList = document.getElementById('postList');
                postList.innerHTML = '';

                sortedPosts.forEach(post => {
                    const postRow = document.createElement('tr');
                    const escapedTitle = post.title;
                    const escapedContent = post.content;
                    postRow.id = `post-${post.id}`;
                    postRow.innerHTML = `
                        <td>${post.title}</td>
                        <td>${post.content}</td>
                        <td><span id="comments-count-${post.id}">${post.comments_count}</span></td>
                        <td>
                            <button class="btn btn-warning btn-sm" onclick="editPost(${post.id}, '${escapedTitle}', '${escapedContent}')">Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="deletePost(${post.id})">Delete</button>
                            <button class="btn btn-info btn-sm" onclick="addComments(${post.id})">Add Comments</button>
                            <button class="btn btn-primary btn-sm" onclick="viewComments(${post.id})">View Comments</button>
                        </td>
                    `;
                    postList.appendChild(postRow);
                });
            });
    };

    const savePost = (event) => {
        event.preventDefault();
        const titleInput = document.getElementById('title');
        const contentInput = document.getElementById('content');
        const title = titleInput.value;
        const content = contentInput.value;

        if (!title.trim()) {
            showMessage('Post Title is required', 'danger');
            return;
        }

        const slug = generateSlug(title);
        const postData = { title, slug, content };

        if (editMode && currentPostId != null) {
            axios.put(`${apiBaseUrl}/${currentPostId}`, postData)
                .then(response => {
                    showMessage('Post updated successfully');
                    fetchPosts();
                    $('#postFormModal').modal('hide');
                });
        } else {
            axios.post(apiBaseUrl, postData)
                .then(response => {
                    showMessage('Post created successfully');
                    fetchPosts();
                    $('#postFormModal').modal('hide');
                });
        }

        titleInput.value = '';
        contentInput.value = '';
        document.getElementById('submitBtn').textContent = 'Add Post';
        editMode = false;
        currentPostId = null;
    };

    window.deletePost = (id) => {
        if (confirm('Are you sure you want to delete this post?')) {
            axios.delete(`${apiBaseUrl}/${id}`)
                .then(response => {
                    showMessage('Post deleted successfully', 'danger');
                    fetchPosts();
                });
        }
    };

    window.editPost = (id, title, content) => {
        document.getElementById('title').value = title;
        document.getElementById('content').value = content;
        currentPostId = id;
        editMode = true;
        document.getElementById('submitBtn').textContent = 'Update Post';
        $('#postFormModal').modal('show');
    };

    window.addComments = (postId) => {
        currentPostId = postId;
        $('#commentFormModal').modal('show');
    };

    const viewComments = (postId) => {
        currentPostId = postId;
        $('#commentFormModal').modal('hide'); // Hide the comment form modal
        axios.get(`${apiBaseUrl}/${postId}/comments`)
            .then(response => {
                const commentList = document.getElementById('commentList');
                commentList.innerHTML = '';
                response.data.forEach(comment => {
                    const commentRow = document.createElement('tr');
                    commentRow.innerHTML = `<td>${comment.content}</td>`;
                    commentList.appendChild(commentRow);
                });
            });
    };

    const saveComment = (event) => {
        event.preventDefault();
        const content = document.getElementById('commentContent').value;
        const postId = currentPostId;

        axios.post(`${apiBaseUrl}/${postId}/comments`, { content })
            .then(response => {
                showMessage('Comment added successfully');
                document.getElementById('commentContent').value = '';
                $('#commentFormModal').modal('hide');

                const commentsCountElement = document.getElementById(`comments-count-${postId}`);
                if (commentsCountElement) {
                    const currentCount = parseInt(commentsCountElement.innerText, 10);
                    commentsCountElement.innerText = currentCount + 1;
                }
            });
    };

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('postForm').addEventListener('submit', savePost);
        document.getElementById('commentForm').addEventListener('submit', saveComment);
        fetchPosts();
    });

    function openPostModal() {
        document.getElementById('title').value = '';
        document.getElementById('content').value = '';
        document.getElementById('submitBtn').textContent = 'Add Post';
        editMode = false;
        currentPostId = null;
    }
</script>
</body>
</html>
