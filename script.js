// GET ELEMENTS

const loginView = document.getElementById("loginView");
const registerView = document.getElementById("registerView");

const showRegister = document.getElementById("showRegister");
const showLogin = document.getElementById("showLogin");

const loginForm = document.getElementById("loginForm");
const registerForm = document.getElementById("registerForm");

const message = document.getElementById("message");

const plumberFields = document.getElementById("plumberFields");


// SWITCH BETWEEN LOGIN AND REGISTER

showRegister.addEventListener("click", function () {

    loginView.classList.remove("active");
    registerView.classList.add("active");

    clearMessage();

    window.scrollTo({ top: 0, behavior: "smooth" });

});


showLogin.addEventListener("click", function () {

    registerView.classList.remove("active");
    loginView.classList.add("active");

    clearMessage();

    window.scrollTo({ top: 0, behavior: "smooth" });

});


// DISPLAY MESSAGE

function showMessage(text, type) {

    message.textContent = text;
    message.className = "message " + type;

}


function clearMessage() {

    message.textContent = "";
    message.className = "message";

}


// PASSWORD VISIBILITY

const passwordButtons = document.querySelectorAll(".toggle-password");

passwordButtons.forEach(function (button) {

    button.addEventListener("click", function () {

        const inputId = button.dataset.target;
        const passwordInput = document.getElementById(inputId);

        if (passwordInput.type === "password") {

            passwordInput.type = "text";
            button.textContent = "Hide";

        } else {

            passwordInput.type = "password";
            button.textContent = "Show";

        }

    });

});


// ROLE SELECTION

const roleCards = document.querySelectorAll(".role-card");
const roleInputs = document.querySelectorAll('input[name="role"]');

roleInputs.forEach(function (input) {

    input.addEventListener("change", function () {

        roleCards.forEach(function (card) {
            card.classList.remove("selected");
        });

        input.closest(".role-card").classList.add("selected");


        if (input.value === "plumber") {

            plumberFields.classList.remove("hidden");

        } else {

            plumberFields.classList.add("hidden");

        }

        clearMessage();

    });

});


// VALIDATION FUNCTIONS


// CHECK FIRST NAME AND LAST NAME
// Only English letters A-Z and a-z are accepted.

function validateName(name) {

    const namePattern = /^[a-zA-Z]+$/;

    return namePattern.test(name);

}


// CHECK EMAIL

function validateEmail(email) {

    const emailPattern =
        /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    return emailPattern.test(email);

}


// CHECK TUNISIAN PHONE NUMBER

function validatePhone(phone) {

    const cleanPhone = phone.replace(/[\s-]/g, "");

    const phonePattern = /^(?:\+216|00216)?[2-9][0-9]{7}$/;

    return phonePattern.test(cleanPhone);

}


// CHECK PASSWORD

function validatePassword(password) {

    const passwordPattern =
        /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^a-zA-Z0-9]).{8,}$/;

    return passwordPattern.test(password);

}


// LOGIN FORM

loginForm.addEventListener("submit", function (event) {

    event.preventDefault();

    clearMessage();

    const identifier =
        document.getElementById("loginIdentifier").value.trim();

    const password =
        document.getElementById("loginPassword").value;


    // CHECK EMPTY FIELDS

    if (identifier === "" || password === "") {

        showMessage(
            "Please fill in all fields.",
            "error"
        );

        return;

    }


    // DEMO ONLY - NO DATABASE CONNECTION

    showMessage(
        "Login form validated. Database authentication is not connected yet.",
        "success"
    );

});


// REGISTRATION FORM

registerForm.addEventListener("submit", function (event) {

    event.preventDefault();

    clearMessage();


    // GET VALUES

    const firstName =
        document.getElementById("firstName").value.trim();

    const lastName =
        document.getElementById("lastName").value.trim();

    const email =
        document.getElementById("registerEmail").value.trim();

    const phone =
        document.getElementById("registerPhone").value.trim();

    const password =
        document.getElementById("registerPassword").value;

    const confirmPassword =
        document.getElementById("confirmPassword").value;

    const selectedRole =
        document.querySelector('input[name="role"]:checked').value;


    // CHECK EMPTY COMMON FIELDS

    if (
        firstName === "" ||
        lastName === "" ||
        email === "" ||
        phone === "" ||
        password === "" ||
        confirmPassword === ""
    ) {

        showMessage(
            "Please fill in all required fields.",
            "error"
        );

        return;

    }


    // CHECK FIRST NAME

    if (!validateName(firstName)) {

        showMessage(
            "First name must contain English letters only (A-Z).",
            "error"
        );

        return;

    }


    // CHECK LAST NAME

    if (!validateName(lastName)) {

        showMessage(
            "Last name must contain English letters only (A-Z).",
            "error"
        );

        return;

    }


    // CHECK EMAIL

    if (!validateEmail(email)) {

        showMessage(
            "Please enter a valid email address.",
            "error"
        );

        return;

    }


    // CHECK PHONE NUMBER

    if (!validatePhone(phone)) {

        showMessage(
            "Please enter a valid Tunisian phone number.",
            "error"
        );

        return;

    }


    // CHECK PASSWORD

    if (!validatePassword(password)) {

        showMessage(
            "Password must contain at least 8 characters, one uppercase letter, one lowercase letter, one number and one special character.",
            "error"
        );

        return;

    }


    // CHECK PASSWORD CONFIRMATION

    if (password !== confirmPassword) {

        showMessage(
            "Passwords do not match. Please try again.",
            "error"
        );

        return;

    }


    // PLUMBER VALIDATION

    if (selectedRole === "plumber") {

        const serviceArea =
            document.getElementById("serviceArea").value;

        const experience =
            document.getElementById("experience").value.trim();


        // CHECK GOVERNORATE

        if (serviceArea === "") {

            showMessage(
                "Please select your service area.",
                "error"
            );

            return;

        }


        // CHECK EXPERIENCE

        if (experience === "") {

            showMessage(
                "Please enter your years of experience.",
                "error"
            );

            return;

        }


        const experiencePattern = /^(0|[1-9][0-9]?)$/;

        if (!experiencePattern.test(experience)) {

            showMessage(
                "Experience must be a whole number between 0 and 70.",
                "error"
            );

            return;

        }


        if (Number(experience) > 70) {

            showMessage(
                "Experience cannot exceed 70 years.",
                "error"
            );

            return;

        }

    }   // end of plumber validation


    // EVERYTHING IS VALID: clean the phone number, then send the form to PHP

    document.getElementById("registerPhone").value =
        phone.replace(/[\s-]/g, "");

    registerForm.submit();

});