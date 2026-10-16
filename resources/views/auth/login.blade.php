<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akaunti - {{ config('app.name', 'PHILADELPHIA CHURCH') }}</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-mint: #e2f2e6;
            --mint-dot: #b7e4c7;
            --card-cream: #fffdfa;
            --text-black: #111827;
            --header-dark-green: #1b4332;
            --button-terracotta: #0f2747;
            --button-terracotta-hover: #0a1a30;
            --button-gradient-end: #1d4ed8;
            --button-shadow: rgba(15, 39, 71, 0.32);
            --link-navy: #1d3557;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-mint);
            background-image: radial-gradient(var(--mint-dot) 1.5px, transparent 1.5px);
            background-size: 24px 24px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        /* Top Home Link */
        .home-back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background: var(--header-dark-green);
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 200;
        }

        /* SINGLE CLEAN CARD CONTAINER (INSPIRED BY PIC 1) */
        .auth-card-wrapper {
            width: 440px;
            max-width: 100%;
            background: #ffffff;
            border-radius: 28px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .auth-card-wrapper.register-wide {
            width: 620px;
        }

        .auth-card-body {
            padding: 40px 36px 30px 36px;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .auth-brand-mark {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
        }

        .auth-brand-mark img {
            width: 58px;
            height: 58px;
            object-fit: contain;
            border-radius: 14px;
            background: #ffffff;
            border: 1.5px solid #e5e7eb;
            padding: 6px;
            box-shadow: 0 8px 22px rgba(15, 39, 71, 0.14);
        }

        .auth-brand-mark span {
            color: var(--button-terracotta);
            font-weight: 900;
            font-size: 1rem;
            letter-spacing: .3px;
            text-transform: uppercase;
        }

        .auth-header h2 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--button-terracotta);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .auth-header p {
            font-size: 0.88rem;
            color: #6b7280;
            margin-top: 6px;
            line-height: 1.4;
        }

        .input-box {
            position: relative;
            margin-bottom: 16px;
        }

        .register-fields-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 14px;
        }

        .register-fields-grid .input-box {
            margin-bottom: 0;
        }

        .register-fields-grid .input-box.full-row {
            grid-column: 1 / -1;
        }

        .input-box i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--button-terracotta);
            font-size: 1.1rem;
        }

        .input-field {
            width: 100%;
            padding: 14px 16px 14px 48px;
            background: #ffffff;
            border: 1.5px solid #e5e7eb;
            border-radius: 50px;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }

        .input-field:focus {
            border-color: var(--button-terracotta);
            box-shadow: 0 4px 15px rgba(15, 39, 71, 0.18);
        }

        .btn-orange-pill {
            width: 100%;
            background: linear-gradient(135deg, var(--button-terracotta) 0%, var(--button-gradient-end) 100%);
            color: #ffffff;
            border: none;
            padding: 15px;
            border-radius: 50px;
            font-weight: 800;
            font-size: 1.05rem;
            letter-spacing: 1px;
            cursor: pointer;
            margin-top: 8px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px var(--button-shadow);
            text-transform: uppercase;
        }

        .btn-orange-pill:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(15, 39, 71, 0.46);
        }

        .auth-toggle-link {
            text-align: center;
            font-size: 0.9rem;
            color: #4b5563;
        }

        .auth-toggle-link a {
            color: var(--text-black);
            font-weight: 700;
            text-decoration: none;
            margin-left: 4px;
        }

        .auth-toggle-link a:hover {
            color: var(--button-terracotta);
        }

        /* BOTTOM BRAND BANNER */
        .auth-bottom-banner {
            background: linear-gradient(135deg, var(--button-terracotta) 0%, var(--button-gradient-end) 100%);
            padding: 22px 20px;
            text-align: center;
            color: #ffffff;
            position: relative;
        }

        .or-badge {
            position: absolute;
            top: -18px;
            left: 50%;
            transform: translateX(-50%);
            width: 36px;
            height: 36px;
            background: #ffffff;
            border: 2px solid var(--button-terracotta);
            color: var(--button-terracotta);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 800;
        }

        .auth-bottom-banner p {
            font-size: 0.9rem;
            font-weight: 600;
            margin-top: 4px;
        }

        @media (max-width: 680px) {
            body {
                align-items: flex-start;
                padding: 12px;
                min-height: 100svh;
            }

            .home-back-btn {
                position: static;
                margin-bottom: 12px;
                width: 100%;
                justify-content: center;
                min-height: 44px;
            }

            .auth-card-wrapper {
                width: 100%;
                border-radius: 22px;
            }

            .auth-card-wrapper.register-wide {
                width: 100%;
            }

            .auth-card-body {
                padding: 28px 18px 24px;
            }

            .auth-header h2 {
                font-size: clamp(1.55rem, 8vw, 2rem);
            }

            .auth-header p {
                font-size: .86rem;
            }

            .register-fields-grid {
                grid-template-columns: 1fr;
            }

            .register-fields-grid .input-box.full-row {
                grid-column: auto;
            }

            .input-field,
            .btn-orange-pill {
                min-height: 48px;
                font-size: .95rem;
            }
        }

    </style>
</head>
<body>

    <a href="{{ url('/') }}" class="home-back-btn">
        <i class="fa-solid fa-arrow-left"></i> Rudi Mwanzo
    </a>

    <!-- Single Clean Card Container (Inspired by Pic 1) -->
    <div class="auth-card-wrapper {{ request()->routeIs('register') ? 'register-wide' : '' }}">
        
        <div class="auth-card-body">

            @if ($errors->any())
                <div style="background:#fee2e2;border:1.5px solid #f87171;color:#b91c1c;padding:12px 16px;border-radius:16px;margin-bottom:20px;font-size:0.85rem;">
                    <strong style="display:block;margin-bottom:4px;font-weight:700;"><i class="fa-solid fa-circle-exclamation"></i> Tafadhali rekebisha makosa yafuatayo:</strong>
                    <ul style="margin-left:18px;margin-top:4px;line-height:1.4;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(request()->routeIs('register'))
                <!-- REGISTER FORM -->
                <div class="auth-header">
                    <div class="auth-brand-mark">
                        <img src="{{ asset('images/eagt_logo.png') }}" alt="EAGT Logo">
                        <span>Philadelphia Church</span>
                    </div>
                    <h2>SIGN UP</h2>
                    <p>Weka taarifa zako za mwanzo kutengeneza akaunti ya Philadelphia Church</p>
                </div>

                <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Photo Upload -->
                    <div style="display:flex;flex-direction:column;align-items:center;margin-bottom:18px;">
                        <div id="photo-preview-wrap" style="width:80px;height:80px;border-radius:50%;overflow:hidden;border:3px solid var(--button-terracotta);margin-bottom:10px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;">
                            <img id="photo-preview" src="" alt="" style="width:100%;height:100%;object-fit:cover;display:none;">
                            <i class="fa-solid fa-user" id="photo-placeholder" style="font-size:2rem;color:#d1d5db;"></i>
                        </div>
                        <label for="avatar_input" style="cursor:pointer;font-size:.8rem;font-weight:700;color:var(--button-terracotta);display:flex;align-items:center;gap:6px;">
                            <i class="fa-solid fa-camera"></i> Pakia Picha Yako
                        </label>
                        <input type="file" id="avatar_input" name="avatar" accept="image/*" style="display:none;" onchange="previewPhoto(this)">
                        <span style="font-size:.72rem;color:#9ca3af;margin-top:3px;">PNG, JPG (max 2MB) — Si lazima</span>
                    </div>

                    <div class="register-fields-grid">
                        <!-- Name -->
                        <div class="input-box full-row">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" name="name" class="input-field" value="{{ old('name') }}" required placeholder="Jina lako kamili">
                        </div>

                        <!-- Email -->
                        <div class="input-box">
                            <i class="fa-solid fa-envelope"></i>
                            <input type="email" name="email" class="input-field" value="{{ old('email') }}" required placeholder="Barua Pepe / Email">
                        </div>

                        <!-- Phone -->
                        <div class="input-box">
                            <i class="fa-solid fa-phone"></i>
                            <input type="text" name="phone" class="input-field" value="{{ old('phone') }}" required placeholder="Namba ya Simu">
                        </div>

                        <!-- Password -->
                        <div class="input-box">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="password" class="input-field" required placeholder="Nywila / Password">
                        </div>

                        <!-- Confirm Password -->
                        <div class="input-box">
                            <i class="fa-solid fa-shield-halved"></i>
                            <input type="password" name="password_confirmation" class="input-field" required placeholder="Thibitisha Password">
                        </div>
                    </div>

                    <button type="submit" class="btn-orange-pill">SIGN UP NOW</button>

                    <div class="auth-toggle-link">
                        Tayari una akaunti? <a href="{{ route('login') }}">Log In Now</a>
                    </div>
                </form>

                <script>
                    function previewPhoto(input) {
                        const preview = document.getElementById('photo-preview');
                        const placeholder = document.getElementById('photo-placeholder');
                        if (input.files && input.files[0]) {
                            const reader = new FileReader();
                            reader.onload = e => {
                                preview.src = e.target.result;
                                preview.style.display = 'block';
                                placeholder.style.display = 'none';
                            };
                            reader.readAsDataURL(input.files[0]);
                        }
                    }
                </script>

            @else
                <!-- LOGIN FORM -->
                <div class="auth-header">
                    <div class="auth-brand-mark">
                        <img src="{{ asset('images/eagt_logo.png') }}" alt="EAGT Logo">
                        <span>Philadelphia Church</span>
                    </div>
                    <h2>LOG IN</h2>
                    <p>Ingia kupata taarifa mpya na matangazo ya Idara za Kanisa</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email / Phone -->
                    <div class="input-box">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" id="login_email" name="login" class="input-field" value="{{ old('login') }}" required autofocus placeholder="Barua Pepe / Namba ya Simu">
                    </div>

                    <!-- Password -->
                    <div class="input-box">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" id="login_password" name="password" class="input-field" required placeholder="PASSWORD">
                    </div>

                    <button type="submit" class="btn-orange-pill">LOGIN</button>

                    <div class="auth-toggle-link">
                        Huna akaunti bado? <a href="{{ route('register') }}">Sign Up Now</a>
                    </div>
                </form>

            @endif

        </div>

        <!-- Bottom Terracotta/Orange Banner (Pic 1 Style) -->
        <div class="auth-bottom-banner">
            <div class="or-badge">EAGT</div>
            <p>PHILADELPHIA CHURCH (EAGT)</p>
        </div>

    </div>
</body>
</html>
