<!doctype html>
<html lang="en">
<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Blog</title>

    <link href="/css/main.css" rel="stylesheet">

</head>
<body class="py-5">

    <header class="mb-5">
        <div class="container">
            <div class="row">
                <div class="col">
                    <a href="/">
                        <img src="https://worldwide.by/storage/images/logos/worldwide_red.svg" class="logo img-fluid">
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="row">
                <div class="col">
                    {block name="content"}{/block}
                </div>
            </div>
        </div>
    </main>

    <script src="/js/bundle.js"></script>

</body>
</html>
