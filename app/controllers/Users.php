<?php

class Users extends Controller
{
    public $userModel;
    public function __construct()
    {
        $this->userModel = $this->model('User');
    }

    public function register()
    {

        //check for POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'name_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            $this->validateRegister($data);

        } else {
            //load form
            $data = [
                'name' => '',
                'email' => '',
                'password' => '',
                'confirm_password' => '',
                'name_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            $this->view('users/register', $data);
        }
    }
    public function login()
    {

        //check for POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'email_err' => '',
                'password_err' => '',
            ];

            $this->validateLogin($data);

        } else {
            $data = [
                'email' => '',
                'password' => '',
                'email_err' => '',
                'password_err' => '',
            ];

            // Load view
            $this->view('users/login', $data);
        }
    }

    private function validateRegister($data)
    {
        if (empty($data['email'])) {
            $data['email_err'] = 'Please enter email';
        }else if($this->userModel->findUserByEmail($data['email'])){
            $data['email_err'] = 'This email is alredy taken.';
        }

        if (empty($data['name'])) {
            $data['name_err'] = 'Please name email';
        }

        if (empty($data['password'])) {
            $data['password_err'] = "This field cannot be empty";
        } else if (strlen($data['password']) < 6) {
            $data['password_err'] = 'Password must be at least 6 characters long';
        }

        if (empty($data['confirm_password'])) {
            $data['confirm_password_err'] = 'Pleae confirm password';
        } else {
            if ($data['password'] != $data['confirm_password']) {
                $data['confirm_password_err'] = 'Passwords do not match';
            }
        }

        $this->cleanErrorsRegister($data);
    }

    private function validateLogin($data)
    {
        if (empty($data['email'])) {
            $data['email_err'] = 'Please enter email';
        }

        if (empty($data['password'])) {
            $data['password_err'] = "This field cannot be empty";
        }

        if($this->userModel->findUserByEmail($data['email'])){
            //found
        }else{
            $data['email_err'] = 'User not found. Check your email and try again.';
        }

        $this->cleanErrorsLogin($data);
    }

    private function cleanErrorsRegister($data)
    {
        if (empty($data['email_err']) && empty($data['name_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {

            $this->registerUser($data);

        } else {
            $this->view('users/register', $data);
        }
    }

    private function cleanErrorsLogin($data)
    {
        if (empty($data['email_err']) && empty($data['password_err'])) {
            $loggedUser = $this->userModel->login($data['email'], $data['password']);

            if($loggedUser){
                $this->createUserSession($loggedUser);
            }else{
                $data['password_err'] = 'Incorrect password';

                $this->view('users/login', $data);
            }
        } else {
            $this->view('users/login', $data);
        }
    }

    private function registerUser($data){

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        if($this->userModel->register($data)){
            flash_helper('register_success', 'You are registered!');
            redirect('users/login');
        }else{
            die("something went wrong...");
        }
    }

    private function createUserSession($user){
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->name;
        redirect('pages/index');
    }

    public function logout(){
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        session_destroy();
        redirect('users/login');
    }


    // Temporary method for debugging
    private function isLoggedIn(){
        if(isset($_SESSION['user_id'])){
            echo "logged";
            return true;
        }else{
            echo "not logged";
            return false;
        }
    }
}
