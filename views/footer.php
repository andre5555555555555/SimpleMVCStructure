    </main>
   <div class="Footer" id="Footer">
    <h1>About Us</h1>
    <p>Email: romlianderandreidampilag@gmail.com</p>
    </div>
    <script>
        (function () {
            const allowedExtensions = ["jpg", "jpeg", "png", "webp"];
            const maxFileSize = 5 * 1024 * 1024;

            function setValidationMessage(form, message) {
                const box = form.querySelector(".validation-message");
                if (!box) return;
                box.hidden = !message;
                box.textContent = message || "";
            }

            function getLabel(input) {
                return input.dataset.label || input.name || "This field";
            }

            function validateFiles(form) {
                const fileInputs = form.querySelectorAll('input[type="file"]');

                for (const input of fileInputs) {
                    const label = getLabel(input);
                    const files = Array.from(input.files || []);

                    if (input.required && files.length === 0) {
                        return label + " is required.";
                    }

                    for (const file of files) {
                        const ext = (file.name.split(".").pop() || "").toLowerCase();

                        if (!allowedExtensions.includes(ext)) {
                            return label + " must be JPG, JPEG, PNG, or WEBP.";
                        }

                        if (file.size > maxFileSize) {
                            return label + " must not be larger than 5MB.";
                        }
                    }
                }

                return "";
            }

            function validateForm(form) {
                const type = form.dataset.validateForm || "";

                for (const input of form.querySelectorAll("input, textarea, select")) {
                    if (input.type === "hidden" || input.type === "submit" || input.type === "button") {
                        continue;
                    }

                    const label = getLabel(input);
                    const value = (input.value || "").trim();

                    if (input.required && value === "" && input.type !== "file") {
                        return label + " is required.";
                    }

                    if (input.minLength > 0 && value !== "" && value.length < input.minLength) {
                        return label + " must be at least " + input.minLength + " characters.";
                    }

                    if (input.maxLength > 0 && value.length > input.maxLength) {
                        return label + " must not exceed " + input.maxLength + " characters.";
                    }

                    if (input.type === "number" && value !== "" && Number(value) <= 0) {
                        return label + " must be greater than 0.";
                    }
                }

                if (type === "register") {
                    const password = form.querySelector('input[name="password"]');
                    const confirm = form.querySelector('input[name="confirm_password"]');

                    if (password && confirm && password.value !== confirm.value) {
                        return "Passwords do not match.";
                    }
                }

                if (type === "item-create" || type === "item-edit") {
                    const checkedCategories = form.querySelectorAll('input[name="categories[]"]:checked');
                    if (checkedCategories.length === 0) {
                        return "Select at least one category.";
                    }

                    const fileError = validateFiles(form);
                    if (fileError) {
                        return fileError;
                    }
                }

                return "";
            }

            document.querySelectorAll("form[data-validate-form]").forEach(function (form) {
                form.addEventListener("submit", function (event) {
                    const message = validateForm(form);
                    setValidationMessage(form, message);

                    if (message) {
                        event.preventDefault();
                    }
                });
            });
        })();
    </script>
</body>
</html>
