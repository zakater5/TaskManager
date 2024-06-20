<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: #2b2b2b;
        }
    </style>
</head>
<body>
    <div class="content">
        <div id="placeholder" class="login-div">
            <form action="/login" method="post">
                <div class="login-title-div">
                    <h1 class="login-title">LOGIN</h1>
                </div>
                <div class="login-textbox">
                    <label for="username" class="login-label">Username</label>
                    <input type="text" name="username" class="login-input" required>
                </div>
                <div class="login-textbox">
                    <label for="password" class="login-label">Password</label>
                    <input type="password" name="password" class="login-input" required>
                </div>
                <?php if (isset($LoginError)): ?>
                    <div class="login-error">
                        <p><?= $LoginError ?></p>
                    </div>
                <?php endif; ?>
                <div style="margin-top: 1.5%;">
                    <input type="submit" value="Login" class="login-submit">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
