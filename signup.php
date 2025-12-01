<?php
include("connection.php");
    if($_SERVER["REQUEST_METHOD"]==='POST'){
        $name=$_POST['name'];
        $phone=$_POST['phone'];
        $email=$_POST['email'];
        $password1=$_POST['password'];
        $password2=$_POST['password1'];
        $role=0;

        $check_email="SELECT * FROM users where email='$email'";
        $check_res=mysqli_query($con,$check_email);
        if(mysqli_num_rows($check_res)>0){
            echo "<script>alert('Email already exists!!'); window.location='signup.php';</script>";
            exit;
        }
        else{
            if($password1===$password2){
                $sql="INSERT INTO users(full_name,phone_num,email,password,role) VALUES('$name','$phone','$email','$password1','$role')";
                if(mysqli_query($con,$sql)){
                    header("Location: login.php");
                }
                else{
                    echo "<script>alert('Error!!'); window.location='signup.php';</script>";
                }
            }
        }
    }
?>
<html>
    <head>
        <title>Signup | PetAdopt</title>
        <link rel="stylesheet" href="css/signup.css">
        <link rel="icon" href="Images/paw.png">
    </head>
    <body>
        <header>
            <div class="navigationbar">
                <a href="landing.php" class="leftsection" style="text-decoration:none">
                    <img src="Images/paw.png" alt="Logo" class="logo">
                    <span class=Title>PetAdopt</span>
                </a>
                <div class="MidSection">
                    <a href="landing.php" style="text-decoration: none" class=Home>Home</a>
                    <a href="browsePets.php" style="text-decoration: none" class=Browse>Browse Pets</a>
                    <a href="aboutus.php" style="text-decoration: none" class=About>About Us</a>
                    <a href="contact.php" style="text-decoration: none" class=Contact>Contact</a>
                </div>
                <div class="Login_Button">
                    <a href="login.php" style="text-decoration: none">Login</a>
                </div>
            </div>
        </header>
        <main>
            <div class="center">
                <h2>Create Account</h2>
                <form action="signup.php" method="post" onsubmit="return checkPasswords()">
                    <label for="name">Full Name:</label>
                    <input type="name" name="name" placeholder="Enter your name" required>
                    <label for="phone">Phone Number:</label>
                    <input type="number" name="phone" placeholder="Enter your number" required>
                    <label for="email">Email:</label>
                    <input type="email" name="email" placeholder="Enter your email" required>
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required>
                    <label for="password1">Confirm password:</label>
                    <input type="password" name="password1" id="password1" placeholder="Re-enter your password" required>
                    <p id="pass_error"></p>
                    <input type="submit" name="submit" id="submit" class="submit" value="Signup">
                    <script>
                        function checkPasswords() {
                        let pass = document.getElementById("password").value;
                        let pass1 = document.getElementById("password1").value;
                        let passError = document.getElementById("pass_error");

                        if (pass !== pass1) {
                            passError.innerText = "Passwords do not match";
                            passError.style = "text-align:center; font-size:20px; padding-top:10px; color:red;";
                            return false;
                        } else {
                            passError.innerText = "";
                            return true;
                        }
                        }
                    </script>
                </form>
            </div>
        </main>
    </body>
</html>