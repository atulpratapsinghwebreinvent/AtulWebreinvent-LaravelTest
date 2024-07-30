<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atul Pratap Singh | Webreinvent Laravel Test</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
    <style>
        .form-container, .comment-form-container {
            display: block;
        }
        .comment-form-container {
            display: none;
            margin-top: 20px;
        }
        .comment-list {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container text-center mt-4">
    <h1>Atul Pratap Singh | Webreinvent Laravel Test</h1>
    <h2 class="text-primary">Post Management Page</h2>
    <div id="message" class="alert alert-success d-none"></div>

    <div id="formContainer" class="form-container mb-3">
        <form id="postForm">
            <div class="form-group">
                <label for="title">Post Title</label>
                <input type="text" id="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="content">Post Content</label>
                <textarea id="content" class="form-control" required></textarea>
            </div>
            <button type="submit" id="submitBtn" class="btn btn-primary">Add Post</button>
            <button type="reset" id="cancelBtn" class="btn btn-secondary">Reset</button>
        </form>
    </div>

    <ul id="postList" class="list-group">
    </ul>

    <div id="commentFormContainer" class="comment-form-container">
        <h3 id="commentFormTitle">Add Comment</h3>
        <form id="commentForm">
            <div class="form-group">
                <label for="commentContent">Comment</label>
                <textarea id="commentContent" class="form-control" required></textarea>
            </div>
            <button type="submit" id="commentSubmitBtn" class="btn btn-primary">Add Comment</button>
            <button type="reset" id="commentCancelBtn" class="btn btn-secondary">Reset</button>
        </form>

    </div>
</div>

<script>
    //create constant for link to avoid url error
    const apiBaseUrl = '/api/posts';
    let editMode = false;
    let currentPostId = null;

    //Code for display the message according operation
    const showMessage = (message, type = 'success') => {
        const messageElement = document.getElementById('message');
        messageElement.textContent = message;
        messageElement.className = `alert alert-${type}`;
        messageElement.classList.remove('d-none');
        setTimeout(() => {
            messageElement.classList.add('d-none');
        }, 3000);
    };

    //Code for generate slug random with the help of title input
    const generateSlug = (title) => {
        return title.toLowerCase();
    };

    //Code to fetch the all post from the database
    const fetchPosts = () => {
        axios.get(apiBaseUrl)
            .then(response => {
                const postList = document.getElementById('postList');
                postList.innerHTML = '';
                response.data.forEach(post => {
                    const postItem = document.createElement('li');
                    postItem.className = 'list-group-item';
                    const escapedTitle = post.title;
                    const escapedContent = post.content;
                    postItem.innerHTML = `
                        <h5>${post.title}</h5>
                        <p>${post.content}</p>
                        <small>Comments: ${post.comments_count}</small>
                        <button class="btn btn-warning btn-sm" onclick="editPost(${post.id}, '${escapedTitle}', '${escapedContent}')">Edit</button>
                        <button class="btn btn-danger btn-sm" onclick="deletePost(${post.id})">Delete</button>
                        <button class="btn btn-info btn-sm" onclick="addComments(${post.id})">Add Comments</button>
                        <button class="btn btn-primary btn-sm" onclick="viewComments(${post.id})">View Comments</button>`;
                    postList.appendChild(postItem);
                });
            })
    };

    //Code for save the input into the database
    const savePost = (event) => {
        event.preventDefault();
        const title = document.getElementById('title').value;
        const content = document.getElementById('content').value;
        const slug = generateSlug(title);

        const postData = { title, slug, content };

        if (editMode && currentPostId != null) {
            axios.put(`${apiBaseUrl}/${currentPostId}`, postData)
                .then(response => {
                    showMessage('Post updated successfully');
                    fetchPosts();
                })
        } else {
            axios.post(apiBaseUrl, postData)
                .then(response => {
                    showMessage('Post created successfully');
                    fetchPosts();
                })

        }
    };

    //Code to delete the post and refresh the page
    window.deletePost = (id) => {
        if (confirm('Are you sure you want to delete this post?')) {
            axios.delete(`${apiBaseUrl}/${id}`)
                .then(response => {
                    showMessage('Post deleted successfully', 'danger');
                    fetchPosts();
                })

        }
    };

    //Code to edit the post and refresh the page
    window.editPost = (id, title, content) => {
        document.getElementById('title').value = title;
        document.getElementById('content').value = content;
        currentPostId = id;
        editMode = true;
        document.getElementById('formContainer').style.display = 'block';
    };

    //Code to display comments option in the page
    window.addComments = (postId) => {
        currentPostId = postId;
        document.getElementById('commentFormContainer').style.display = 'block';

        axios.get(`${apiBaseUrl}/${postId}/comments`)
            .then(response => {
                const commentList = document.getElementById('commentList');
                commentList.innerHTML = '';
                response.data.forEach(comment => {
                    const commentItem = document.createElement('div');
                    commentList.appendChild(commentItem);
                });
            })

    };
    const viewComments = (postId) => {
        window.open(`${apiBaseUrl}/${postId}/comments`,'_blank');
        currentPostId = postId;

    };

    //Code to save the comment into the database
    const saveComment = (event) => {
        event.preventDefault();
        const content = document.getElementById('commentContent').value;

        axios.post(`${apiBaseUrl}/${currentPostId}/comments`, { content })
            .then(response => {
                showMessage('Comment added successfully');
                document.getElementById('commentContent').value = '';
            })

    };


    //Code for take the event performed in the Form
    document.addEventListener('DOMContentLoaded', () => {
        const postForm = document.getElementById('postForm');

        const commentForm = document.getElementById('commentForm');
        const commentCancelBtn = document.getElementById('commentCancelBtn');

        postForm.addEventListener('submit', savePost);
        commentForm.addEventListener('submit', saveComment);
        commentCancelBtn.addEventListener('click', () => {
            document.getElementById('commentFormContainer').style.display = 'none';
        });

        //Called the function to fetch posts
        fetchPosts();
    });
</script>
</body>
</html>
