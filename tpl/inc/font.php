<? if (isDev()) { ?>
  <style>
    :root {
      --font: sans-serif;
      --font-mono: monospace;
    }
  </style>
<? } else { ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Ubuntu+Sans:ital,wght@0,100..800;1,100..800&display=swap" rel="stylesheet">
  <style>
    :root {
      --font: 'Ubuntu Sans', sans-serif;
      --font-mono: monospace;
    }
  </style>
<? } ?>
