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
    <h2 class="text-danger">Task Management Page</h2>
    <div id="message" class="alert alert-success d-none"></div>

    <div id="formContainer" class="form-container mb-3">
        <form id="postForm">
            <div class="form-group">
                <label for="title">Task Title</label>
                <input type="text" id="title" class="form-control">
            </div>
            <div class="form-group">
                <label for="content">Task Description</label>
                <textarea id="content" class="form-control"></textarea>
            </div>
            <button type="submit" id="submitBtn" class="btn btn-primary">Add Task</button>
            <button type="reset" id="cancelBtn" class="btn btn-secondary">Reset</button>
        </form>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2>Task Comments</h2>
                <div id="commentList" class="comment-list">

                </div>
            </div>
            <div class="col-md-6">
                <h2>Task List</h2>
                <ul id="postList" class="list-group">

                </ul>
            </div>
        </div>
    </div>

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
    const apiBaseUrl = '/api/tasks';
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
    const fetchTasks = () => {
        axios.get(apiBaseUrl)
            .then(response => {
                const sortedPosts = response.data.sort((a, b) => b.id - a.id);
                const postList = document.getElementById('postList');
                postList.innerHTML = '';
                response.data.forEach(post => {
                    const postItem = document.createElement('li');
                    postItem.className = 'list-group-item';
                    const escapedTitle = post.title;
                    const escapedContent = post.description;
                    postItem.innerHTML = `
                        <h5>${post.title}</h5>
                        <p>${post.description}</p>
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
        const titleInput = document.getElementById('title');
        const contentInput = document.getElementById('content');
        const title = titleInput.value;
        const description = contentInput.value;
        if (!title.trim()) {
            showMessage('Task Title is required', 'danger');
            return;
        }
        const slug = generateSlug(title);

        const postData = { title, slug, description };

        if (editMode && currentPostId != null) {
            axios.put(`${apiBaseUrl}/${currentPostId}`, postData)
                .then(response => {
                    showMessage('Task updated successfully');
                    titleInput.value = '';
                    contentInput.value = '';
                    document.getElementById('submitBtn').textContent = 'Add Task';
                    fetchTasks();
                })
        } else {
            axios.post(apiBaseUrl, postData)
                .then(response => {
                    showMessage('Task created successfully');
                    fetchTasks();
                    titleInput.value = '';
                    contentInput.value = '';
                })

        }
    };

    //Code to delete the post and refresh the page
    window.deletePost = (id) => {
        if (confirm('Are you sure you want to delete this Task?')) {
            axios.delete(`${apiBaseUrl}/${id}`)
                .then(response => {
                    showMessage('Task deleted successfully', 'danger');
                    fetchTasks();
                })

        }
    };

    //Code to edit the post and refresh the page
    window.editPost = (id, title, description) => {
        document.getElementById('title').value = title;
        document.getElementById('content').value = description;
        currentPostId = id;
        editMode = true;
        document.getElementById('submitBtn').textContent = 'Update Task';
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
        const commentList = document.getElementById('commentList');

        axios.get(`${apiBaseUrl}/${postId}`)
            .then(postResponse => {
                const TaskTitle = postResponse.data.title;
                console.log(TaskTitle);
                axios.get(`${apiBaseUrl}/${postId}/comments`)
                    .then(commentsResponse => {
                        const comments = commentsResponse.data;
                        commentList.innerHTML = '';

                        const postTitleElement = document.createElement('h3');
                        postTitleElement.textContent = `Title: ${TaskTitle}`;
                        commentList.appendChild(postTitleElement);


                        if (comments.length === 0) {
                            const noCommentsElement = document.createElement('p');
                            noCommentsElement.textContent = 'No comments found.';
                            commentList.appendChild(noCommentsElement);
                        } else {
                            comments.forEach(comment => {
                                const commentItem = document.createElement('div');
                                commentItem.className = 'comment-item';
                                commentItem.innerHTML = `
                                Comment:<p>${comment.content}</p>
                            `;
                                commentList.appendChild(commentItem);
                            });
                        }
                    })
            })

    };

    //Code to save the comment into the database
    const saveComment = (event) => {
        event.preventDefault();
        const content = document.getElementById('commentContent').value;

        axios.post(`${apiBaseUrl}/${currentPostId}/comments`, { content })
            .then(response => {
                showMessage('Comment added successfully');

                document.getElementById('commentContent').value = '';
                document.getElementById('commentFormContainer').style.display = 'none';
            })

    };

    const resetForm = () => {
        document.getElementById('submitBtn').textContent = 'Add Task';
        editMode = false;
        currentPostId = null;

    };

    const postForm = document.getElementById('postForm');
    postForm.addEventListener('reset', resetForm);

    //Code for take the event performed in the Form
    document.addEventListener('DOMContentLoaded', () => {
        const postForm = document.getElementById('postForm');
        const cancelBtn = document.getElementById('cancelBtn');
        const commentForm = document.getElementById('commentForm');
        const commentCancelBtn = document.getElementById('commentCancelBtn');

        postForm.addEventListener('submit', savePost);
        commentForm.addEventListener('submit', saveComment);
        commentCancelBtn.addEventListener('click', () => {
            document.getElementById('commentFormContainer').style.display = 'none';
        });

        fetchTasks();
    });
</script>
</body>
</html>
