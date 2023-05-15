<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload PDF</title>
</head>

<body>
    @if(session("path"))
    <div>Upploaded to: {{session("path")}}</div>
    @endif
    <form method="POST" action="{{route('documents.store')}}" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" />
        <button type="submit">Upload</button>
    </form>
</body>

</html>