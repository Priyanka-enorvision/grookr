<?php

$expertId = $expert_id;
$user_id = $user_id; 

if ($expertId <= 0) {
    echo "Invalid expert ID.";
    exit;
}

$curl = curl_init();
$url = "http://103.104.73.221:3000/api/V1/global/lead/$expertId?userId=$user_id";

curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_URL => $url,
    CURLOPT_HTTPGET => true,
]);

$response = curl_exec($curl);

if (curl_errno($curl)) {
    echo '<p style="color:red; text-align:center;">Failed to connect to the expert API. Please try again later.</p>';
    $applied_expert_detail = [];
    // echo 'cURL error: ' . curl_error($curl);
    // exit;
}
else
{
    $applied_expert_detail = json_decode($response, true)['detail'] ?? [];

}


curl_close($curl);
?>


<style>
    /* Status Badge Styles */
    .status-badge {
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
    }

    /* Colors for Different Statuses */
    .badge-pending {
        background-color: #ffc107;
        color: #212529;
    }

    .badge-accept {
        background-color: #28a745;
        color: #fff;
    }

    .badge-complete {
        background-color: #007bff;
        color: #fff;
    }

    .badge-reject {
        background-color: #dc3545;
        color: #fff;
    }

    .badge-in-progress {
        background-color: #17a2b8;
        color: #fff;
    }

    .badge-unknown {
        background-color: #6c757d;
        color: #fff;
    }
</style>

<div class="container mt-4">
    <div class="row justify-content-center">
        <?php if (!empty($applied_expert_detail)) { ?>
            <div class="col-lg-8">
                <div class="card user-card user-card-1 shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-4 text-center">
                                <div class="mb-3">
                                    <span class="badge badge-success">Active</span>
                                </div>
                                <h5 class="mb-0">
                                    <?= 
                                    htmlspecialchars($applied_expert_detail['expertFullName']);
                                    // htmlspecialchars($applied_expert_detail['requesterFullName'] . ' ' . $applied_expert_detail['expertFullName']); 
                                    ?>
                                </h5>
                            </div>
                            <div class="col-md-8">
                                <p><b>Email:</b> <a href="mailto:<?= htmlspecialchars($applied_expert_detail['requesterEmail']); ?>"><?= htmlspecialchars($applied_expert_detail['requesterEmail']); ?></a></p>
                                <p><b>Phone:</b> <?= htmlspecialchars($applied_expert_detail['requesterPhoneNumber']); ?></p>
                                <p class="mb-0">
                                    <b>Status:</b>
                                    <?php
                                    $status = $applied_expert_detail['status'];
                                    switch ($status) {
                                        case 'P':
                                            echo '<span class="status-badge badge-pending">Pending</span>';
                                            break;
                                        case 'A':
                                            echo '<span class="status-badge badge-accept">Accepted</span>';
                                            break;
                                        case 'C':
                                            echo '<span class="status-badge badge-complete">Complete</span>';
                                            break;
                                        case 'R':
                                            echo '<span class="status-badge badge-reject">Rejected</span>';
                                            break;
                                        case 'W':
                                            echo '<span class="status-badge badge-in-progress">Work in Progress</span>';
                                            break;
                                        default:
                                            echo '<span class="status-badge badge-unknown">Unknown</span>';
                                    }
                                    ?>
                                </p>
                                <p><b>Last Update:</b> <?= date('M d, Y', strtotime($applied_expert_detail['lastUpdate'])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="col-lg-8">
                <div class="alert alert-danger" role="alert">
                    Expert Detail not found.
                </div>
            </div>
        <?php } ?>
    </div>
</div>
