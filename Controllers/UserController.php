<?php

class UserController extends Controller
{
    public function getAllUsersAction()
    {
        $user  = new User();
        $users = $user->get(false, ["firstname", "lastname", "email", "gender"])->fetch();
        echo json_encode(array(
            'status' => 'Success',
            'data'   => $users
        ));
    }

    public function getUserAction($id)
    {
        $user        = new User();
        $currentUser = $user->get(false, ["firstname", "lastname", "email", "gender"])->simple(['id' => $id])->fetch();
        echo json_encode(array(
            'status' => 'Success',
            'data'   => $currentUser
        ));
    }

    public function createUserAction()
    {
        $user         = new User();
        $error        = false;
        $firstname    = isset($_POST['firstname']) ? App::test_input($_POST['firstname']) : '';
        $lastname     = isset($_POST['lastname']) ? App::test_input($_POST['lastname']) : '';
        $email        = isset($_POST['email']) ? App::test_input($_POST['email']) : '';
        $password     = isset($_POST['password']) ? App::test_input($_POST['password']) : '';
        $confpassword = isset($_POST['confPassword']) ? App::test_input($_POST['confPassword']) : '';
        $gender       = isset($_POST['gender']) ? $_POST['gender'] : 'MALE';

        if (empty($firstname) ) {
            $error = true;
        } else {
            if(!preg_match("/^[a-zA-Z ]*$/",$firstname)){
                $error = true;
            }
        }

        if (empty($lastname)) {
            $error = true;
        } else {
            if(!preg_match("/^[a-zA-Z ]*$/",$lastname)){
                $error = true;
            }
        }

        if (empty($email)) {
            $error = true;
        }
        elseif (count($user->get()->simple(["email" => $email])->query()) > 0) {
            $error = true;
        } else {
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $error = true;
            }
        }

        if (empty($password)) {
            $error = true;
        }

        if (empty($confpassword)) {
            $error = true;
        } else {
            if($password != $confpassword){
                $error = true;
            }
        }


        if (!$error) {
            $hash_password = password_hash($password, PASSWORD_DEFAULT);
            $data = [
                "firstname" => $firstname,
                "lastname"  => $lastname,
                "email"     => $email,
                "password"  => $hash_password,
                "gender"    => $gender
            ];
            if ($user->insert($data)) {
                echo json_encode(array(
                    'status' => 'Success',
                ));
            } else {
                die(mysqli_errno(User::getConn()));
            }
        } else {
            echo json_encode(array(
                'status'   => 'Fail',
                'mmessage' => 'Validation error',
            ));
        }
    }

    public function updateUserAction($id)
    {
        $user         = new User();
        $firstname    = isset($_POST['firstname']) ? App::test_input($_POST['firstname']) : '';
        $lastname     = isset($_POST['lastname']) ? App::test_input($_POST['lastname']) : '';
        $email        = isset($_POST['email']) ? App::test_input($_POST['email']) : '';
        $password     = isset($_POST['password']) ? App::test_input($_POST['password']) : '';
        $confpassword = isset($_POST['confPassword']) ? App::test_input($_POST['confPassword']) : '';
        $gender       = isset($_POST['gender']) ? $_POST['gender'] : 'MALE';
        
        $data = [];
        if (!empty($firstname) && preg_match("/^[a-zA-Z ]*$/",$firstname) ) {
            $data['firstname'] = $firstname;
        }

        if (!empty($lastname) && preg_match("/^[a-zA-Z ]*$/",$lastname) ) {
            $data['lastname'] = $lastname;
        }

        if (!empty($email) && count($user->get()->simple(["email" => $email])->query()) === 0 && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $data['email'] = $email;
        }
        
        if (!empty($password) && $password === $confpassword) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if(count($data)) {
            $data['gender'] = $gender;
            $status = $user->update(
                $data,
                ['id' => $id]
            );
            echo json_encode(array(
                'status' => $status ? 'Success' : 'Fail',
            ));
        } else {
            echo json_encode(array(
                'status'  => 'Fail',
                'message' => 'Empty data',
            ));
        }
    }

    public function deleteUserAction($id)
    {
        $user = new User();
        $status = $user->delete([
            'id' => $id
        ]);
        echo json_encode(array(
            'status' => $status ? 'Success' : 'Fail',
        ));
    }
    
}