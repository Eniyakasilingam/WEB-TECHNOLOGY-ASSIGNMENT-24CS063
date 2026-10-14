const form = document.getElementById("registrationForm");

const fields = ["name", "roll", "email", "course", "password", "document"];

function showError(id, message) {
    const field = document.getElementById(id);
    const error = document.getElementById(id + "Error");

    error.textContent = message;

    if (field) {
        field.classList.toggle("invalid", Boolean(message));
        field.setAttribute("aria-invalid", Boolean(message));
    }
}

function clearErrors() {
    fields.forEach(id => showError(id, ""));
    document.getElementById("successMessage").textContent = "";
}

function checkPasswordStrength(password) {
    let score = 0;

    if (password.length >= 8) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[a-z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;

    const bar = document.getElementById("strengthBar");
    const text = document.getElementById("strengthText");

    const levels = [
        { width: "0%", label: "Password strength", color: "#ff617d" },
        { width: "20%", label: "Very weak", color: "#ff617d" },
        { width: "40%", label: "Weak", color: "#ff9861" },
        { width: "60%", label: "Fair", color: "#f5cf68" },
        { width: "80%", label: "Strong", color: "#72dca4" },
        { width: "100%", label: "Very strong", color: "#55efab" }
    ];

    const level = levels[password ? score : 0];

    bar.style.width = level.width;
    bar.style.background = level.color;
    text.textContent = level.label;

    return score;
}

document.getElementById("password").addEventListener("input", function () {
    checkPasswordStrength(this.value);
    showError("password", "");
});

fields.forEach(id => {
    const field = document.getElementById(id);

    field.addEventListener("input", () => {
        showError(id, "");
        document.getElementById("successMessage").textContent = "";
    });

    field.addEventListener("change", () => {
        showError(id, "");
    });
});

form.addEventListener("submit", function (event) {
    event.preventDefault();
    clearErrors();

    const name = document.getElementById("name").value.trim();
    const roll = document.getElementById("roll").value.trim();
    const email = document.getElementById("email").value.trim();
    const course = document.getElementById("course").value;
    const password = document.getElementById("password").value;
    const file = document.getElementById("document").files[0];

    let valid = true;

    // Name validation
    if (name.length < 3 || !/^[\p{L}][\p{L}\s.'-]*$/u.test(name)) {
        showError("name", "Enter a valid name with at least 3 characters.");
        valid = false;
    }

    // Roll number validation
    if (!/^[A-Za-z0-9/-]{2,20}$/.test(roll)) {
        showError("roll", "Enter a valid roll number.");
        valid = false;
    }

    // Email validation
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailPattern.test(email)) {
        showError("email", "Enter a valid email address.");
        valid = false;
    }

    // Course validation
    if (!course) {
        showError("course", "Please select a course.");
        valid = false;
    }

    // Password validation
    const passwordScore = checkPasswordStrength(password);

    if (password.length < 8 ||
        !/[A-Z]/.test(password) ||
        !/[a-z]/.test(password) ||
        !/[0-9]/.test(password) ||
        !/[^A-Za-z0-9]/.test(password)) {

        showError(
            "password",
            "Use 8+ characters with uppercase, lowercase, a number and a symbol."
        );

        valid = false;
    }

    // File validation
    if (!file) {
        showError("document", "Please upload a student document.");
        valid = false;
    } else {
        const allowedTypes = [
            "application/pdf",
            "image/jpeg",
            "image/png"
        ];

        if (!allowedTypes.includes(file.type)) {
            showError("document", "Only PDF, JPG and PNG files are allowed.");
            valid = false;
        } else if (file.size > 2 * 1024 * 1024) {
            showError("document", "File size must not exceed 2 MB.");
            valid = false;
        }
    }

    if (!valid) {
        const firstInvalid = form.querySelector(".invalid");

        if (firstInvalid) {
            firstInvalid.focus();
        }

        return;
    }

    document.getElementById("successMessage").textContent =
        "✓ Application validated successfully!";

    // Demo only: no data is stored or uploaded to a server.
    form.reset();
    checkPasswordStrength("");

    fields.forEach(id => {
        const field = document.getElementById(id);

        field.classList.remove("invalid");
        field.removeAttribute("aria-invalid");
    });
});