<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Atul Pratap Singh | WebReinvent Laravel Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
<h1 class="text-center mt-5">Atul Pratap Singh | WebReinvent Laravel Test</h1>

<div class="container mt-5">

    <h2>Create Post From Here</h2>
    <form id="create-post-form-data">
        <div class="form-group">
            <label class="post-title">Post Title</label>
            <input type="text" class="form-control mt-2" id="post-title" placeholder="Enter the Post Title" required>
        </div>
        <div class="form-group">
            <label class="post-title">Post Content</label>
            <input type="text" class="form-control mt-2" id="post-content" placeholder="Enter the Post content" required>
        </div>

        <button type="submit" class="btn btn-success mt-2">Create Post</button>

    </form>
    <div id="root" class="mt-5">

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.7.2/axios.min.js"></script>

<script>
    document.addEventListener('DomContentLoader', function ()
    {

        // display the all post here with the help of javascript
        //creating one function to fetch all posts

        fetchAllPostFormDatabase();


        //function define

        function fetchAllPostFormDatabase()
        {
            axios.get('/api/posts')
                .then(response =>
                {
                    let postsData = response.data;

                    let postListData = '';

                    postsData.forEach(
                        posts =>
                        {
                            postListData +=
                                ` <div class="card" style="width: 18rem;">
  <div class="card-body">
    <h5 class="card-title">Card title</h5>
    <h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>
    <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>

  </div>
</div>`;
                        }
                    );
                    document.getElementById('root').innerHTML = postListData;
                });
        }


        //create form and send data

        document.getElementById('create-post-form-data').addEventListener('submit', function (e)
        {
            e.preventDefault();

            //get the input field to store data

            let postTitle = document.getElementById('post-title').value;
            let postContent = document.getElementById('post-content').value;
            console.log('Title', postTitle);

            axios.post('/api/posts', {

                title: postTitle,
                content: postContent
            }).then(response => {
                console.log('Response: ', response.data);
                alert('Post Created Successfully!');

                //call the all fetch post

                fetchAllPostFormDatabase();
            }).catch(error => {
                alert('Something Went Wrong!');
                console.error('Error', error);
            });

        });
    });

</script>
</body>
</html>
