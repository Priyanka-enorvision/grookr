<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Images from S3</title>
</head>

<body>
    <h1>Images from S3</h1>
    <a href="/upload">Upload New Image</a>
    <?php if (session()->get('success')): ?>
        <p><?= session()->get('success') ?></p>
    <?php endif; ?>
    <?php if (isset($fileUrls) && count($fileUrls) > 0): ?>
        <?php foreach ($fileUrls as $fileUrl): ?>
            <div style="margin-bottom: 10px;">
                <img src="<?= $fileUrl ?>" alt="Image from S3" style="max-width: 100%; height: auto;">
                <form action="/delete/<?= basename($fileUrl) ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit">Delete</button>
                </form>
                <a href="/edit/<?= basename($fileUrl) ?>">Edit</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No images found.</p>
    <?php endif; ?>
</body>

</html>