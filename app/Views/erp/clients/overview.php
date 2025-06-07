<?php

use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\ProjectsModel;
use App\Models\OpportunityModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();
$ProjectsModel = new ProjectsModel();
$opportunityModel = new OpportunityModel();
$session = \Config\Services::session();
$usession = $session->get('sup_username');
$router = service('router');
$xin_system = $SystemModel->where('setting_id', 1)->first();
$user = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$locale = service('request')->getLocale();

$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$user_id = $usession['sup_user_id'];

$opportunity_count = $opportunityModel->where('company_id', $user_info['company_id'])->countAllResults();
$client_count = $UsersModel->where('user_type', 'customer')->countAllResults();

$db = \Config\Database::connect();
$company_name = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', $user_info['company_name']));
$table_name = 'leads_' . $company_name;
$builder = $db->table($table_name);
$lead_count = $builder->countAllResults();

$invoice_count = $db->table('ci_invoices')
    ->where('company_id', $user_info['company_id']) // Apply the condition
    ->countAllResults();


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
    $rows = json_decode($response_apply_data, true)['detail']['rows'] ?? [];

    $applyExpertData = array_filter($rows, function ($row) {
        return $row['status'] === 'A';
    });

    $applyExpertDataId = array_column($applyExpertData, 'expertId');
}

curl_close($curl);

if ($user_info['user_type'] == 'staff') {

    $user_id = $user_info['user_id'];
    $company_id = $user_info['company_id'];

    // Fetch expert user details via cURL
    $curl = curl_init();
    $url = "http://103.104.73.221:3000/api/V1/global/expert-user/$user_id";

    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL => $url,
        CURLOPT_HTTPGET => true,
        CURLOPT_TIMEOUT => 10,
    ]);

    $response = curl_exec($curl);

    if ($response === false) {
        curl_close($curl);
        die("cURL Error: " . curl_error($curl));
    }

    $expert_user_detail = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        curl_close($curl);
        die("JSON Decoding Error: " . json_last_error_msg());
    }

    curl_close($curl);

    $expert_id = $expert_user_detail['detail']['id'] ?? null;

    // Function to count projects by status
    function getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, $status, $expert_id = null)
    {
        $builder = $ProjectsModel->where('company_id', $company_id)
            ->where('status', $status)
            ->groupStart()
            ->where('added_by', $user_id)
            ->orWhere('FIND_IN_SET(' . $user_id . ', assigned_to) > 0');

        if ($expert_id !== null) {
            $builder->orWhere('FIND_IN_SET(' . $expert_id . ', expert_to) > 0');
        }

        $builder->groupEnd();

        return $builder->countAllResults();
    }

    // Retrieve staff and client information
    $staff_info = $UsersModel->where('company_id', $company_id)->where('user_type', 'staff')->findAll();
    $all_clients = $UsersModel->where('company_id', $company_id)->where('user_type', 'customer')->findAll();

    // Count total projects
    $total_projects = $ProjectsModel->where('company_id', $company_id)
        ->groupStart()
        ->where('added_by', $user_id)
        ->orWhere('FIND_IN_SET(' . $user_id . ', assigned_to) > 0')
        ->groupEnd()
        ->countAllResults();

    // Count projects by status
    $not_started = getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 0, $expert_id);
    $in_progress = getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 1, $expert_id);
    $completed = getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 2, $expert_id);
    $cancelled = getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 3, $expert_id);
    $hold = getProjectsCountByStatus($ProjectsModel, $company_id, $user_id, 4, $expert_id);
} else {

    $staff_info = $UsersModel->where('company_id', $usession['sup_user_id'])->where('user_type', 'staff')->findAll();
    $all_clients = $UsersModel->where('company_id', $usession['sup_user_id'])->where('user_type', 'customer')->findAll();
    $total_projects = $ProjectsModel->where('company_id', $usession['sup_user_id'])->countAllResults();
    $not_started = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 0)->countAllResults();
    $in_progress = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 1)->countAllResults();
    $completed = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 2)->countAllResults();
    $cancelled = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 3)->countAllResults();
    $hold = $ProjectsModel->where('company_id', $usession['sup_user_id'])->where('status', 4)->countAllResults();
}
?>

<?php if (in_array('project1', staff_role_resource()) || in_array('projects_calendar', staff_role_resource()) || in_array('projects_sboard', staff_role_resource()) || $user_info['user_type'] == 'company') { ?>

    <hr class="border-light m-0 mb-3">
<?php } ?>


<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<div class="row">
    <!-- Opportunity Card -->
    <div class="col-xl-3 col-md-6">
        <a href="<?= site_url('erp/opportunity-list'); ?>" class="card feed-card">
            <div class="card-body p-t-0 p-b-0">
                <div class="row">
                    <div class="col-4 bg-success border-feed">
                        <i class="fas fa-bullseye f-40"></i> <!-- Icon for opportunity -->
                    </div>
                    <div class="col-8">
                        <div class="p-t-25 p-b-25">
                            <h2 class="f-w-400 m-b-10"><?= $opportunity_count; ?></h2> <!-- Opportunity data -->
                            <p class="text-muted m-0"><?= lang('Main.xin_total'); ?>
                                <span class="text-success f-w-400">Opportunity</span> <!-- Updated name -->
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Leads Card -->
    <div class="col-xl-3 col-md-6">
        <a href="<?= site_url('erp/leads-list'); ?>" class="card feed-card">
            <div class="card-body p-t-0 p-b-0">
                <div class="row">
                    <div class="col-4 bg-primary border-feed">
                        <i class="fas fa-users f-40"></i> <!-- Icon for leads -->
                    </div>
                    <div class="col-8">
                        <div class="p-t-25 p-b-25">
                            <h2 class="f-w-400 m-b-10"><?= $lead_count; ?></h2> <!-- Leads data -->
                            <p class="text-muted m-0"><?= lang('Main.xin_total'); ?>
                                <span class="text-primary f-w-400">Leads</span> <!-- Updated name -->
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Client Card -->
    <div class="col-xl-3 col-md-6">
        <a href="<?= site_url('erp/clients-list'); ?>" class="card feed-card">
            <div class="card-body p-t-0 p-b-0">
                <div class="row">
                    <div class="col-4 bg-info border-feed">
                        <i class="fas fa-handshake f-40"></i> <!-- Icon for client -->
                    </div>
                    <div class="col-8">
                        <div class="p-t-25 p-b-25">
                            <h2 class="f-w-400 m-b-10"><?= $client_count; ?></h2> <!-- Client data -->
                            <p class="text-muted m-0"><?= lang('Main.xin_total'); ?>
                                <span class="text-info f-w-400">Client</span> <!-- Updated name -->
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- Invoice Card -->
    <!-- <div id="draggableForm" draggable="true" ondragstart="drag(event)" class="col-xl-3 col-md-6"> -->
    <div class="col-xl-3 col-md-6">
        <a href="<?= site_url('erp/invoices-list'); ?>" class="card feed-card">
            <div class="card-body p-t-0 p-b-0">
                <div class="row">
                    <div class="col-4 bg-danger border-feed">
                        <i class="fas fa-file-invoice-dollar f-40"></i> <!-- Icon for invoice -->
                    </div>
                    <div class="col-8">
                        <div class="p-t-25 p-b-25">
                            <h2 class="f-w-400 m-b-10"><?= $invoice_count; ?></h2> <!-- Invoice data -->
                            <p class="text-muted m-0"><?= lang('Main.xin_total'); ?>
                                <span class="text-danger f-w-400">Invoice</span> <!-- Updated name -->
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <!-- </div> -->
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.5/xlsx.full.min.js"></script>

<script>
    function drag(event) {
        event.dataTransfer.setData("text", event.target.id);
    }

    function allowDrop(event) {
        event.preventDefault();
    }

    function drop(event) {
        event.preventDefault();
        var data = event.dataTransfer.getData("text");
        event.target.appendChild(document.getElementById(data));
    }
</script>