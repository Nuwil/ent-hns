<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Invision - Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mini.css/3.0.1/mini-default.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
      background: #fff;
      margin: 0;
      padding: 0;
      overflow-x: hidden;
    }

    .login-container {
      display: flex;
      min-height: 100vh;
      width: 100%;
    }

    .login-left {
      flex: 1;
      background: linear-gradient(135deg, #4a5ec7 0%, #3d4599 100%);
      color: white;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 40px;
      position: relative;
      overflow: hidden;
      width: 100%;
      height: auto;
    }

    .login-left::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -20%;
      width: 600px;
      height: 600px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      z-index: 1;
    }

    .login-left-content {
      position: relative;
      z-index: 2;
      text-align: center;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    .login-hero-image {
      max-width: 100%;
      width: 40rem;
      height: auto;
      margin-bottom: 30px;
      border-radius: 10px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    .login-left h1 {
      font-size: 28px;
      margin-bottom: 15px;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    .login-left p {
      font-size: 14px;
      opacity: 0.9;
      line-height: 1.6;
      max-width: 40rem;
    }

.login-right {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    padding: 40px;
    background: #f8f9fb;
    padding-top: 10rem;
}

    .login-form-wrapper {
      width: 100%;
      max-width: 400px;
    }

    .login-form-header {
      text-align: center;
      margin-bottom: 35px;
    }

    .login-form-header .logo {
      font-size: 28px;
      color: #4a5ec7;
      margin-bottom: 10px;
      font-weight: 600;
    }

    .login-form-header h2 {
      font-size: 24px;
      color: #333;
      margin-bottom: 8px;
      font-weight: 600;
    }

    .login-form-header p {
      color: #999;
      font-size: 13px;
    }

    .login-form-authenticator {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      width: 100%;
      height: 80%;
      padding: 1rem;
      border-radius: 10px;
    }

    a .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      color: #555;
      font-size: 13px;
      font-weight: 500;
    }

    .form-group input {
      width: 22.95rem;
      padding: 12px 14px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 14px;
      transition: border-color 0.3s ease;
    }

    .form-group input:focus {
      outline: none;
      border-color: #4a5ec7;
      box-shadow: 0 0 0 3px rgba(74, 94, 199, 0.1);
    }

    .form-group input::placeholder {
      color: #bbb;
    }

    input:not([type="button"]):not([type="submit"]):not([type="reset"]):invalid,
    input:not([type="button"]):not([type="submit"]):not([type="reset"]):focus:invalid {
      border-color: #acacac;
      width: 22.95rem;
    }


    .login-btn {
      width: 100%;
      padding: 12px;
      background: linear-gradient(135deg, #4a5ec7 0%, #3d4599 100%);
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: box-shadow 0.3s ease, transform 0.2s ease;
      margin-top: 10px;
    }

    .login-btn:hover {
      box-shadow: 0 4px 15px rgba(74, 94, 199, 0.3);
      transform: translateY(-2px);
    }

    .login-btn:active {
      transform: translateY(0);
    }

    .forgot-password {
      text-align: right;
      margin-top: 12px;
    }

    .forgot-password a {
      color: #4a5ec7;
      text-decoration: none;
      font-size: 9.5px;
      font-weight: 500;
    }

    .forgot-password a:hover {
      text-decoration: underline;
    }

    .signup-link {
      text-align: center;
      margin-top: 20px;
      padding-top: 20px;
      border-top: 1px solid #eee;
      font-size: 13px;
      color: #666;
    }

    .signup-link a {
      color: #4a5ec7;
      text-decoration: none;
      font-weight: 600;
    }

    .signup-link a:hover {
      text-decoration: underline;
    }

    .error-message {
      background: #fee;
      color: #c33;
      padding: 12px 14px;
      border-radius: 6px;
      margin-bottom: 20px;
      font-size: 13px;
      border-left: 4px solid #c33;
    }

    .api-tester-link {
      text-align: center;
      margin-top: 15px;
      font-size: 12px;
    }

    .api-tester-link a {
      color: #999;
      text-decoration: none;
    }

    .api-tester-link a:hover {
      color: #4a5ec7;
    }

    @media (max-width: 768px) {
      .login-container {
        flex-direction: column;
      }

      .login-left {
        padding: 30px;
        min-height: 40vh;
        justify-content: flex-start;
        padding-top: 50px;
      }

      .login-right {
        min-height: 60vh;
        padding: 30px 20px;
      }

      .login-form-wrapper {
        max-width: 100%;
      }

      .login-hero-image {
        width: 200px;
        margin-bottom: 20px;
      }

      .login-left h1 {
        font-size: 22px;
      }

      .login-left p {
        font-size: 13px;
      }

      .login-form-header h2 {
        font-size: 20px;
      }
    }

    @media (max-width: 480px) {
      .login-left {
        padding: 20px;
      }

      .login-right {
        padding: 20px;
      }

      .login-hero-image {
        width: 160px;
      }

      .login-left h1 {
        font-size: 18px;
      }

      .login-left p {
        font-size: 12px;
      }
    }
  </style>
</head>

<body>
  <div class="login-container">
    <!-- Left Side - Branding & Image -->
    <div class="login-left">
      <div class="login-left-content">
        <img src="{{ asset('images/login-hero.png') }}" alt="Doctor" class="login-hero-image">
        <h1>Welcome to ENT-HN Center</h1>
        <p>The Patient Management Recording System is designed specifically for Ears, Nose, Throat, Head, and Neck
          (ENT-HN) patients, enabling efficient documentation, monitoring, and management of clinical records.</p>
        <p style="margin-top: 10px; font-size: 12px; opacity: 0.8;">Cloud-based telemedicine system with integrated
          user-friendly platform</p>
      </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="login-right">
      <div class="login-form-wrapper">
        <div class="login-form-header">
          <div class="logo">
            <i class="fas fa-circle" style="color: #4a5ec7;"></i> ENT-HNS Clinic
          </div>
          <h2>Login</h2>
          <p>Enter your credentials to login to your account</p>
        </div>

        @if(session('error'))
          <div class="error-message">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
          </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="login-form-authenticator">
          @csrf

          <div class="form-group">
            <label for="username">Email</label>
            <input type="text" id="username" name="username" placeholder="example@example.com"
              value="{{ old('username') }}" required>
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required>
          </div>

          <div class="forgot-password">
            <a href="#">Forgot your password? Please contact the administrator to reset your credentials.</a>
          </div>

          <button type="submit" class="login-btn">Sign In</button>
        </form>

        <!-- <div class="api-tester-link">
          <a href="/test-api.html">API Tester</a>
        </div> -->
      </div>
    </div>
  </div>
</body>

</html>