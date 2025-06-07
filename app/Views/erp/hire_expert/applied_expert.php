<?php

$curl = curl_init();
$url = "http://103.104.73.221:3000/api/V1/global/lead?userId=$user_id";

curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_URL => $url,
    CURLOPT_HTTPGET => true,
]);

$response_apply_data = curl_exec($curl);

if (curl_errno($curl)) {
    $applyExpertData = []; 
} else {
    $applyExpertData = json_decode($response_apply_data, true)['detail']['rows'] ?? [];
}

curl_close($curl);
?>


<style>
    .user-card .user-about-block {
        margin-top: 0px;
    }

    .status-badge {
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
    }

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

<section>
    <div class="container">
        <div class="row" id="expertContainer">
            <?php if(!empty($applyExpertData)){ ?>
                <?php foreach ($applyExpertData as $r): ?>
                    <div class="col-lg-4 col-md-6 mb-4 expert-card" data-category="<?= htmlspecialchars($r['expertId']); ?>">
                        <div class="card user-card user-card-1">
                            <div class="card-body pt-0">
                                <div class="user-about-block text-center">
                                    <div class="row align-items-end">
                                        <div class="col text-left pb-3">
                                            <span class="badge badge-success">Active</span>
                                        </div>
                                        <div class="col text-right pb-3">
                                            <div class="dropdown">
                                                <a class="drp-icon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="feather icon-more-horizontal"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="<?= site_url('erp/applied-expert-data/' . intval($r['id'])); ?>">
                                                        <i class="feather icon-eye"></i> View
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="text-center">
                                        <a href="<?= site_url('erp/applied-expert-data/' . intval($r['id'])); ?>" class="text-decoration-none">
                                            <h4 class="mb-1 mt-3">Expert: <?= htmlspecialchars($r['expertFullName']); ?></h4>
                                        </a>
                                        <hr class="my-4">
                                        
                                        <h5 class="text-center mb-4">Requester Details:-</h5>

                                        <div class="row justify-content-center">
                                            <div class="col-md-8">
                                                <div class="mb-3">
                                                    <p><b>Requirement:</b> <?= !empty($r['shortDescription']) ? htmlspecialchars($r['shortDescription']) : '<em>No Requirement</em>'; ?></p>
                                                    <p><b>Description:</b> <?= !empty($r['longDescription']) ? htmlspecialchars($r['longDescription']) : '<em>No Description</em>'; ?></p>
                                                    <p><b>Email:</b> <a href="mailto:<?= htmlspecialchars($r['requesterEmail']); ?>"><?= htmlspecialchars($r['requesterEmail']); ?></a></p>
                                                    <p><b>Phone:</b> <?= htmlspecialchars($r['requesterPhoneNumber']); ?></p>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <p><b>Status:</b>
                                                        <?php
                                                            $status = $r['status'];
                                                            switch ($status) {
                                                                case 'P':
                                                                    echo '<span class="badge badge-pill badge-info">Pending</span>';
                                                                    break;
                                                                case 'A':
                                                                    echo '<span class="badge badge-pill badge-success">Accepted</span>';
                                                                    break;
                                                                case 'C':
                                                                    echo '<span class="badge badge-pill badge-primary">Completed</span>';
                                                                    break;
                                                                case 'R':
                                                                    echo '<span class="badge badge-pill badge-danger">Rejected</span>';
                                                                    break;
                                                                case 'W':
                                                                    echo '<span class="badge badge-pill badge-warning">Work in Progress</span>';
                                                                    break;
                                                                default:
                                                                    echo '<span class="badge badge-pill badge-secondary">Unknown</span>';
                                                            }
                                                        ?>
                                                    </p>
                                                    <p><b>Last Update:</b> <?= date('M d, Y', strtotime($r['lastUpdate'])); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php } else {  ?>
                <div>
                    <p style="color:red; text-align:center;">No Applied Expert.</p>
                </div>
            <?php } ?>
        </div>
    </div>
</section>

    