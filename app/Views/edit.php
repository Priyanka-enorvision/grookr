<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Image</title>
</head>
<body>
    <h1>Edit Image</h1>

    <?php if (session()->getFlashdata('message')): ?>
        <p style="color: green;"><?= session()->getFlashdata('message') ?></p>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <p style="color: red;"><?= session()->getFlashdata('error') ?></p>
    <?php endif; ?>

    <!-- Display current image -->
    <img src="<?= $file_url ?>" alt="Image Preview" style="max-width: 200px; display: block;">

    <!-- Form to upload a new file -->
    <form action="<?= site_url('/s3-images/update/' . base64_encode($key)) ?>" method="POST" enctype="multipart/form-data">
        <label for="file">Replace Image:</label>
        <input type="file" name="file" id="file" required>
        <button type="submit">Update</button>
    </form>
</body>
</html>
