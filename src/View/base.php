<!DOCTYPE html>
<html lang="en">
<head>
    <title>test</title>
    <link rel="stylesheet" href="/static/styles.css">
    <script src="/static/bundle.js"></script>
</head>
<body>
    <?php
        if($content ?? null) $content();
    ?>
</body>
</html>
