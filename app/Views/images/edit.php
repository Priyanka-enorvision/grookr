<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Image</title>
</head>

<body>
    <h1>Edit Image</h1>
    <img src="<?= $fileUrl ?>" alt="Image from S3" style="max-width: 100%; height: auto;">
    <form action="/update/<?= $fileId ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <label for="image">Choose Image:</label>
        <input type="file" name="image" id="image" required>
        <button type="submit">Update</button>
    </form>
</body>

</html>