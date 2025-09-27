<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify OTP</title>
<link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    body {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .otp-card {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        padding: 30px 25px;
        max-width: 420px;
        width: 100%;
        text-align: center;
    }

    .otp-card h2 {
        font-weight: 700;
        margin-bottom: 10px;
        color: #333;
    }

    .otp-card p {
        font-size: 14px;
        color: #666;
        margin-bottom: 20px;
    }

    .otp-input {
        width: 50px;
        height: 50px;
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        margin: 0 5px;
        border-radius: 10px;
        border: 2px solid #ddd;
        outline: none;
        transition: all 0.3s ease;
    }

    .otp-input:focus {
        border-color: #6a11cb;
        box-shadow: 0 0 10px rgba(106, 17, 203, 0.4);
    }

    /* Button default style */
    .btn-submit {
        border: none;
        padding: 12px;
        width: 100%;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        margin-top: 15px;
        transition: all 0.3s ease;
        color: #fff;
        background: #6a11cb;
    }

    /* Inactive state */
    .btn-submit.inactive {
        cursor: not-allowed;
        
    }

    /* Active state */
    .btn-submit.active {
     transition: all 0.3s ease;
        color: #fff;
        background: #6a11cb;
        cursor: pointer;
    }

    /* Active hover effect */
    .btn-submit.inactive:hover {
    transition: all 0.3s ease;
        color: #fff;
        background: #6a11cb;
    }
    .btn-submit.active:hover {
      transition: all 0.3s ease;
        color: #fff;
        background: #6a11cb;
    }

    .resend-link {
        color: #2575fc;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .resend-link:hover {
        text-decoration: underline;
    }

    .alert {
        border-radius: 8px;
        font-size: 14px;
        padding: 10px;
    }
</style>
</head>
<body>

<div class="otp-card">
    <h2>Verify OTP</h2>
    <p>Please enter the 6-digit OTP sent to your email</p>

    <!-- Laravel alerts -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('verify.email.submit') }}">
        @csrf
        <input type="hidden" name="email" value="{{ session('email') }}">
        <div class="d-flex justify-content-center mb-3">
            @for ($i = 0; $i < 6; $i++)
                <input type="text" name="otp[]" class="otp-input" maxlength="1" required>
            @endfor
        </div>
        <button type="submit" class="btn btn-submit inactive" id="submitOtpBtn">Verify OTP</button>
    </form>

    <div class="mt-3">
        <span class="resend-link" id="resendOtp">Resend OTP</span>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    let inputs = document.querySelectorAll(".otp-input");
    let submitBtn = document.getElementById("submitOtpBtn");

    function checkOtpFilled() {
        let filled = Array.from(inputs).every(i => i.value.length === 1);
        if (filled) {
            submitBtn.classList.add("active");
            submitBtn.classList.remove("inactive");
        } else {
            submitBtn.classList.add("inactive");
            submitBtn.classList.remove("active");
        }
    }

    inputs.forEach((input, i) => {
        input.addEventListener("input", function() {
            if (this.value.length === 1 && i < inputs.length - 1) inputs[i+1].focus();
            checkOtpFilled();
        });

        input.addEventListener("keydown", function(e) {
            if (e.key === "Backspace" && i > 0 && this.value === "") inputs[i-1].focus();
            checkOtpFilled();
        });
    });

    checkOtpFilled();

    // Resend OTP
    document.getElementById("resendOtp").addEventListener("click", function() {
        let email = "{{ session('email') }}";
        fetch("{{ route('resend.otp') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({email: email})
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                Swal.fire({
                    icon: 'success',
                    text: 'A new OTP has been sent to your email.',
                    position: 'bottom-end',
                    toast: true,
                    showConfirmButton: false,
                    timer: 4000,
                    customClass: { popup: 'custom-success-popup' }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    text: 'Failed to resend OTP.',
                    position: 'bottom-end',
                    toast: true,
                    showConfirmButton: false,
                    timer: 4000,
                    customClass: { popup: 'custom-error-popup' }
                });
            }
        }).catch(() => {
            Swal.fire({
                icon: 'error',
                text: 'An error occurred. Please try again.',
                position: 'bottom-end',
                toast: true,
                showConfirmButton: false,
                timer: 4000,
                customClass: { popup: 'custom-error-popup' }
            });
        });
    });

    // Highlight invalid OTP after failed submit
    @if(session('invalid_otp'))
        Swal.fire({
            icon: 'error',
            text: '{{ session("invalid_otp") }}',
            position: 'bottom-end',
            toast: true,
            showConfirmButton: false,
            timer: 5000,
            customClass: { popup: 'custom-error-popup' }
        });
    @endif
});
</script>

</body>
</html>
