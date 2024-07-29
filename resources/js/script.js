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

        let postTitle = document.getElementById('post-title').value();
        let postContent = document.getElementById('post-content').value();

        axios.get('/api/posts', {

            post_title: postTitle,
            post_content: postContent
        }).then(response => {
            alert('Post Created Successfully!');

            //call the all fetch post

            fetchAllPostFormDatabase();
        }).catch(error => {
            alert('Something Went Wrong!');
            console.error('Error', error);
        });

    });
});
