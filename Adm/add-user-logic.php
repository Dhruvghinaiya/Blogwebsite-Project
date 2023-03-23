<?php
    session_start();
    require 'config/database.php';

    //get  form data if  submit button  was clicked

    if(isset($_POST['submit']))
    {
        $firstname=filter_var($_POST['firstname'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $lastname=filter_var($_POST['lastname'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $username=filter_var($_POST['username'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email=filter_var($_POST['email'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password=filter_var($_POST['password'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $confirmpassword=filter_var($_POST['confirmpassword'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        // $is_admin = filter_var($_POST['userrole'],FILTER_SANITIZE_NUMBER_INT);
        $avatar= $_FILES['avatar'];

        //validate input values
        if(!$firstname){
            $_SESSION['add-user'] = "Please Enter Your First Name";
        }elseif(!$lastname){
            $_SESSION['add-user'] ="Please Enter Your last Name ";
        }
        elseif(!$username){
            $_SESSION['add-user'] ="Please Enter Your Username ";
        }
        elseif(!$email){
            $_SESSION['add-user'] ="Please Enter Your a Valid Email ";
        } 
        elseif(strlen ($password) < 8 || strlen ($confirmpassword) < 8 ){
            $_SESSION['add-user'] ="Please Should Be 8+ Character ";
        }
        elseif(!$avatar['name']){
            $_SESSION['add-user'] ="Please add avatar ";
        }
        else{
            //check if password don' t match
            if($password !== $confirmpassword){
                $_SESSION['signup'] = "Password Do Not Match";
            }else{
                //hase password
                //$hashed_password = password_hash($password,PASSWORD_DEFAULT);

                //check if username or email already exit in database
                $user_check_query ="SELECT * FROM users WHERE username='$username' OR email='$email'";
                $user_check_result=mysqli_query($con,$user_check_query);
                if(mysqli_num_rows($user_check_result) > 0){
                    $_SESSION['add-user'] = 'Username Or Email Already Exits';
                }else{
                    //work on avatar
                    $time = time(); //make ex=ach image name unique usin current timestamp
                    $avatar_name = $time . $avatar['name'];
                    $avatar_tmp_name = $avatar['tmp_name'];
                    $avatar_destination_path ='../images/'.$avatar_name;

                    //make sure file is an image
                    $allowed_files =['png','jpg','jpeg'];
                    $textention = explode('.',$avatar_name);
                    $textention = end ($textention);
                    if(in_array($textention,$allowed_files)){
                        //make sure image is not too large (1mb+)
                        if($avatar['size'] < 20000000){
                            //upload avatar
                            move_uploaded_file($avatar_tmp_name,$avatar_destination_path);
                        }
                        else{
                            $_SESSION['add-user'] = "File Size Too Big. Should Be Less Than 2mb";
                         }
                       }   else{
                                $_SESSION['add-user'] = "File Should ne png ,jpg, or jpeg";
                            }
                        
                    
                }

            }
        }
            // redirect back to signup page eif there was any problem
            if(isset($_SESSION['add-user'])){
                //pass form data back to signup page
                $_SESSION['add-user-data'] = $_POST;
                header('location:/blog/admin/add-user.php');
                die();
            }else{
                //insert new user into users table
                $insert_user_query ="INSERT INTO users (firstname,lastname,username,email,password,avatar,is_admin) VALUES('$firstname','$lastname','$username','$email','$password','$avatar_name','is_admin=$is_admin')";
                $insert_user_result = mysqli_query($con,$insert_user_query); 
                if(!mysqli_errno($con)){
                    //redirect to login page with success message
                    $_SESSION['add-user-success'] = " New User $firstname $lastname added successfully..";
                    header('location:/blog/admin/manage-users.php');
                    die();
                }
            }

    }
    
    else

    {
        //if button wasn' t clicked bounce back to signup page
        header('location:localhost/blog/admin/add-user.php');
        die();
    }
?>