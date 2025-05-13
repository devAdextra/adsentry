<!DOCTYPE html>
<html>

<head>
    <title>Upload</title>
</head>

<body>
    <h1>Carica File</h1>

    @if(session('success'))
        <p style="color:green">{{ session('success') }}</p>
    @endif

    <form action="{{ route('upload.handle') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="upload_file">Seleziona il file:</label>
        <input type="file" name="upload_file" id="upload_file" required>
        <button type="submit">Carica</button>
    </form>
</body>

</html>