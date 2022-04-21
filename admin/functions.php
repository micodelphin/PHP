<?php

function redirect($location){

  return header("location:". $location);
}



function users_online(){
   
if(isset($_GET['onlineusers'])){    
    

global $connection;
     
if(!$connection){
    
  session_start();
    
  include ("../includes/db.php"); 
    
      $session = session_id();
      $time = time();
      $time_out_in_seconds = 05;
      $time_out = $time - $time_out_in_seconds;

      $query = "SELECT * FROM users_online WHERE session = '$session'";        
      $send_query = mysqli_query($connection,$query);    
      $count = mysqli_num_rows($send_query);

          if($count == NULL){

          mysqli_query($connection,"INSERT INTO users_online(session,time) VALUES('$session','$time')");    

          }else{

          mysqli_query($connection,"UPDATE users_online SET time ='$time' WHERE session ='$session'");    

          }

      $user_online_query = mysqli_query($connection,"SELECT * FROM users_online WHERE time > '$time_out'");    
      echo $count_users = mysqli_num_rows($user_online_query);         

}    
 
} //get request isset()
   
} 

users_online();




function confirmQuery($result){
     global $connection;
    
  if(!$result){
    
   die("QUERY FAILED" . mysqli_error($connection)); 
    
    
}      
    
}



function insert_categories(){
    
       global $connection;

        if(isset($_POST['submit'])){
          $cat_title = $_POST['cat_title'];  
    
       if($cat_title =="" || empty($cat_title)){
           
        echo "this field should not be empty";   
           
       }else{
           
      $query = "INSERT INTO categories(cat_title) ";
      $query .="VALUE('{$cat_title}')";       
           
    $create_category_query = mysqli_query($connection, $query);   
           
       
        if(!$create_category_query){
                     
        die("QUERY FAILED");             
                     
                 }
       }
            
        }
             
}

//find all categories

function findAllcategories(){
      global $connection;
    
$query = "SELECT * FROM categories ";
$select_categories = mysqli_query($connection,$query);                               
while($row = mysqli_fetch_assoc($select_categories)){
$cat_id = $row['cat_id'];
$cat_title = $row['cat_title'];    
    
echo "<tr>";
echo "<td>{$cat_id}</td>";
echo "<td>{$cat_title}</td>";
echo "<td><a href='categories.php?delete={$cat_id}'>delete</a></td>"; 
echo "<td><a href='categories.php?edit={$cat_id}'>Edit</a></td>";     
echo "</tr>";
}                                                  

}

function deleteCategories(){
global $connection;

if(isset($_GET['delete'])){
$the_cat_id = $_GET['delete'];
$query = "DELETE FROM categories WHERE cat_id = {$the_cat_id} ";
$delete_query = mysqli_query($connection, $query);
header("location: categories.php");

    }                    
 
}



// this function is for the dashboard on index in admin

function  recordcount($table){

  global $connection;

          $query = "SELECT * FROM " . $table;
          $select_all_post = mysqli_query($connection,$query);
          $result = mysqli_num_rows($select_all_post);

     confirmQuery($result);

     return $result;

}


function is_admin($username){

global $connection;

$query = "SELECT user_role FROM users WHERE username = '$username'";

$result = mysqli_query($connection,$query);
confirmQuery($result);
$row = mysqli_fetch_array($result);

if($row['user_role'] == 'admin'){

  return true;

}else{

  return false;
}



}


function username_exists($username){

global $connection;

$query = "SELECT username FROM users WHERE username = '$username'";

$result = mysqli_query($connection,$query);
confirmQuery($result);

if(mysqli_num_rows($result) > 0){

  return true;

}else{

  return false;
}

}


function email_exists($email){

global $connection;

    $query = "SELECT user_email FROM users WHERE user_email = '$user_email'";

    $result = mysqli_query($connection,$query);
    confirmQuery($result);

  if(mysqli_num_rows($result) > 0){

  return true;
}else{

  return false;
}

}


function register_user($firstname,$lastname,$username,$email,$password){

global $connection;

    $firstname = mysqli_real_escape_string($connection,$firstname);
    $lastname = mysqli_real_escape_string($connection,$lastname);    
    $username = mysqli_real_escape_string($connection,$username);
    $email    = mysqli_real_escape_string($connection,$email);
    $password = mysqli_real_escape_string($connection,$password); 



    $password = password_hash($password,PASSWORD_BCRYPT, array('cost' => 12));    

    $query = "INSERT INTO users (user_firstname,user_lastname,username,user_email,user_password,user_role) ";
    $query .= "VALUES('{$firstname}','{$lastname}' ,'{$username}', '{$email}', '{$password}','subscriber')";

    $registrater_user_query = mysqli_query($connection,$query);
    confirmQuery($registrater_user_query);

   
}

function login_user($username,$password){

global $connection;

              $username = trim($username);
              $password = trim($password);

              $username = mysqli_real_escape_string($connection,$username);
              $password = mysqli_real_escape_string($connection,$password);


              $query = "SELECT * FROM users WHERE username = '{$username}' ";
              $select_query_user = mysqli_query($connection,$query);    
    
  if(!$select_query_user){

      die("QUERY FAILED" . mysqli_error($connectoin));
  }
    
  while($row = mysqli_fetch_array($select_query_user)){
        
            $db_user_id = $row['user_id'];
            $db_username = $row['username'];
            $db_user_password = $row['user_password'];    
            $db_user_firstname = $row['user_firstname'];
            $db_user_lastname = $row['user_lastname'];
            $db_user_role = $row['user_role'];
        
  }
    
  if(password_verify($password,$db_user_password)){
 
            $_SESSION['username'] = $db_username;
            $_SESSION['firstname'] = $db_user_firstname;
            $_SESSION['lastname'] = $db_user_lastname;
            $_SESSION['user_role'] = $db_user_role;    
       

  redirect("/cms/admin");   
    
 } 
  else{
    
  redirect("/cms/index");     
    
 }  

}


?>