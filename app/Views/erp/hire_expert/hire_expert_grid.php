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
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .expert {
        background-color: #fff;
        border-radius: 10px;
        overflow: hidden;
        display: flex;
        margin-bottom: 5px;
    }

    .expert img {
        border-radius: 50%;
        /* Round the image */
        display: block;
        width: 60px;
        height: 56px;
        box-shadow: 0 6px 10px rgba(0, 0, 0, 0.6);
        /* Black shadow with more opacity */
        transition: box-shadow 0.3s ease-in-out;
        /* Smooth transition for hover effect */
    }





    .status-active {
        position: absolute;
        width: 15px;
        height: 15px;
        background-color: green;
        border: 3px solid white;
        border-radius: 50%;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
        margin-left: 46px;
        margin-top: -20px;
    }

    .expert .expert-details {
        flex-grow: 0.5;
        padding: 0 20px;
    }

    /* .expert .expert-actions {
        text-align: right;
        margin-left: 220px;
    } */

    .badge-success {
        background-color: #28a745;
        color: #fff;
        padding: 5px 10px;
        border-radius: 5px;
        margin-bottom: 10px;
        display: inline-block;
    }

    .expert-actions {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .badge-success {
        background-color: #28a745;
        font-size: 0.9rem;
        padding: 0.4rem 0.8rem;
        width: 129px;
    }



    #categorySelect {
        margin-right: 13px;
        width: 186px !important;
    }

    .divider {
        border: 0;
        border-top: 1px solid #ddd;
        margin: 0 0 20px;
        width: 100%;
    }

    .design-btn {
        display: inline-block;
        padding: 3px 30px;
        border-radius: 25px;
        color: #007bff;
        border: 2px solid #007bff;
        background-color: aliceblue;

    }

    .details-row {
        display: flex;
        gap: 5px;
        align-items: center;
        margin-bottom: 5px;
        flex-wrap: wrap;
        /* Ensures it wraps properly on small screens */
    }

    .details-row span {
        white-space: nowrap;
        /* Prevents text from breaking into a new line */
    }
</style>


<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">



<section>
    <div class="container">
        <div class="row align-items-center mb-3">
            <div class="col-md-6">
                <h5 class="mb-0">Filter Experts</h5>
            </div>
            <div class="col-md-6 d-flex justify-content-end align-items-center">

                <select class="form-control w-auto" id="categorySelect" onchange="filterExperts()">
                    <option value="all">All Categories</option>
                    <?php if (!empty($expertDataCategory)) { ?>
                        <?php foreach ($expertDataCategory as $data): ?>
                            <option value="<?= htmlspecialchars($data['id']); ?>"><?= htmlspecialchars($data['categoryName']); ?></option>
                        <?php endforeach; ?>
                    <?php } else { ?>
                        <option value="">No Categories</option>
                    <?php } ?>
                </select>

                <a class="btn mr-2" href="<?= site_url('erp/applied-experts'); ?>" style="background-color:#007bff;color:white;">Applied Experts</a>
            </div>
        </div>
    </div>
</section>
<hr>

<section>
    <div class="container">
        <div class="row" id="expertContainer">
            <?php if (!empty($expertData)) { ?>
                <?php foreach ($expertData as $r): ?>
                    <div class="col-12">
                        <div class="expert" data-category="<?= htmlspecialchars($r['categoryId']); ?>">

                            <div class="col-1">
                                <img class="img-fluid" src="<?= 'https://ekartrent.s3.amazonaws.com/gpsServices/' . $r['profilePic']; ?>"
                                    alt="<?= htmlspecialchars($r['firstName'] . ' ' . $r['lastName']); ?>">
                                <span class="status-active"></span>
                            </div>

                            <div class="expert-details col-6">
                                <h5>
                                    <?= htmlspecialchars($r['firstName'] . ' ' . $r['lastName']); ?>
                                    <i class="fas fa-user-check"></i>
                                </h5>
                                <p class="text-muted mb-0"><?= htmlspecialchars($r['designation'] ?? 'No designation'); ?></p>
                                <div class="details-row">
                                    <span><b>Email:</b> <a href="mailto:<?= htmlspecialchars($r['email']); ?>"><?= htmlspecialchars($r['email']); ?></a></span>
                                    ||
                                    <span><b>Phone:</b> <?= htmlspecialchars($r['phoneNo']); ?></span>
                                </div>
                                <div class="details-row">
                                    <span><b>Total Experience:</b> <?= intval($r['totalExperience']); ?> years</span>
                                    ||
                                    <span><b>Per Hour Cost:</b> $<?= number_format(floatval($r['perHourCost']), 2); ?></span>
                                </div>
                            </div>

                            <div class="expert-actions text-center col-4">
                                <div class="action-buttons mt-2">
                                    <a href="<?= site_url('erp/expert-details/' . intval($r['id'])); ?>" class="design-btn">View</a>
                                    <a href="<?= site_url('erp/expert-apply/' . intval($r['id'])); ?>" class="design-btn">Hire</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="divider">
                <?php endforeach; ?>
            <?php } else { ?>
                <div class="col-12">
                    <p style="color:red; text-align:center;">No Expert Yet.</p>
                </div>
            <?php } ?>
        </div>
    </div>


</section>
<script>
    function filterExperts() {
        var categoryId = $('#categorySelect').val();

        // Get all expert elements
        var experts = $('.expert');

        // Remove existing dividers
        $('.divider').remove();

        // Track visible experts and add dividers dynamically
        var visibleExperts = experts.filter(function() {
            var expertCategory = $(this).data('category');
            return categoryId === 'all' || expertCategory == categoryId;
        }).show();

        // Add divider after each visible expert except the last one
        visibleExperts.each(function(index) {
            if (index < visibleExperts.length - 1) {
                $(this).after('<hr class="divider">');
            }
        });

        // Hide non-matching experts
        experts.not(visibleExperts).hide();
    }

    $(document).ready(function() {
        // Apply the filter on page load
        filterExperts();
    });
</script>



<!-- <script>
    function filterExperts() {
        var categoryId = $('#categorySelect').val();
        $('.expert').each(function() {
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
</script> -->