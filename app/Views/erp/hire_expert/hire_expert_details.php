<?php
$expertId = $expert_id;
if ($expertId <= 0) {
    echo "Invalid expert ID.";
    exit;
}
$curl = curl_init();
$url = "http://103.104.73.221:3000/api/V1/global/expert/$expertId";

curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_URL => $url,
    CURLOPT_HTTPGET => true,
]);

$response = curl_exec($curl);
if (curl_errno($curl)) {
    $expertData = [];
} else {
    $expertData = json_decode($response, true)['detail'] ?? [];
}
curl_close($curl);

// Fetch a list of experts for random selection
$curl = curl_init();
$expertUrl = "http://103.104.73.221:3000/api/V1/global/expert";
curl_setopt_array(
    $curl,
    [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => $expertUrl,
        CURLOPT_HTTPGET => true,
    ]
);
$response = curl_exec($curl);
if (curl_errno($curl)) {
    $expertDataList = [];
} else {
    $expertDataList = json_decode($response, true)['detail']['rows'] ?? [];
}
curl_close($curl);
$randomExperts = [];
if (!empty($expertDataList)) {
    $randomExperts = array_rand($expertDataList, min(4, count($expertDataList)));
    $randomExperts = array_intersect_key($expertDataList, array_flip($randomExperts));
}

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile UI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        .profile-header {
            background: url('data:image/jpeg;base64,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') no-repeat center center / cover;
            padding: 40px;
            color: #fff;
            height: 281px;
        }

        .profile-picture {
            width: 140px;
            height: 140px;
            border: 4px solid white;
        }

        .btn-primary {
            background-color: #0a39ef;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0636bf;
        }

        .card-footer {
            background-color: #f8f9fa;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f3f2ef;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .profile-pic {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .user-details {
            font-size: 14px;
        }

        .user-details h3 {
            margin: 0;
            font-size: 16px;
        }

        .user-details span {
            color: #888;
            font-size: 12px;
        }



        .action-btn {
            background: none;
            border: none;
            font-size: 14px;
            color: #888;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .action-btn:hover {
            color: #0a66c2;
        }

        .about-section {
            margin-bottom: 30px;
        }

        .about-section h3 {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 15px;
            color: #0073b1;
            /* LinkedIn Blue */
        }

        .about-section p {
            font-size: 1rem;
            line-height: 1.6;
            color: #555;
        }

        .featured-card {
            text-align: center;
            padding: 0px;
            padding-top: 10px;
        }

        .featured-card img {
            width: 88%;
            height: 195px;
            margin-left: 16px;
            margin-right: 16px;
        }

        .featured-card p {
            margin-top: 10px;
            font-weight: bold;
            color: #333;
        }

        .card {
            border: none;
        }

        .card.shadow-lg {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .experience-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
            margin-left: 40px;
            padding-bottom: 20px;
        }

        .experience-image {
            margin-right: 20px;
        }

        .experience-image img {
            width: 60px;
            height: 60px;
        }

        .honors-image img {
            height: 60px;
            margin-right: 22px;
        }

        .experience-details {
            flex: 1;
        }

        .experience-details h3 {
            margin-top: 0;
        }

        .experience-details p,
        .experience-details span {
            margin: 5px 0;
        }

        .skills-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 17px;
            margin-top: 10px;
        }

        .skill-item {
            background-color: #f0f0f0;
            padding: 8px 15px;
            border-radius: 25px;
            font-size: 14px;
            color: #333;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s;
        }

        .custom-shadow {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1), 0 6px 20px rgba(0, 0, 0, 0.1);
        }
    </style>

</head>

<div class="container mt-3 mb-1">
    <div class="card shadow-lg mb-0">
        <div class="card-header profile-header text-center" style="height: 281px;">
            <img class="profile-picture rounded-circle shadow" src="<?= htmlspecialchars('https://ekartrent.s3.amazonaws.com/gpsServices/' . $expertData['profilePic']); ?>" alt="Profile Picture">
            <h3 class="mt-1"><?= htmlspecialchars($expertData['firstName'] . ' ' . $expertData['lastName']); ?></h3>
            <p class="mt-0 mb-0"><?= htmlspecialchars($expertData['designation'] ?? 'No designation'); ?></p>
            <p> <?= htmlspecialchars($expertData['shortDescription'] ?? 'No description'); ?>, <?= htmlspecialchars($expertData['previousOrg'] ?? 'No previousOrg'); ?> </p>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="row mb-3">
                    <div class="col-8">
                        <p style="font-size: larger;" class="mb-0"><b><?= htmlspecialchars($expertData['designation'] ?? 'No designation'); ?></b></p>
                        <p class="mb-3"><?= htmlspecialchars($expertData['shortDescription'] ?? 'No description'); ?> , <?= htmlspecialchars($expertData['previousOrg'] ?? 'No previousOrg'); ?></p>
                        <p class="mb-0"><b><i class="fas fa-envelope"></i> Email:</b> <a href="<?= htmlspecialchars($expertData['email'] ?? 'notfound@example.com'); ?>">
                                <?= htmlspecialchars($expertData['email'] ?? 'Not Found'); ?></a></p>
                        <p class="mb-0"><b><i class="fas fa-briefcase"></i> Total Experience:</b> <?= $expertData['totalExperience'] ? intval($expertData['totalExperience']) . ' years' : 'Not Found'; ?></p>
                        <p class="mb-0"><b><i class="fas fa-cogs"></i> Category:</b> <?= htmlspecialchars($expertData['categoryName'] ?? 'Not Found'); ?></p>
                    </div>
                    <div class="col-4">
                        <p class="mb-0"><b><i class="fas fa-phone-alt"></i> Phone:</b> +91 <?= htmlspecialchars($expertData['phoneNo'] ?? 'Not Found'); ?></p>
                        <p class="mb-0"><b><i class="fas fa-dollar-sign"></i> Per Hour Cost:</b> <?= isset($expertData['perHourCost']) ? '$' . number_format(floatval($expertData['perHourCost']), 2) : 'Not Found'; ?></p>
                    </div>
                </div>

                <!-- Hire Me Button Section -->
                <div class="row mt-3">
                    <div class="col-12 text-end">
                        <a href="<?= site_url('erp/expert-apply/' . intval($expertData['id'])); ?>" class="btn"
                            style="padding: 3px 30px; border-radius: 25px;  color: #007bff; border: 2px solid #007bff;  background-color: aliceblue;">Hire</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<div class="container mb-1">
    <div class="card shadow-lg mb-0">
        <h3 style="margin-bottom: 0px; margin-left: 18px;margin-top: 15px;">About</h3>
        <div class="card-body">
            <p>
                <?= htmlspecialchars($expertData['longDescription'] ?? 'Not Found'); ?>
            </p>
        </div>
    </div>
</div>

<div class="container mb-1">
    <div class="card shadow-lg mb-0">
        <h3 style="margin-left: 22px;margin-top: 20px;">Experience</h3>

        <?php foreach ($expertData['Experiences'] as $value) {
            $startDate = new DateTime($value['startDate']);
            $endDate = $value['endDate'] !== 'Present' ? new DateTime($value['endDate']) : new DateTime();
            $formattedStartDate = $startDate->format('M Y');
            $formattedEndDate = $value['endDate'] !== 'Present' ? $endDate->format('M Y') : 'Present';
            $interval = $startDate->diff($endDate);

            // Format duration intelligently
            $duration = '';
            if ($interval->y > 0) {
                $duration .= $interval->y . ' year' . ($interval->y > 1 ? 's' : '');
            }
            if ($interval->m > 0) {
                $duration .= ($duration ? ' ' : '') . $interval->m . ' mon';
            }
        ?>
            <div class="experience-item">
                <div class="experience-image">
                    <img src="<?= htmlspecialchars('https://ekartrent.s3.amazonaws.com/gpsServices/' . $value['companyImage']); ?>" alt="Software Developer">
                </div>
                <div class="experience-details">
                    <b><?= $value['companyName']; ?></b><br>
                    <span class="mt-0 mb-0 text-muted" style="font-size: 13px; font-weight: 400;">
                        <?= $formattedStartDate . " - " . $formattedEndDate; ?> .<?= $duration; ?>
                    </span>

                    <p class="mt-0 mb-0 text-muted" style="font-size: 13px; font-weight: 400;"><?= $value['description']; ?></p>
                </div>
            </div>
        <?php } ?>



    </div>
</div>


<div class="container mb-1">
    <div class="card shadow-lg mb-0">
        <h3 style="margin-left: 22px; margin-top: 20px;">Skills</h3>
        <div class="skills-list" style="margin-left: 22px; margin-top: 10px;">
            <?php
            $skills = $expertData['skills'] ?? 'Not Found';
            if ($skills !== 'Not Found') {
                $skillItems = explode(',', $skills);
                foreach ($skillItems as $skill) {
                    echo '<span class="skill-item">' . htmlspecialchars(trim($skill)) . '</span><br>';
                }
            } else {
                echo htmlspecialchars($skills);
            }
            ?>
        </div>
    </div>
</div>


<div class="container mb-1">
    <div class="card shadow-lg mb-0">
        <h3 style="margin-left: 22px; margin-top: 20px;">Honors & Awards</h3>

        <?php foreach ($expertData['Awards'] as $value) {
            $awardDate = new DateTime($value['awardDate']);
            $formattedAwardDate = $awardDate->format('d M Y');
        ?>
            <div class="row align-items-center experience-item mb-2">
                <div class="col-md-8">
                    <b><?= htmlspecialchars($value['title']); ?></b>
                    <p class="mt-0 mb-0 text-muted" style="font-size: 13px; font-weight: 400;">by <?= htmlspecialchars($value['organisationName']); ?></p>
                    <span class="mt-0 mb-0 text-muted" style="font-size: 13px; font-weight: 400;">on <?= $formattedAwardDate; ?></span>
                </div>

            </div>
        <?php } ?>


    </div>
</div>

<div class="container mb-1">
    <div class="card shadow-lg mb-0">
        <h3 style="margin-left: 22px; margin-top: 30px;">Suggestions</h3>
        <div class="row p-3">
            <?php foreach ($randomExperts as $expert) { ?>
                <div class="col-md-3">
                    <div class="card custom-shadow">
                        <div class="card-body text-center">
                            <!-- Circle image -->
                            <img src="<?= 'https://ekartrent.s3.amazonaws.com/gpsServices/' . $expert['profilePic']; ?>" alt="Profile Image" class="rounded-circle" width="100" height="100">
                            <h5 class="mt-3"><?= htmlspecialchars($expert['firstName'] . ' ' . $expert['lastName']); ?></h5>
                            <p class="mb-0"><?= htmlspecialchars($expert['designation'] ?? 'No designation'); ?></p>
                            <p><?= ($expert['description'] ?? '') . ',' . ($expert['categoryName'] ?? '') ?></p>
                            <a href="<?= site_url('erp/expert-details/' . intval($expert['id'])); ?>" class="btn"
                                style="padding: 3px 30px; border-radius: 25px;  color: #007bff; border: 2px solid #007bff;  background-color: aliceblue;">View</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelectorAll('.action-btn').forEach(button => {
        button.addEventListener('click', () => {
            alert('Feature under development!');
        });
    });
</script>