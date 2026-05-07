<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <style>
        body {
            margin: 0;
            padding: 24px;
            background: #f4f6fb;
            color: #111827;
            font-family: Arial, sans-serif;
        }

        .preview-sheet {
            max-width: 900px;
            margin: 0 auto;
            padding: 36px 42px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 18px 48px rgba(15, 23, 42, .12);
        }

        {!! $customCss !!}
    </style>
</head>
<body>
    <div class="preview-sheet">
        {!! $html !!}
    </div>
</body>
</html>
