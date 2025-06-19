<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KasPlus Onboarding</title>
  @vite('resources/css/app.css') 
  <style>
    body {
      margin: 0;
      font-family: 'Satoshi', sans-serif;
      background-color: white;
      color: #131951;
      height: 100vh;
      width: 100vw;
      position: relative;
      overflow: hidden;
    }

    .main-content-group {
      position: absolute;
      top: 40%;
      left: 50%;
      transform: translate(-50%, -50%);
      display: flex;
      flex-direction: column;
      align-items: center;
      width: 100%;
      padding: 0 20px;
      box-sizing: border-box;
    }

    .logo-and-text-group {
      display: flex;
      align-items: center;
      /* UBAH INI: Dekatkan jarak ke subjudul */
      margin-bottom: 8px; /* Diubah dari 12px menjadi 8px */
    }

    .logo-container {
      width: 65px;
      height: 65px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 6px;
    }

    .logo-container img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }

    .title {
      margin-top: 0;
      /* UBAH INI: Perbesar font-size dan sesuaikan line-height agar sejajar dengan logo */
      font-size: 62px; /* Diubah dari 44px menjadi 55px */
      font-weight: 750;
      text-align: center;
      color: #131951;
      line-height: 55px; /* Diubah dari 48px menjadi 55px (sama dengan font-size) */
    }

    .subtitle {
      /* UBAH INI: Dekatkan jarak dari grup judul/logo */
      margin-top: -3px; /* Diubah dari 5px menjadi 0px */
      font-size: 18px;
      font-weight: 500;
      text-align: center;
      color: #666;
      line-height: 24px;
    }

    .btn-start {
      position: absolute;
      bottom: 60px;
      left: 50%;
      transform: translateX(-50%);
      
      width: 335px;
      height: 50px;
      padding: 0;
      border-radius: 25px;
      
      line-height: 50px;
      font-weight: 500;
      font-size: 20px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      
      background-color: #131951;
      color: white;
      border: none;
      
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
    }

    .btn-start:hover {
      opacity: 0.9;
      background-color: #0c1032;
      color: white;
      transform: translateY(-3px) translateX(-50%);
      box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.2);
    }
  </style>
</head>
<body>
  <div class="main-content-group">
    <div class="logo-and-text-group">
      <div class="logo-container">
        <img src="{{ asset('images/kasplus_logo.png') }}" alt="KasPlus Logo">
      </div>
      <div class="title">KasPlus</div>
    </div>
    <div class="subtitle">Partner cerdas untuk bisnis Anda</div>
  </div>

  <a href="{{ route('login') }}" class="btn-start">Mulai Sekarang</a>
</body>
</html>