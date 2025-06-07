<?php

use App\Models\UsersModel;

$UsersModel = new UsersModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();


$email = isset($user_info['email']) ? $user_info['email'] : '';
$phone = isset($user_info['contact_number']) ? $user_info['contact_number'] : '';

$redirect_url = 'https://connect195.com/gps/erp/hire-experts';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header("Location: $redirect_url");
    exit;
}
?>


<style>
    form {
        max-width: 1000px;
        margin: 0 auto;
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
    }

    input[type="text"],
    textarea,
    button,
    input[type="file"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 16px;
    }

    button {
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
        padding: 15px 20px;
        font-size: 16px;
        border-radius: 4px;
    }

    button:hover {
        background-color: #45a049;
    }
</style>


<form id="myForm" action="http://103.104.73.221:3000/api/V1/global/lead" method="post" enctype="multipart/form-data">

    <input type="hidden" name="userId" value="<?= $user_id ?>">
    <input type="hidden" name="expertId" value="<?= $apply_expert_id ?>">
    <input type="hidden" name="requesterEmail" value="<?= $email ?>">
    <input type="hidden" name="requesterPhoneNumber" value="<?= $phone ?>">

    <fieldset>
        <legend>Request Details</legend>

        <label for="shortDescription">Requirement:</label>
        <input type="text" id="shortDescription" name="shortDescription">

        <label for="longDescription"> Description:</label>
        <textarea id="longDescription" name="longDescription"></textarea>

        <label for="hour">Hours:</label>
        <input type="number" id="hour" name="hour" min="0">

        <label for="attachment">Attachment:</label>
        <input type="file" id="attachment" name="attachment">
    </fieldset>

    <button type="submit">Submit</button>
</form>

<script>
    document.getElementById('myForm').addEventListener('submit', function() {
        window.location.href = 'https://connect195.com/gps/erp/hire-experts';
    });
</script>