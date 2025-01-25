<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php
    if (isset($_GET['error'])) {
        $error = urldecode($_GET['error']);
        echo "<p style='color: red; text-align: center;'>Error: $error</p>";
    }
    ?>
    <div class="form-container">
        <h2>Registration Form</h2>
        <form action="process.php" method="POST" onsubmit="return validateForm()">
            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname">
            <div class="error" id="firstname_error"></div>

            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname">
            <div class="error" id="lastname_error"></div>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address">
            <div class="error" id="address_error"></div>
            
            <label for="country">Country:</label>
            <select id="country" name="country">
                <option value="">--Select Country--</option>
                <option value="Portside">Portside</option>
                <option value="Alexandria">Alexandria</option>
                <option value="Ismailia">Ismailia</option>
                <option value="Cairo">Cairo</option>
            </select>
            <div class="error" id="country_error"></div>

            <label>Gender:</label>
            <div class="radio-group">
                <label><input type="radio" name="gender" value="Male"> Male</label>
                <label><input type="radio" name="gender" value="Female"> Female</label>
            </div>
            <div class="error" id="gender_error"></div>

            <label for="skills">Skills:</label>
            <div class="checkbox-group">
                <label><input type="checkbox" name="skills[]" value="PHP"> PHP</label>
                <label><input type="checkbox" name="skills[]" value="JavaScript"> JavaScript</label>
                <label><input type="checkbox" name="skills[]" value="HTML"> HTML</label>
                <label><input type="checkbox" name="skills[]" value="CSS"> CSS</label>
            </div>
            <div class="error" id="skills_error"></div>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username">
            <div class="error" id="username_error"></div>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password">
            <div class="error" id="password_error"></div>

            <label for="department">Department:</label>
            <input type="text" id="department" name="department">
            <div class="error" id="department_error"></div>

            <button type="submit">Register</button>
        </form>
    </div>
    <script src="script.js"></script>
</body>
</html>