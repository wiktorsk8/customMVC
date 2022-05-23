<?php
    class Pages extends Controller{
        public function __construct()
        {
             
        }

        public function index(){
            if(isLoggedIn_helper()){
                redirect('posts');
            }
            $data = [
                'title' => 'SocialMedia',
                'description' => 'Socialmedia made on my own php framework',
            ];

            $this->view('pages/index', $data);
        }

        public function about(){
            $data = [
                'title' => 'About us',
                'description' => 'Its basicly unusable instagram or fb'
            ];

            $this->view('pages/about', $data);
        }


    }