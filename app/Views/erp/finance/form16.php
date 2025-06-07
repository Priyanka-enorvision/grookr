<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
        html,
        body {
            margin: 7px;
            padding: 0;
        }

        img {
            /* max-width: auto;
            height: auto; */
            border: none;
        }

        table {
            width: 100%;
            table-layout: fixed;
            word-wrap: break-word;
            /* border: 1px solid black; */

            td,
            th {
                height: 35px;
                padding: 5px;
                font-size: 12px;
                border-collapse: collapse;
            }

            .no-border {
                border: none;
            }

            .no-br {
                border-right: 0px solid white;
            }

        }

        .table2 {
            border-collapse: collapse;

            td {
                border: 0;
                padding: 5px 10px
            }
        }

        .first-half {
            float: left;
            width: 50%;
        }

        .second-half {
            float: right;
            width: 50%;
        }

        .tac {
            text-align: center
        }

        .tar {
            text-align: right
        }

        .tat {
            vertical-align: top;
        }

        .tal {
            text-align: left
        }

        .border-td {
            border-top: 1px solid black;
            border-bottom: 1px solid black;
        }

        .border {
            border: 1px solid black;
        }

        /* .form16 {
            padding: 12px;
        } */


        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.2;
            z-index: -1;
        }
    </style>
</head>

<body>
    <div class="watermark">
        <img src="http://localhost/gpsadvanced/public/uploads/form16/watermark.png" alt="Watermark" style="width: 500px;">
    </div>
    <div class="form16">
        <table style="border:none;">
            <tr>
                <td style="text-align: left; padding-bottom: 0;">
                    <img src="http://localhost/gpsadvanced/public/uploads/form16/download.png" alt="Left Image" style="height: 75px;width: 480px;">
                </td>
                <td style="padding-bottom: 0; padding-left: 299px;padding-top: 0;">
                    <img src="http://localhost/gpsadvanced/public/uploads/form16/income_logo.png" alt="Right Image" style="height: 75px;">
                </td>
            </tr>
        </table>
        <table class="myTable" cellspacing="0">
            <tr style="text-align: center; border: 1px solid black;">
                <th class="tac" colspan="6">
                    FORM NO. 16
                </th>
            </tr>
            <tr style="text-align: center; border: 1px solid black;">
                <th style="text-align: center;font-weight: 400;" colspan="6">
                    [See rule 31(1)(a)]
                </th>
            </tr>
            <tr style="text-align: center; border: 1px solid black;">
                <th class="tac" colspan="6">
                    PART A
                </th>
            </tr>
            <tr style="border: 1px solid black;">
                <th class="tac" colspan="6">
                    Certificate under Section 203 of the Income-tax Act, 1961 for tax deducted at source on salary paid to an employee under section 192 or pension/interest income
                    of specified senior citizen under section 194P

                </th>
            </tr>
            <tr style="border: 1px solid black;">
                <th colspan="3" style="border: 1px solid black;">
                    Certificate No. - <p style="display: inline;"> <?= $certificateNumber ?? 'N/A' ?></p>
                </th>
                <th colspan="3" style="border: 1px solid black;">
                    Last updated on -<p style="display: inline;"> 19/May/2024</p>
                </th>
            </tr>


            <tr style="border: 1px solid black; text-align: center;">
                <th colspan="3" style="border: 1px solid black;">
                    Name and Address of the Employer/Specified Bank
                </th>
                <th colspan="3" style="border: 1px solid black;">
                    Name and Address of the Employee/Specified Senior Citizen
                </th>
            </tr>
            <tr style="text-align: center; border: 1px solid black;">
                <th colspan="3" style="border: 1px solid black; font-weight: 400;">
                    <?= $user_info['company_name'] ?> <?= $user_info['contact_number'] ?> <?= $user_info['address_1'] ?>
                </th>
                <th colspan="3" style="border: 1px solid black; font-weight: 400;">
                    <?= $user_info['first_name'] ?> <?= $user_info['last_name'] ?> <?= $user_info['address_2'] ?>
                </th>
            </tr>

            <tr style="border: 1px solid black; text-align: center;">
                <th style="border: 1px solid black;">
                    PAN of the Deductor
                </th>
                <th colspan="2" style="border: 1px solid black;">
                    TAN of the Deductor
                </th>
                <th style="border: 1px solid black;">
                    PAN of the
                    Employee/Specified senior
                    citizen

                </th>
                <th colspan="2" style="border: 1px solid black;">
                    Employee Reference No. provided by the
                    Employer/Pension Payment order no. provided
                    by the Employer (If available)
                </th>
            </tr>
            <tr style="border: 1px solid black;text-align: center;">
                <th style="border: 1px solid black; font-weight: 400;">
                    AAECI4976L
                </th>
                <th colspan="2" style="border: 1px solid black; font-weight: 400;">
                    DELI12286B
                </th>
                <th style="border: 1px solid black; font-weight: 400;">
                    GHFPK3329L
                </th>
                <th colspan="2" style="border: 1px solid black;font-weight: 400;">

                </th>
            </tr>
            <tr style="border: 1px solid black;text-align: center;">
                <th colspan="3" style="border: 1px solid black;">
                    CIT (TDS)
                </th>
                <th colspan="1" style="border: 1px solid black;">
                    Assessment Year
                </th>
                <th colspan="2" style="border: 1px solid black;">
                    Period with the Employer
                </th>
            </tr>
            <tr style="text-align: center;border: 1px solid black;">
                <td colspan="3" style="border: none; font-weight: 400;">
                    The Commissioner of Income Tax (TDS)
                    Aayakar Bhawan, District Centre, 6th Floor Room no 610, Hall no.
                    4 , Luxmi Nagar, Delhi - 110092
                </td>
                <td colspan="1" style="border: 1px solid black; font-weight: 400;">
                    <?php
                    $created_at = $tax_list[0]['created_at']; // Full created_at value
                    $date = strtotime($created_at);
                    $year = date('Y', $date);
                    $month = date('m', $date);

                    if ($month >= 4) {
                        $financial_year = $year . '-' . ($year + 1);
                    } else {
                        $financial_year = ($year - 1) . '-' . $year;
                    }

                    echo $financial_year;
                    ?>
                </td>

                <th colspan="1" style="border: 1px solid black;">
                    From <br> <?php
                                $created_at = trim($user_info['created_at']);
                                $formatted_date = date('d- M-Y', strtotime($created_at));
                                echo $formatted_date;
                                ?>
                </th>
                <th colspan="1" style="border: 1px solid black;">
                    To <br> <?php
                            $formatted_date = date('d-M-Y');
                            echo $formatted_date;
                            ?>
                </th>
            </tr>


            <tr style="border: 1px solid black;">
                <th class="tac" colspan="6" style="border: 1px solid black;">
                    Summary of amount paid/credited and tax deducted at source thereon in respect of the employee
                </th>
            </tr>
            <tr style="border: 1px solid black;text-align: center;">
                <th style="border: 1px solid black;">
                    Quarter(s)
                </th>
                <th style="border: 1px solid black;">
                    Receipt Numbers of
                    original quarterly
                    statements of TDS
                    under sub-section (3) of
                    section 200
                </th>
                <th style="border: 1px solid black;">
                    Amount
                    paid/credited
                </th>
                <th colspan="2" style="border: 1px solid black;">
                    Amount of tax
                    deducted
                    (Rs. )
                </th>
                <th style="border: 1px solid black;">
                    Amount of tax
                    deposited/remitted
                    (Rs. )
                </th>
            </tr>
            <tr style="border: 1px solid black;text-align: center;">
                <th style="border: 1px solid black;font-weight: 400;">Q4
                </th>
                <th style="border: 1px solid black;font-weight: 400;">FXCRLVKY
                </th>
                <th style="border: 1px solid black;font-weight: 400;">399654.00
                </th>
                <th colspan="2" style="border: 1px solid black;font-weight: 400;">
                    107569.00
                </th>
                <th style="border: 1px solid black;font-weight: 400;">
                    107569.00
                </th>
            </tr>
            <tr style="border: 1px solid black;">
                <th style="border: 1px solid black; text-align: center;">
                    Total (Rs.)
                </th>
                <th style="border: 1px solid black;">
                </th>
                <th style="border: 1px solid black;">
                </th>
                <th colspan="2" style="border: 1px solid black;">

                </th>
                <th style="border: 1px solid black;">

                </th>
            </tr>


            <tr style="border: 1px solid black;">
                <th colspan="6" style="border: 1px solid black;  text-align: center ;">
                    <p style="font-size: 13px;">
                        I. DETAILS OF TAX DEDUCTED AND DEPOSITED IN THE CENTRAL GOVERNMENT ACCOUNT THROUGH BOOK ADJUSTMENT
                        <br />
                        (The deductor to provide payment wise details of tax deducted
                        and deposited with respect to the deductee)
                    </p>
                </th>
            </tr>
            <tr style="border: 1px solid black;text-align: center ;">
                <th rowspan='2' style="border: 1px solid black;">
                    Sl. No.
                </th>
                <th rowspan='2' style="border: 1px solid black;">
                    Tax deposited
                    in respect of the
                    deductee (Rs.)
                </th>
                <th colspan='4' style="border: 1px solid black;">
                    Book Identification Number (BIN)
                </th>
            </tr>
            <tr style="border: 1px solid black;">
                <th style="border: 1px solid black;">
                    Receipt numbers of
                    Form No. 24G
                </th>
                <th style="border: 1px solid black;">
                    DDO serial
                    number in Form
                    No. 24G

                </th>
                <th style="border: 1px solid black;">
                    Date of Transfer
                    voucher
                    (dd/mm/yyyy)
                </th>
                <th style="border: 1px solid black;">
                    Status of
                    Matching with
                    Form No.24G
                </th>
            </tr>
            <tr style="border: 1px solid black;text-align: center ;">
                <td style="border: 1px solid black;">1 .
                </td>
                <td style="border: 1px solid black;">35856.00
                </td>
                <td style="border: 1px solid black;">0510002
                </td>
                <td style="border: 1px solid black;">07-02-2024
                </td>
                <td style="border: 1px solid black;">18033
                </td>
                <td style="border: 1px solid black;">F
                </td>
            </tr>
            <tr style="border: 1px solid black;">
                <th style="border: 1px solid black;text-align: center ;">
                    Total(Rs)
                </th>
                <td style="border: 1px solid black;">
                </td>
                <td colspan='4' style="border: 1px solid black;">
                </td>
            </tr>

            <tr style="border: 1px solid black;">
                <th colspan="6" style="border: 1px solid black;  text-align: center">
                    <p style="font-size: 13px;">
                        II. DETAILS OF TAX DEDUCTED AND DEPOSITED IN THE CENTRAL GOVERNMENT ACCOUNT THROUGH CHALLAN<br />
                        (The deductor to provide payment wise details of tax deducted
                        and deposited with respect to the deductee)
                    </p>
                </th>
            </tr>
            <tr style="border: 1px solid black; text-align: center ;">
                <th rowspan='2' style="border: 1px solid black;">
                    Sl. No.
                </th>
                <th rowspan='2' style="border: 1px solid black;">
                    Tax deposited
                    in respect of the
                    deductee (Rs.)
                </th>
                <th colspan='4' style="border: 1px solid black;">
                    Challan Identification Number (CIN)
                </th>
            </tr>
            <tr style="border: 1px solid black;">
                <th style="border: 1px solid black;">
                    BSR Code of the
                    Bank Branch
                </th>
                <th style="border: 1px solid black;">
                    Date on which tax
                    deposited (dd/mm/yyyy)
                </th>
                <th style="border: 1px solid black;">
                    Challan Serial
                    Number

                </th>
                <th style="border: 1px solid black;">
                    Status of
                    matching with OLTAS
                </th>
            </tr>
            <tr style="border: 1px solid black; text-align: center ;">
                <td style="border: 1px solid black;">1.
                </td>
                <td style="border: 1px solid black;">35856.00
                </td>
                <td style="border: 1px solid black;">0510002
                </td>
                <td style="border: 1px solid black;">07-02-2024
                </td>
                <td style="border: 1px solid black;">18033
                </td>
                <td style="border: 1px solid black;">F
                </td>
            </tr>
            <tr style="border: 1px solid black;">
                <th style="border: 1px solid black; text-align: center ;">
                    Total(Rs)
                </th>
                <td style="border: 1px solid black;">
                </td>
                <td colspan='4' style="border: 1px solid black;">
                </td>
            </tr>
            <tr style="border: 1px solid black; text-align: center ;">
                <th colspan='6' style="border: 1px solid black;">
                    Verification
                </th>
            </tr>
            <tr style="border: 1px solid black;">
                <td colspan='6' style="border: 1px solid black;">
                    I,…………….., son/daughter of …………. working in the capacity of ……. (designation) do hereby certify that a
                    sum of Rs. ………….. [Rs. ………. (in words)] has been deducted and deposited to the credit of the Central
                    Government. I further certify that the information given above is true, complete and correct and is based on the
                    books of account, documents, TDS statements, TDS deposited and other available records.
                </td>
            </tr>
            <tr style="border: 1px solid black;">
                <th style="border: 1px solid black;">
                    Place
                </th>
                <td colspan="2" style="border: 1px solid black;">
                    NEW DELHI
                </td>
                <th colspan="3" style="border: 1px solid black; text-align: center;">
                    (Signature of person responsible for deduction of tax)
                </th>
            </tr>
            <tr style="border: 1px solid black;">
                <th style="border: 1px solid black;">
                    Date
                </th>
                <td colspan="2" style="border: 1px solid black;">
                    20-May-2024

                </td>
                <th colspan="3" style="border: 1px solid black;">

                </th>
            </tr>
            <tr style="border: 1px solid black;">
                <td colspan="3" style="border: 1px solid black;">
                    <b>Designation: </b>
                </td>
                <td colspan="3" style="border: 1px solid black;">
                    <b>Full Name:</b>
                </td>
            </tr>
        </table>
        <br />

        <p style="font-size: 11px;"><strong>Notes:</strong><br />1. Part B (Annexure) of the certificate in Form No.16 shall be issued by the employer.<br>
            2. If an assessee is employed under one employer during the year, Part 'A' of the certificate in Form No.16 issued for the quarter ending on 31st March of the financial year shall contain the details
            of tax deducted and deposited for all the quarters of the financial year.<br>
            3. If an assessee is employed under more than one employer during the year, each of the employers shall issue Part A of the certificate in Form No.16 pertaining to the period for which such
            assessee was employed with each of the employers. Part B (Annexure) of the certificate in Form No. 16 may be issued by each of the employers or the last employer at the option of the assessee.
            <br> 4. To update PAN details in Income Tax Department database, apply for 'PAN change request' through NSDL or UTITSL.
        </p>
        <br />
        <div class="legend-title" style="font-weight: bold;font-size: 13px;">Legend used in Form 16</div>
        <div class="note" style="font-weight: bold; font-size: 13px;">* Status of matching with OLTAS</div>

        <table style="border: 1px solid black;">
            <thead style="background-color: lightblue; text-align: center; ">
                <tr style="border: 1px solid black;">
                    <th colspan="1" style="border: 1px solid black;">Legend</th>
                    <th colspan="1" style="border: 1px solid black;">Description</th>
                    <th colspan="4" style="border: 1px solid black;">Definition</th>
                </tr>
            </thead>
            <tbody style="border: 1px solid black;  text-align: center ;">
                <tr style="border: 1px solid black;">
                    <td colspan="1" style="border: 1px solid black;">U</td>
                    <td colspan="1" style="border: 1px solid black;">Unmatched</td>
                    <td colspan="4" style="border: 1px solid black;">Deductors have not deposited taxes or have furnished incorrect particulars of tax payment. Final credit will be reflected only when payment details in bank match with details of deposit in TDS / TCS statement.</td>
                </tr>
                <tr style="border: 1px solid black;">
                    <td colspan="1" style="border: 1px solid black;">P</td>
                    <td colspan="1" style="border: 1px solid black;">Provisional</td>
                    <td colspan="4" style="border: 1px solid black;">Provisional tax credit is effected only for TDS / TCS Statements filed by Government deductors. "P" status will be changed to Final (F) on verification of payment details submitted by Pay and Accounts Officer (PAO).</td>
                </tr>
                <tr style="border: 1px solid black;">
                    <td colspan="1" style="border: 1px solid black;">F</td>
                    <td colspan="1" style="border: 1px solid black;">Final</td>
                    <td colspan="4" style="border: 1px solid black;">In case of non-government deductors, payment details of TDS / TCS deposited in bank by deductor have matched with the payment details mentioned in the TDS / TCS statement filed by the deductors. In case of government deductors, details of TDS / TCS booked in Government account have been verified by Pay & Accounts Officer (PAO).</td>
                </tr>
                <tr style="border: 1px solid black;">
                    <td colspan="1" style="border: 1px solid black;">O</td>
                    <td colspan="1" style="border: 1px solid black;">Overbooked</td>
                    <td colspan="4" style="border: 1px solid black;">Payment details of TDS / TCS deposited in bank by deductor have matched with details mentioned in the TDS / TCS statement but the amount is over claimed in the statement. Final (F) credit will be reflected only when deductor reduces claimed amount in the statement or makes new payment for excess amount claimed in the statement.</td>
                </tr>
            </tbody>
        </table>

        <div style="position: absolute; bottom: 10px; right: 10px;">
            <img src="http://localhost/gpsadvanced/public/uploads/form16/1718622655140.png" alt="Right Side Image" style="height: 110px;">
        </div>

    </div>
</body>

</html>