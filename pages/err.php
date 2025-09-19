<?
header("HTTP/1.1 $err $msg");
header("Content-Type: text/html");
?>
<!DOCTYPE html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $err ?></title>
  <style>
    body {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      font-family: sans-serif;
      background-color: #eee;
      text-align: center;
    }

    svg {
      width: 64%;
      max-height: 42vh;
      margin: 2rem;
    }

    p {
      /* font-size: 2rem; */
      color: #111;
      margin: .2em;
    }
  </style>
</head>

<body>
  <a href="<?= h(URT) ?>">
    <svg viewBox="0 0 60 60">
      <circle cx="30" cy="30" r="30" fill="#46211A" />
      <path stroke-width="2.7" stroke="#A43820" stroke-linecap="round" stroke-linejoin="round" fill="#F1D3B2" d="M11.25 41.25v7.5l4.5-3 3 3h7.5l-15-15v-7.5l22.5 22.5h7.5l-30-30v-7.5l37.5 37.5v-7.5l-30-30h7.5l22.5 22.5v-7.5l-15-15h7.5l3 3 4.5-3v7.5l-37.5 22.5" />
    </svg>
  </a>

  <p><?= h($err) ?></p>
  <p><?= h($msg) ?></p>
</body>

</html>
