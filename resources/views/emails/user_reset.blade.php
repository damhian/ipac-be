<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Static Template</title>
  </head>
  <header
    style="
      background-color: #edf2f7;
      padding: 2px;
      text-align: center;
      font-size: 16px;
      color: #000000;
    "
  >
    <h2 style="color: #3d4872;">IPAC</h2>
  </header>
  <br /><br />
  <section style="padding: 32px; width: 100%;">
    <article style="padding: 2px;">
      <div>
        <strong>
          <p>Password Berhasil di Reset</p>
        </strong>
        <br />
        <p>Hai alumnus dengan username: {{ $username }}, </p>
        <p>Akun anda sudah di reset password.
        <p>Silahkan login menggunakan data berikut:</p>
        <p>Email: {{ $email }}</p>
        <p>Password: {{ $password }}</p>
        <p>Sekarang anda bisa login ke akun alumni anda.</p>

        <br /><br /><strong
          >ini pesan otomatis. Mohon untuk tidak membalas</strong
        >
        <br /><br />

        <p>Regards,</p>
        <p>IPAC</p>
      </div>
    </article>
  </section>
  <footer
    style="
      background-color: #edf2f7;
      text-align: center;
      font-size: 10px;
      color: #000000;
    "
  >
    <p style="padding: 25px; color: #3d4872;">
      Copyright © 2023 IPAC, All rights reserved.
    </p>
  </footer>
</html>
