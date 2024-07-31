<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atul Pratap Singh | Webreinvent Laravel Test</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" crossorigin="anonymous">
    <style>
        .main-container {
            max-width: 600px;
            margin: auto;
            padding: 40px;
            text-align: center;
        }
        .btn-custom {
            margin: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="main-container">
        <h1> Atul Pratap Singh | Webreinvent Laravel Test</h1>
        <p class="lead">Select one of the options below to view or manage tasks and posts.</p>
        <a href="{{ url('/posts') }}" class="btn btn-primary btn-custom">Posts Management</a>
        <a href="{{ url('/tasks') }}" class="btn btn-success btn-custom">Tasks Management</a>
    </div>
</div>
</body>
</html>
