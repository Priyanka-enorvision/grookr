<style>
    /* Custom Styles */
    .card-custom {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin-bottom:0px;
    }

    .card-header-custom {
        background: linear-gradient(45deg, #4e73df, #1cc88a);
        color: #fff;
        padding: 1.25rem;
    }

    .card-header-custom h5 {
        font-weight: 600;
    }

    .card-body-custom {
        padding: 1.5rem;
        background-color: #f8f9fc;
    }

    .form-control-custom {
        border-radius: 8px;
        border: 1px solid #ced4da;
        transition: all 0.3s;
    }

    .form-control-custom:focus {
        border-color: #4e73df;
        box-shadow: 0 0 8px rgba(78, 115, 223, 0.3);
    }

    .btn-custom {
        background-color: #4e73df;
        border: none;
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        color: #fff;
        font-size: 1rem;
        font-weight: 600;
        transition: background-color 0.3s ease;
    }

    .btn-custom:hover {
        background-color: #2e59d9;
        box-shadow: 0 4px 10px rgba(78, 115, 223, 0.4);
    }
    
</style>

<div class="card card-custom ">
    <div class="card-header-custom d-flex justify-content-between align-items-center">
        <h5>
            <i data-feather="settings" class="icon-svg-primary wid-20"></i>
            <span class="ms-2">Planning Configuration</span>
        </h5>
        <small>Change Your Planning Configuration</small>
    </div>

    <?php 
        $attributes = array('name' => 'planning_configuration_update', 'id' => 'planning_configuration_update', 'autocomplete' => 'off');
        $hidden = array('user_id' => 0);
    ?>
    <?= form_open('erp/settings/planning_configuration_update', $attributes, $hidden); ?>

    <div class="card-body card-body-custom">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="year" class="form-label">Year <span class="text-danger">*</span></label>
                    <input type="number" min="2000" max="2099" step="1" class="form-control form-control-custom" id="year" name="year" 
                        value="<?php echo $result['year']; ?>" required placeholder="Enter Year">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="month" class="form-label">Month</label>
                    <select class="form-control form-control-custom" id="month" name="month" required>
                        <option value="" disabled selected>Select Month</option>
                        <?php
                        $months = [
                            "January", "February", "March", "April", "May", 
                            "June", "July", "August", "September", "October", 
                            "November", "December"
                        ];
                        foreach ($months as $month) {
                            $selected = ($result['month'] == $month) ? 'selected' : '';
                            echo "<option value='$month' $selected>$month</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="percentage" class="form-label">Percentage <span class="text-danger">*</span></label>
                    <input type="number" min="0" max="100" class="form-control form-control-custom" id="percentage" name="percentage"
                        value="<?php echo $result['percentage']; ?>" required placeholder="Enter Percentage">
                </div>
            </div>
        </div>
    </div>

    <div class="card-footer text-right">
        <button type="submit" class="btn btn-custom">
            <i data-feather="save" class="me-2"></i>Update
        </button>
    </div>

    <?= form_close(); ?>
</div>

<script>
    feather.replace();
</script>
