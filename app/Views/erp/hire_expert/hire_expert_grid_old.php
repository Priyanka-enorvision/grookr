<?php
function fetchDataFromApi($url)
{
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => $url,
        CURLOPT_HTTPGET => true,
    ]);

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        return 'error';
    }

    curl_close($curl);
    return $response;
}

$expertUrl = "http://103.104.73.221:3000/api/V1/global/expert";
$categoryUrl = "http://103.104.73.221:3000/api/V1/global/category";

$response = fetchDataFromApi($expertUrl);
if ($response === 'error') {
    $expertData = [];
} else {
    $expertData = json_decode($response, true)['detail']['rows'] ?? [];
}

$response_category = fetchDataFromApi($categoryUrl);
if ($response_category === 'error') {
    $expertDataCategory = [];
} else {
    $expertDataCategory = json_decode($response_category, true)['detail']['rows'] ?? [];
}
?>


<style>
    .user-card .user-about-block {
        margin-top: 0px;
    }

    .button-link {
        display: inline-block;
        padding: 10px 20px;
        background-color: #ff230054;
        color: #fff;
        text-decoration: none;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .button-link:hover {
        background-color: #0056b3;
    }

    .text-center .button-link {
        display: inline-block;
        padding: 2px 10px;
        margin-top: 5px;
        background-color: #007bff;
        color: #fff;
        text-decoration: none;
        border: none;
        width: 80px;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }


    .text-center .button-link {
        display: inline-block;
        padding: 2px 10px;
        margin-top: 5px;
        background-color: #ff230054;
        color: #fff;
        text-decoration: none;
        border: none;
        width: 80px;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
</style>


<section>
    <div class="container">
        <div class="form-group text-right">
            <a class="button-link" href="<?= site_url('erp/applied-experts'); ?>">Applied Experts</a>
        </div>
    </div>
</section>

<section>
    <div class="container">
        <div class="form-group">
            <label for="categorySelect">Select Category:</label>
            <select class="form-control" id="categorySelect" onchange="filterExperts()">
                <option value="all">All Categories</option>
                <?php if (!empty($expertDataCategory)) { ?>
                    <?php foreach ($expertDataCategory as $data): ?>
                        <option value="<?= htmlspecialchars($data['id']); ?>"><?= htmlspecialchars($data['categoryName']); ?></option>
                    <?php endforeach; ?>
                <?php } else { ?>
                    <div>
                        <p style="color:red; text-align:center;">No Category.</p>
                    </div>
                <?php } ?>
            </select>
        </div>
    </div>
</section>

<section>
    <div class="container">
        <div class="row" id="expertContainer">
            <?php if (!empty($expertData)) { ?>
                <?php foreach ($expertData as $r): ?>
                    <div class="col-lg-4 col-md-6 mb-4 expert-card" data-category="<?= htmlspecialchars($r['categoryId']); ?>">
                        <div class="card user-card user-card-1">
                            <div class="card-body pt-0">
                                <div class="user-about-block text-center">
                                    <div class="row align-items-end">
                                        <div class="col text-left pb-3">
                                            <span class="badge badge-success">Active</span>
                                        </div>
                                        <div class="col">
                                            <img class="img-fluid wid-80" src="<?= 'https://ekartrent.s3.amazonaws.com/gpsServices/' . $r['profilePic']; ?>" alt="<?= htmlspecialchars($r['firstName'] . ' ' . $r['lastName']); ?>">
                                        </div>
                                        <div class="col text-right pb-3">
                                            <div class="dropdown">
                                                <a class="drp-icon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="feather icon-more-horizontal"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="<?= site_url('erp/expert-details/' . intval($r['id'])); ?>">
                                                        <i class="feather icon-eye"></i> View
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <a href="<?= site_url('erp/expert-details/' . intval($r['id'])); ?>">
                                        <h4 class="mb-1 mt-3">
                                            <?= htmlspecialchars($r['firstName'] . ' ' . $r['lastName']); ?>
                                        </h4>
                                    </a>
                                    <p class="mb-3 text-muted">
                                        <?= htmlspecialchars($r['designation'] ?? 'No designation'); ?>
                                    </p>
                                    <p class="mb-1"><b>Email:</b> <a href="mailto:<?= htmlspecialchars($r['email']); ?>"><?= htmlspecialchars($r['email']); ?></a></p>
                                    <p class="mb-0"><b>Phone:</b> <?= htmlspecialchars($r['phoneNo']); ?></p>
                                    <p class="mb-0"><b>Total Experience:</b> <?= intval($r['totalExperience']); ?> years</p>
                                    <p class="mb-0"><b>Per Hour Cost:</b> $<?= number_format(floatval($r['perHourCost']), 2); ?></p>
                                    <!-- <p class="mb-0"><b>Per Hour Cost:</b> <?= $r['perHourCost']; ?></p> -->
                                    <div class="text-center">
                                        <a href="<?= site_url('erp/expert-apply/' . intval($r['id'])); ?>" class="button-link">
                                            Hire
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php } else { ?>
                <div>
                    <p style="color:red; text-align:center;">No Expert Yet.</p>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

<script>
    function filterExperts() {
        var categoryId = $('#categorySelect').val();
        $('.expert-card').each(function() {
            var expertCategory = $(this).data('category');
            if (categoryId === 'all' || expertCategory == categoryId) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    $(document).ready(function() {
        filterExperts();
    });
</script>