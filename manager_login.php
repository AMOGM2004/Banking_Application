<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stylish Animated Login Form</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .nav{ position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;

        }

        .down{
            margin-bottom:160px;
        }
        
        body {
            padding-top: 80px; 
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #e3f2fd; /* Light blue background */
            overflow: hidden;
        }

        .login-container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 320px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            animation: containerFloat 3s ease-in-out infinite alternate; /* Floating animation */
        }

        @keyframes containerFloat {
            0% {
                transform: translateY(0);
            }
            100% {
                transform: translateY(-20px); /* Float up */
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .login-header h1 {
            color: #007BFF; /* Header color */
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #007BFF; 
            border-radius: 4px; 
            font-size: 1rem;
            transition: border-color 0.3s ease;
            background-color: #f9f9f9;
        }

        .input-group input:focus {
            outline: none;
            border-color: #0056b3; 
        }

        .input-group label {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            font-size: 1rem;
            color: #666;
            transition: all 0.3s ease;
            pointer-events: none;
            background-color: transparent;
            padding: 0 5px;
        }

        .input-group input:focus + label,
        .input-group input:not(:placeholder-shown) + label {
            top: 0;
            font-size: 0.8rem;
            color: #007BFF; 
            background-color: #ffffff;
        }

        .login-button {
            width: 50%;
            padding: 8px; /* Smaller padding */
            background: #007BFF; /* Button background color */
            color: white;
            border: none;
            border-radius: 50px; /* Ellipse shape */
            font-size: 0.9rem; /* Smaller font size */
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease; /* Added transform transition */
            display: block; /* Block display for centering */
            margin: 0 auto; /* Centering */
        }

        .login-button:hover {
            background: #0056b3; /* Darker button on hover */
            transform: scale(1.05); /* Scale effect on hover */
        }

        .login-button:active {
            transform: scale(0.95); /* Slight shrink on click */
        }

        .login-button:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Focus shadow */
        }
    </style>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="nav ">
      <nav class="section__container nav__container">
        <div class="nav__logo">Finova<span>Bank</span></div>
        <ul class="nav__links">
          <li class="link"><a href="index.html">Home</a></li>
          <li class="link"><a href="#">About Us</a></li>
          <li class="link"><a href="register.php">User</a></li>
          <li class="link"><a href="manager_login.php">Accountant</a></li>
          <li class="link"><a href="manager_login.php">Manager</a></li>
          
        </ul>
       <a href="blog_comments.html" ><button class="btn">Feedback</button></a>
      </nav>
    </header>
    
    <div class="login-container">
        <div class="login-header">
            <h1>Login</h1>
        </div>
        <form action="clerk_login.php" method="post">
            <div class="input-group">
                <input type="text" id="userid" name="userid" placeholder=" " required>
                <label for="userid">Username</label>
            </div>
            <div class="input-group">
                <input type="password" id="password" name="password" placeholder=" " required>
                <label for="password">Password</label>
            </div>
            <button type="submit" class="login-button">Login</button>
        </form>
    </div>


   

</body>
</html>
