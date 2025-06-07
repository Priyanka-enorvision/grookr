<?php

use App\Models\UsersModel;
use App\Models\InvestmentTypeModel;

$UsersModel = new UsersModel();
$investmentModel = new InvestmentTypeModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$employeeList = $UsersModel->where('company_id', $user_info['company_id'])->orderBy('user_id', 'desc')->findAll();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <style>
        #DataTables_Table_0_wrapper {
            padding-top: 10px;
            width: 100%;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0px !important;
        }

        .modal-header {
            background: linear-gradient(to right, #226faa 0, #2989d8 37%, #72c0d3 100%);
        }

        .status-label {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 12px;
            cursor: pointer;
            color: white;

        }

        .status-active {
            border-color: #28a745;
            background-color: #28a745;
        }

        .status-inactive {

            border-color: #dc3545;
            background-color: #dc3545;
        }

        .status-label:hover {
            text-decoration: none;
            color: #fff;
        }

        .project-status-pending {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 12px;
            color: #ffc107;
            /* Yellow for Pending */
            border: 1px solid #ffc107;
        }

        .project-status-approved {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 12px;
            color: #28a745;
            /* Green for Approved */
            border: 1px solid #28a745;
        }

        .project-status-rejected {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 12px;
            color: #dc3545;
            /* Red for Rejected */
            border: 1px solid #dc3545;
        }
    </style>
</head>

<body>
    <div id="wrapper" style="min-height: 1020px;">
        <div class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel_s">
                        <hr>
                        <div class="panel-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="m-0">Tax Declaration Applicants </h6>
                            </div>

                            <div class="clearfix"></div>
                            <!-- <hr class="hr-panel-heading"> -->
                            <div id="DataTables_Table_0_wrapper"
                                class="dataTables_wrapper form-inline dt-bootstrap no-footer">

                                <table class="table table-striped dataTable no-footer" id="DataTables_Table_0"
                                    role="grid">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Employee Name</th>
                                            <th>Taxable income</th>
                                            <th>Declared Amount </th>
                                            <th>financial year</th>
                                            <th>Approval Status</th>
                                            <th>View</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        foreach ($employeeList as $list) {
                                            $data = getdeclarationAmount($list['user_id']);
                                            $taxList = $data['taxList'];
                                            $totalDeclaredAmount = $data['totalDeclaredAmount'];
                                            $overallStatus = '';

                                            if (!empty($taxList)) {
                                                $statuses = array_column($taxList, 'status');
                                                if (in_array('Pending', $statuses)) {
                                                    $overallStatus = 'Pending';
                                                } elseif (count(array_unique($statuses)) === 1 && $statuses[0] === 'Rejected') {
                                                    $overallStatus = 'Rejected';
                                                } elseif (count(array_unique($statuses)) === 1 && $statuses[0] === 'Approved') {
                                                    $overallStatus = 'Approved';
                                                }
                                            }
                                        ?>
                                            <tr>
                                                <td><?= $i++; ?></td>
                                                <td><?= $list['first_name'] . " " . $list['last_name']; ?></td>
                                                <td><!-- Additional data can go here --></td>
                                                <td><?= number_format($totalDeclaredAmount); ?></td>
                                                <?php
                                                $createdAt = !empty($taxList) ? $taxList[0]['created_at'] : null;

                                                if (!empty($createdAt)) {
                                                    $date = new \DateTime($createdAt);
                                                    $year = $date->format('Y');
                                                    if ($date->format('m') >= 4) {
                                                        $startYear = $year;
                                                        $endYear = $year + 1;
                                                    } else {
                                                        $startYear = $year - 1;
                                                        $endYear = $year;
                                                    }
                                                    $financialYear = $startYear . '-' . $endYear;
                                                } else {
                                                    $financialYear = 'NA';
                                                }
                                                ?>
                                                <td><?= $financialYear ?></td>

                                                <td>
                                                    <?php
                                                    $status = strtolower($overallStatus);
                                                    $statusClass = '';

                                                    if ($status == 'pending') {
                                                        $statusClass = 'project-status-pending';
                                                    } elseif ($status == 'approved') {
                                                        $statusClass = 'project-status-approved';
                                                    } elseif ($status == 'rejected') {
                                                        $statusClass = 'project-status-rejected';
                                                    }
                                                    ?>
                                                    <span class="<?= $statusClass; ?>"><?= $overallStatus; ?></span>
                                                </td>
                                                <td>
                                                    <a href="<?= site_url('erp/tax-declaration/' . base64_encode($list['user_id'])); ?>"><i class="feather icon-eye"></i></a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>




                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
</body>

</html>
<script>
    $(document).ready(function() {
        // Display toastr notifications for flash data
        <?php if (session()->getFlashdata('error')): ?>
            toastr.error("<?= esc(session()->getFlashdata('error')); ?>", 'Error', {
                timeOut: 5000
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('message')): ?>
            toastr.success("<?= esc(session()->getFlashdata('message')); ?>", 'Success', {
                timeOut: 5000
            });
        <?php endif; ?>

        $('#DataTables_Table_0').DataTable({
            paging: true, // Enable pagination
            searching: true, // Enable search
            ordering: true, // Enable ordering
            lengthMenu: [5, 10, 25, 50, 100], // Options for number of records to show
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search records",
            }
        });
    });
</script>