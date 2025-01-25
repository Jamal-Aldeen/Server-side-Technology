function validateForm() {
    let valid = true;

    const firstname = document.getElementById('firstname').value.trim();
    if (firstname === "") {
        document.getElementById('firstname_error').textContent = "First Name is required.";
        document.getElementById('firstname_error').style.display = "block";
        valid = false;
    } else {
        document.getElementById('firstname_error').style.display = "none";
    }

    const lastname = document.getElementById('lastname').value.trim();
    if (lastname === "") {
        document.getElementById('lastname_error').textContent = "Last Name is required.";
        document.getElementById('lastname_error').style.display = "block";
        valid = false;
    } else {
        document.getElementById('lastname_error').style.display = "none";
    }

    const address = document.getElementById('address').value.trim();
    if (address === "") {
        document.getElementById('address_error').textContent = "Address is required.";
        document.getElementById('address_error').style.display = "block";
        valid = false;
    } else {
        document.getElementById('address_error').style.display = "none";
    }

    const country = document.getElementById('country').value;
    if (country === "") {
        document.getElementById('country_error').textContent = "Country is required.";
        document.getElementById('country_error').style.display = "block";
        valid = false;
    } else {
        document.getElementById('country_error').style.display = "none";
    }

    const gender = document.querySelector('input[name="gender"]:checked');
    if (!gender) {
        document.getElementById('gender_error').textContent = "Gender is required.";
        document.getElementById('gender_error').style.display = "block";
        valid = false;
    } else {
        document.getElementById('gender_error').style.display = "none";
    }

    const skills = document.querySelectorAll('input[name="skills[]"]:checked');
    if (skills.length < 1) {
        document.getElementById('skills_error').textContent = "At least one skill is required.";
        document.getElementById('skills_error').style.display = "block";
        valid = false;
    } else {
        document.getElementById('skills_error').style.display = "none";
    }

    const username = document.getElementById('username').value.trim();
    if (username === "") {
        document.getElementById('username_error').textContent = "Username is required.";
        document.getElementById('username_error').style.display = "block";
        valid = false;
    } else {
        document.getElementById('username_error').style.display = "none";
    }

    const password = document.getElementById('password').value.trim();
    if (password === "") {
        document.getElementById('password_error').textContent = "Password is required.";
        document.getElementById('password_error').style.display = "block";
        valid = false;
    } else {
        document.getElementById('password_error').style.display = "none";
    }


    const department = document.getElementById('department').value.trim();
    if (department === "") {
        document.getElementById('department_error').textContent = "Department is required.";
        document.getElementById('department_error').style.display = "block";
        valid = false;
    } else {
        document.getElementById('department_error').style.display = "none";
    }

    return valid;
}