<!DOCTYPE html>
<html>
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Sistem Informasi Alumni</title>
</head>
<body>
    <p>Terima kasih <b>{{$details['nama']}}</b>, sudah melakukan registrasi akun pada Sistem Informasi Alumni STMIK Indonesia Padang ,  berikut ini adalah informasi akun anda:</p>
    <table>
      <tr>
        <td>NIM / No BP</td>
        <td>:</td>
        <td>{{$details['nim']}}</td>
      </tr>
      <tr>
        <td>Nama</td>
        <td>:</td>
        <td>{{$details['nama']}}</td>
      </tr>
      <tr>
        <td>Prodi</td>
        <td>:</td>
        <td>{{$details['prodi']}}</td>
      </tr>
      <tr>
        <td>Email</td>
        <td>:</td>
        <td>{{$details['email']}}</td>
      </tr>
      <tr>
        <td>Username</td>
        <td>:</td>
        <td>{{$details['nim']}}</td>
      </tr> 
      <tr>
        <td>Password</td>
        <td>:</td>
        <td>{{$details['password']}}</td>
      </tr>            
    </table>
    <p>Klik link berikut untuk aktivasi akun anda: <a href="http://localhost:8000/aktivasi/{{ $details['keyword'] }}">http://localhost:8000/aktivasi/{{ $details['keyword'] }} </a></p>
</body>
</html>