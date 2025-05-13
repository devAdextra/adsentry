<!DOCTYPE html>
<html>

<head>
    <title>Downloads</title>
</head>

<body>
    <h1>Elenco Download</h1>
    <ul>
        @foreach($downloads as $download)
            <li>
                <strong>File:</strong> {{ $download['filename'] }}
                <em>(Generato il {{ $download['created_at'] }})</em>
            </li>
        @endforeach
    </ul>
</body>

</html>