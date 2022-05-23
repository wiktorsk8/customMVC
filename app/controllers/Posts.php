<?php
class Posts extends Controller
{
    public $postModel;
    public $userModel;

    public function __construct()
    {
        if (!isLoggedIn_helper()) {
            flash_helper('not_logged', 'you are not logged in!', 'alert alert-danger');
            redirect('users/login');
        }

        $this->postModel = $this->model('Post');
        $this->userModel = $this->model('User');
    }

    public function index()
    {
        $posts = $this->postModel->getPosts();
        $data = [
            'posts' => $posts
        ];

        $this->view('posts/index', $data);
    }

    // Add post
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'title' => trim($_POST['title']),
                'body' => trim($_POST['body']),
                'user_id' => $_SESSION['user_id'],
                'title_err' => '',
                'body_err' => ''
            ];

            $this->validateAddPost($data);
        } else {
            $data = [
                'title' => '',
                'body' => ''
            ];
        }
        $this->view('posts/add', $data);
    }

    
    private function validateAddPost($data)
    {
        if (empty($data['title'])) {
            $data['title_err'] = 'Title cannot be blank';
        }
        if (empty($data['body'])) {
            $data['body_err'] = 'Body cannot be blank';
        }

        $this->cleanErrors($data);
    }

    private function cleanErrors($data)
    {
        if (empty($data['title_err']) && empty($data['body_err'])) {
            if ($this->postModel->addPost($data)) {
                flash_helper('post_message', 'Post added succesfully!');
                redirect('posts');
            } else {
                die("something went wrong");
            }
        } else {
            $this->view('posts/add', $data);
        }
    }

    // Edit Post
    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'id' => $id,
                'title' => trim($_POST['title']),
                'body' => trim($_POST['body']),
                'user_id' => $_SESSION['user_id'],
                'title_err' => '',
                'body_err' => ''
            ];

            $this->validateEditPost($data);
        } else {


            $post = $this->postModel->getPostById($id);

            if ($post->user_id != $_SESSION['user_id']) {
                redirect('posts');
            }

            $data = [
                'id' => $id,
                'title' => $post->title,
                'body' => $post->body
            ];
            $this->view('posts/edit', $data);
        }
    }

    private function validateEditPost($data)
    {
        if (empty($data['title'])) {
            $data['title_err'] = 'Title cannot be blank';
        }
        if (empty($data['body'])) {
            $data['body_err'] = 'Body cannot be blank';
        }

        $this->cleanEditErrors($data);
    }

    private function cleanEditErrors($data)
    {
        if (empty($data['title_err']) && empty($data['body_err'])) {
            if ($this->postModel->UpdatePost($data)) {
                flash_helper('post_message', 'Post edited succesfully!');
                redirect('posts');
            } else {
                die("something went wrong");
            }
        } else {
            $this->view('posts/edit', $data);
        }
    }

    // Show post
    public function show($id)
    {

        $post = $this->postModel->getPostById($id);
        $user = $this->userModel->getUserById($post->user_id);

        $data = [
            'post' => $post,
            'user' => $user
        ];

        $this->view('posts/show', $data);
    }

    // Delete post

    public function delete($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if($this->postModel->deletePost($id)){
                flash_helper('post_message', 'Post removed');
                redirect('posts');
            }else{
                die('cos sie zesralo');
            }
        }else{
            redirect('posts');
        }
    }

}
