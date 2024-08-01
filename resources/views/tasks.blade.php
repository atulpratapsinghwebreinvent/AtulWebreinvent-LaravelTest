<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atul Pratap Singh | Webreinvent Laravel Test</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
    <style>
        .form-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
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
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#taskModal" onclick="openTaskModal()">Create New Task</button>
    <a href="{{ url('/') }}" class="btn btn-success btn-custom mb-3">Home Page</a>
    <div class="container">
        <h2>Task List</h2>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Comments</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody id="postList">
            <!-- Task rows will be inserted here -->
            </tbody>
        </table>
    </div>

    <div class="container">
        <h2>Task Comments</h2>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Comment</th>
            </tr>
            </thead>
            <tbody id="commentList">
            <!-- Comments rows will be inserted here -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Task Form -->
<div class="modal fade" id="taskModal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskModalLabel">Task Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="postForm">
                    <div id="errorMessages" class="alert alert-danger d-none"></div>
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
        </div>
    </div>
</div>

<!-- Modal for Comment Form -->
<div class="modal fade" id="commentModal" tabindex="-1" role="dialog" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentModalLabel">Add Comment</h5>
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
                    <button type="reset" id="commentCancelBtn" class="btn btn-secondary">Reset</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const apiBaseUrl = '/api/tasks';
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

    const showErrorMessages = (errors) => {
        const errorMessagesElement = document.getElementById('errorMessages');
        errorMessagesElement.innerHTML = errors.join('<br>');
        errorMessagesElement.classList.remove('d-none');
        setTimeout(() => {
            errorMessagesElement.classList.add('d-none');
        }, 3000);
    };

    const generateSlug = (title) => {
        return title.toLowerCase();
    };

    const fetchTasks = () => {
        axios.get(apiBaseUrl)
            .then(response => {
                const tasks = response.data.sort((a, b) => b.id - a.id);
                const postList = document.getElementById('postList');
                postList.innerHTML = '';

                tasks.forEach(task => {
                    const taskRow = document.createElement('tr');
                    taskRow.id = `task-${task.id}`;
                    taskRow.innerHTML =
                        `<td>${task.title}</td>
                         <td>${task.description}</td>
                         <td><span id="comments-count-${task.id}">${task.comments_count}</span></td>
                         <td>
                            <button class="btn btn-warning btn-sm" onclick="editTask(${task.id}, '${task.title}', '${task.description}')">Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteTask(${task.id})">Delete</button>
                            <button class="btn btn-info btn-sm" onclick="showCommentForm(${task.id})">Add Comment</button>
                            <button class="btn btn-info btn-sm" onclick="viewComments(${task.id})">View Comments</button>
                         </td>`;
                    postList.appendChild(taskRow);
                });
            })
            .catch(error => {
                showMessage('Failed to fetch tasks. Please try again.', 'danger');
            });
    };

    const saveTask = (event) => {
        event.preventDefault();
        const titleInput = document.getElementById('title');
        const contentInput = document.getElementById('content');
        const title = titleInput.value;
        const description = contentInput.value;

        const errors = [];
        if (!title.trim()) {
            errors.push('Task Title is required.');
        }

        if (description.length > 255) {
            errors.push('Task Description length should be 255 characters or less.');
        }

        if (errors.length > 0) {
            showErrorMessages(errors);
            return;
        }

        axios.get(apiBaseUrl)
            .then(response => {
                const existingTasks = response.data;
                const isDuplicate = existingTasks.some(task => task.title.trim().toLowerCase() === title.trim().toLowerCase() && task.id !== currentPostId);

                if (isDuplicate) {
                    showMessage('A task with the same title already exists', 'danger');
                    return;
                }

                const slug = generateSlug(title);
                const taskData = { title, slug, description };

                if (editMode && currentPostId != null) {
                    axios.put(`${apiBaseUrl}/${currentPostId}`, taskData)
                        .then(response => {
                            showMessage('Task updated successfully');
                            resetForm();
                            fetchTasks();
                            $('#taskModal').modal('hide');
                        })
                        .catch(error => {
                            showMessage('Failed to update task. Please try again.', 'danger');
                            $('#taskModal').modal('hide');
                        });
                } else {
                    axios.post(apiBaseUrl, taskData)
                        .then(response => {
                            showMessage('Task created successfully');
                            fetchTasks();
                            $('#taskModal').modal('hide');
                        })
                        .catch(error => {
                            showMessage('Failed to create task. Please try again.', 'danger');
                            $('#taskModal').modal('hide');
                        });
                }
            })
            .catch(error => {
                showMessage('Failed to check for duplicate tasks. Please try again.', 'danger');
            });
    };

    const deleteTask = (id) => {
        if (confirm('Are you sure you want to delete this Task?')) {
            axios.delete(`${apiBaseUrl}/${id}`)
                .then(response => {
                    showMessage('Task deleted successfully', 'danger');
                    fetchTasks();
                })
                .catch(error => {
                    showMessage('Failed to delete task. Please try again.', 'danger');
                });
        }
    };

    const editTask = (id, title, description) => {
        document.getElementById('title').value = title;
        document.getElementById('content').value = description;
        currentPostId = id;
        editMode = true;
        document.getElementById('submitBtn').textContent = 'Update Task';
        $('#taskModal').modal('show');

        axios.get(apiBaseUrl)
            .then(response => {
                const existingTasks = response.data;
                const isDuplicate = existingTasks.some(task => task.title.trim().toLowerCase() === title.trim().toLowerCase() && task.id !== id);

                if (isDuplicate) {
                    showMessage('A task with the same title already exists', 'danger');
                    $('#taskModal').modal('hide');
                }
            })
    };

    const showCommentForm = (taskId) => {
        currentPostId = taskId;
        $('#commentModal').modal('show');
    };

    const viewComments = (taskId) => {
        axios.get(`${apiBaseUrl}/${taskId}/comments`)
            .then(response => {
                const commentList = document.getElementById('commentList');
                commentList.innerHTML = '';
                if (response.data.length > 0) {
                    response.data.forEach(comment => {
                        const commentRow = document.createElement('tr');
                        commentRow.innerHTML = `<td>${comment.content}</td>`;
                        commentList.appendChild(commentRow);
                    });
                } else {
                    commentList.innerHTML = '<tr><td>No comments available</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error fetching comments:', error);
            });
    };

    const saveComment = (event) => {
        event.preventDefault();
        const content = document.getElementById('commentContent').value;
        const entityId = currentPostId;
        console.log(entityId);
        if (!content.trim()) {
            showMessage('Comment content is required', 'danger');
            return;
        }
        if (content.length > 255) {
            showMessage('Comment Content Length is extended', 'danger');
            $('#commentModal').modal('hide');
            return;
        }
        axios.post(`${apiBaseUrl}/${entityId}/comments`, { content })
            .then(response => {
                showMessage('Comment added successfully');
                document.getElementById('commentContent').value = '';
                $('#commentModal').modal('hide');

                const commentsCountElement = document.getElementById(`comments-count-${entityId}`);
                if (commentsCountElement) {
                    const currentCount = parseInt(commentsCountElement.innerText, 10);
                    commentsCountElement.innerText = currentCount + 1;
                }
            })

    };

    const resetForm = () => {
        document.getElementById('title').value = '';
        document.getElementById('content').value = '';
        document.getElementById('errorMessages').classList.add('d-none');
    };

    function openTaskModal() {
        resetForm();
        document.getElementById('submitBtn').textContent = 'Add Task';
        editMode = false;
        currentPostId = null;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const postForm = document.getElementById('postForm');
        const commentForm = document.getElementById('commentForm');

        postForm.addEventListener('submit', saveTask);
        commentForm.addEventListener('submit', saveComment);
        fetchTasks();
    });
</script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
