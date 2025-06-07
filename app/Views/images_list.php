<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>S3 Image List</title>
</head>

<body>
    <h1>S3 Image List</h1>

    <?php if (session()->getFlashdata('message')): ?>
        <p style="color: green;"><?= session()->getFlashdata('message') ?></p>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <p style="color: red;"><?= session()->getFlashdata('error') ?></p>
    <?php endif; ?>

    <ul>
        <?php foreach ($files as $file):
        ?>

            <li>
                <img src="https://grookr.s3.us-west-2.amazonaws.com/<?= $file['Key'] ?>" alt="<?= $file['Key'] ?>"
                    style="max-width: 200px; display: block;">


                <a href="<?= site_url('/s3-images/edit/' . base64_encode($file['Key'])) ?>">Edit</a>
                <a href="<?= site_url('/s3-images/download/' . base64_encode($file['Key'])) ?>">Download</a>
                <a href="<?= site_url('/s3-images/delete/' . base64_encode($file['Key'])) ?>">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>


    <h2>Upload New File</h2>
    <form action="<?= site_url('/s3-images/upload') ?>" method="POST" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <button type="submit">Upload</button>
    </form>
</body>

</html>