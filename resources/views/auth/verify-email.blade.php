




    <style>


        .custom-success-popup {
            border-radius: 10px;
            font-size: 10px;
            background-color: #4CAF50 !important;
            color: white !important;
        }

        .custom-error-popup {
            border-radius: 10px;
            font-size: 10px;
            background-color: #f44336 !important;
            color: white !important;
        }

        .container {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .otp-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.2);
            padding: 30px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .otp-card h2 {
            font-weight: 600;
            color: #333;
        }

        .otp-card p {
            font-size: 14px;
            color: #666;
        }

        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 5px;
            border-radius: 8px;
            border: 2px solid #ddd;
            outline: none;
            transition: all 0.3s ease;
        }

        .otp-input:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 5px rgba(106, 17, 203, 0.5);
        }

        .btn-submit {
            background: #6a11cb;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            font-size: 16px;
            color: #fff;
            font-weight: bold;
            transition: all 0.3s ease;
            cursor: not-allowed;
        }

        .btn-submit.enabled {
            cursor: pointer;
            background: #4c0aa8;
        }

        .resend-link {
            color: #2575fc;
            font-size: 14px;
            cursor: pointer;
        }

        .resend-link:hover {
            text-decoration: underline;
        }

        .alert-danger {
            padding: 10px;
            border-radius: 8px;
            font-size: 14px;
        }
    </style>




    <div class="container">
        <div class="otp-card">
            <h2>Verify OTP</h2>
                                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                        @endif
            <p>Please enter the 6-digit OTP sent to your email</p>



            <form method="POST" action="{{ route('verify.email.submit') }}">
                @csrf
                <input type="hidden" name="email" value="{{ session('email') }}">

                <div class="d-flex justify-content-center">
                    <input type="text" name="otp[]" class="otp-input" maxlength="1" required>
                    <input type="text" name="otp[]" class="otp-input" maxlength="1" required>
                    <input type="text" name="otp[]" class="otp-input" maxlength="1" required>
                    <input type="text" name="otp[]" class="otp-input" maxlength="1" required>
                    <input type="text" name="otp[]" class="otp-input" maxlength="1" required>
                    <input type="text" name="otp[]" class="otp-input" maxlength="1" required>
                </div>

                <button type="submit" class="btn btn-submit mt-4" id="submitOtpBtn" disabled>Verify OTP</button>
            </form>

            <div class="mt-3">
                <span class="resend-link" id="resendOtp">Resend OTP</span>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let inputs = document.querySelectorAll(".otp-input");
            let submitBtn = document.getElementById("submitOtpBtn");

            function checkOtpFilled() {
                let isFilled = Array.from(inputs).every(input => input.value.length === 1);
                if (isFilled) {
                    submitBtn.classList.add("enabled");
                    submitBtn.removeAttribute("disabled");
                } else {
                    submitBtn.classList.remove("enabled");
                    submitBtn.setAttribute("disabled", "true");
                }
            }

            inputs.forEach((input, index) => {
                input.addEventListener("input", function() {
                    if (this.value.length === 1 && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                    checkOtpFilled();
                });

                input.addEventListener("keydown", function(e) {
                    if (e.key === "Backspace" && index > 0 && this.value === "") {
                        inputs[index - 1].focus();
                    }
                });
            });

            document.getElementById("resendOtp").addEventListener("click", function() {
                let email = "{{ session('email') }}"; // Get the email from session

                fetch("", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            email: email
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                text: 'A new OTP has been sent to your email.',
                                position: 'bottom-end',
                                toast: true,
                                showConfirmButton: false,
                                timer: 5000,
                                timerProgressBar: true,
                                customClass: {
                                    popup: 'custom-success-popup'
                                },
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                text: 'Failed to resend OTP. Please try again.',
                                position: 'bottom-end',
                                toast: true,
                                showConfirmButton: false,
                                timer: 5000,
                                timerProgressBar: true,
                                customClass: {
                                    popup: 'custom-error-popup'
                                },
                            });
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        Swal.fire({
                            icon: 'error',
                            text: 'An error occurred. Please try again.',
                            position: 'bottom-end',
                            toast: true,
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'custom-error-popup'
                            },
                        });
                    });
            });

        });
    </script>







 