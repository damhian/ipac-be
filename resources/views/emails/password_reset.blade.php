<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Static Template</title>
  </head>
  <header
    style="
      background-color: #ffffff;
      padding: 2px;
      text-align: center;
      font-size: 16px;
      color: #000000;
    "
  >
    <table style="table-layout: fixed; width: 100%;">
      <tr>
        <td style="text-align: left; padding-left: 2%;">
          <img
            id="imglogo"
            alt=""
            src="images/LogoIPAC-c60f8341.svg"
            width="155px"
            height="48px"
          />
        </td>
        <td style="text-align: center;"><h2>IPAC</h2></td>
        <td></td>
      </tr>
    </table>
  </header>
  <br /><br />
  <section style="padding: 10px; width: 100%;">
    <article style="padding: 2px;">
      <h1>Reset Password</h1>
      Klik tombol berikut untuk reset password:

      <div style="margin: auto; width: 50%; padding: 10px; text-align: center;">
        <a
          style="cursor: pointer;"
          href="{{ $url }}&token={{ $token }}"
          target="_blank"
          rel="noopener"
          ><button
            type="button"
            style="
              border: 0;
              border-radius: 5%;
              background-color: rgb(156, 149, 149);
              padding: 5px;
              color: #fff;
              cursor: pointer;
            "
          >
            Reset Password
          </button></a
        >
        <br />
      </div>

      <br /><br /><br /><br /><br /><strong
        >ini pesan otomatis. Mohon untuk tidak membalas</strong
      >
    </article>
  </section>

  <footer
    style="
      background-color: #ffffff;
      text-align: center;
      font-size: 10px;
      color: #000000;
    "
  >
    <p>Copyright © 2023 IPAC, All rights reserved.</p>
  </footer>
</html>
