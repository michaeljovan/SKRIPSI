<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Terima kasih</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="col-lg-6 mx-auto">
      <div class="alert alert-success shadow-sm">
        <h4 class="alert-heading">Terima kasih!</h4>
        <p>{{ session('success') ?? 'Respon evaluasi Anda sudah kami terima.' }}</p>
        <hr>
      </div>
    </div>
  </div>
</body>
</html>
