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
                padding: 7px 10px
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

        table th,
        table td {
            padding: 6px;
        }

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
        <table class="myTable" cellspacing="0" style="margin-top: 5px;">
            <tr style="text-align: center; border: 1px solid black;">
                <th class="tac" colspan="6">
                    FORM NO. 16
                </th>
            </tr>
            <tr style="text-align: center; border: 1px solid black;">
                <th class="tac" colspan="6">
                    PART B
                </th>
            </tr>
            <tr style="border: 1px solid black;">
                <th class="tac" colspan="6">
                    Certificate under section 203 of the Income-tax Act, 1961 for tax deducted at source on salary paid to an employee under section 192 or pension/interest income
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


            <tr style="border: 1px solid black; text-align: center">
                <th colspan="3" style="border: 1px solid black;">
                    Name and address of the Employer/Specified Bank
                </th>
                <th colspan="3" style="border: 1px solid black;">
                    Name and address of the Employee/Specified senior citizen
                </th>
            </tr>
            <tr style=" text-align: center;border: 1px solid black;">
                <th colspan="3" style="border: 1px solid black;font-weight: 400;">
                    <?= $user_info['company_name'] ?> <?= $user_info['contact_number'] ?> <?= $user_info['address_1'] ?>
                </th>
                <th colspan="3" style="border: 1px solid black;font-weight: 400;">
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
                <th style="border: 1px solid black; font-weight: 400; ">
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
        </table>

        <div class="header-title" style="margin: 7px; text-align: right;">Annexure - I</div>
        <table>
            <tr style="border: 1px solid black;">
                <th colspan="6" style="border: 1px solid black;">Details of Salary Paid and any other income and tax deducted</th>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">A.</td>
                <td colspan="3" style="border: 1px solid black;"> Whether opting out of taxation u/s 115BAC(1A)?</td>
                <td colspan="2" style="border: 1px solid black; text-align: center;">Yes</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">1.</td>
                <td colspan="3" class="section-title" style="border: 1px solid black;">Gross Salary</td>
                <td colspan="1" style="border: 1px solid black;text-align: center;">Rs</td>
                <td colspan="1" style="border: 1px solid black;text-align: center;">Rs</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(a)</td>
                <td colspan="3" style="border: 1px solid black;"> Salary as per provisions contained in section 17(1)</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">Rs.</td>
                <td colspan="1" style="border: 1px solid black;text-align: right;">1580853.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(b)</td>
                <td colspan="3" style="border: 1px solid black;"> Value of perquisites under section 17(2) (as per Form No. 12BA, wherever applicable)</td>
                <td colspan="1" style="border: 1px solid black;text-align: right;">Rs.</td>
                <td colspan="1" style="border: 1px solid black;text-align: right;">0.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(c)</td>
                <td colspan="3" style="border: 1px solid black;"> Profits in lieu of salary under section 17(3) (as per Form No. 12BA, wherever applicable)</td>
                <td colspan="1" style="border: 1px solid black;text-align: right;">Rs.</td>
                <td colspan="1" style="border: 1px solid black;text-align: right;">0.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(d)</td>
                <td colspan="3" style="border: 1px solid black;"> Total</td>
                <td colspan="1" style="border: 1px solid black;text-align: right;"></td>
                <td colspan="1" style="border: 1px solid black;text-align: right;">0.00</td>
            </tr>


            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(e)</td>
                <td colspan="3" style="border: 1px solid black;"> Reported total amount of salary received from other employer(s)</td>
                <td colspan="1" style="border: 1px solid black;text-align: right;">Rs.</td>
                <td colspan="1" style="border: 1px solid black;text-align: right;">0.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">2</td>
                <td style="border: 1px solid black;" colspan="3" class="section-title"> Less: Allowances to the extent exempt under section 10</td>
                <td colspan="1" style="border: 1px solid black;text-align: right;"></td>
                <td colspan="1" style="border: 1px solid black;text-align: right;"></td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(a)</td>
                <td colspan="3" style="border: 1px solid black;">Travel concession or assistance under section 10(5)</td>
                <td colspan="1" style="border: 1px solid black;text-align: right;">Rs.</td>
                <td colspan="1" style="border: 1px solid black;text-align: right;">0.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(b)</td>
                <td colspan="3" style="border: 1px solid black;">Death-cum-retirement gratuity under section 10(10)</td>
                <td colspan="1" style="border: 1px solid black;text-align: right;">Rs.</td>
                <td colspan="1" style="border: 1px solid black;text-align: right;">0.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(c)</td>
                <td colspan="3" style="border: 1px solid black;">Commuted value of pension under section 10(10A)</td>
                <td colspan="1" style="border: 1px solid black;text-align: right;">Rs.</td>
                <td colspan="1" style="border: 1px solid black;text-align: right;">0.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(d)</td>
                <td colspan="3" style="border: 1px solid black;"> Cash equivalent of leave salary encashment under section 10(10AA)</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">Rs.</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">0.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(e)</td>
                <td colspan="3" style="border: 1px solid black;">House rent allowance under section 10(13A)
                </td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">180150.00</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;"></td>
            </tr>
            <!--  -->
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(f)</td>
                <td colspan="3" style="border: 1px solid black;">Amount of any other exemption under section 10
                    [Note: Break-up to be filled and signed by employer in the table
                    provide at the bottom of this form]

                </td>
                <td colspan="1" style="border: 1px solid black; text-align: right;"></td>
                <td colspan="1" style="border: 1px solid black; text-align: right;"></td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(g)</td>
                <td colspan="3" style="border: 1px solid black;"> Total amount of any other exemption under section 10</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">0.00</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;"></td>
            </tr>

            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(h)</td>
                <td colspan="3" style="border: 1px solid black;"> Total amount of exemption claimed under section 10
                    [2(a)+2(b)+2(c)+2(d)+2(e)+2(f)+2(h)]</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;"></td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">180150.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">3.</td>
                <td colspan="3" style="border: 1px solid black;"> Total amount of salary received from current employer
                    [1(d)-2(h)]</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;"></td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">2377833.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">4.</td>
                <td colspan="3" style="border: 1px solid black;"> Less: Deductions under section 16</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;"></td>
                <td colspan="1" style="border: 1px solid black; text-align: right;"></td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(a)</td>
                <td colspan="3" style="border: 1px solid black;">Standard deduction under section 16(ia)</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">50000.00</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;"></td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(b)</td>
                <td colspan="3" style="border: 1px solid black;"> Entertainment allowance under section 16(ii)</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">0.00</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;"></td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(c)</td>
                <td colspan="3" style="border: 1px solid black;"> Tax on employment under section 16(iii)</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">2400.00</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;"></td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">5.</td>
                <td colspan="3" style="border: 1px solid black;"> Total amount of deductions under section 16 [4(a)+4(b)+4(c)]</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;"></td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">52400.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">6.</td>
                <td colspan="3" style="border: 1px solid black;"> Income chargeable under the head "Salaries" [(3+1(e)-5)]</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;"></td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">2325433.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">7.</td>
                <td colspan="5" style="border: 1px solid black;">Add: Any other income reported by the employee under as per section 192 (2B)
                </td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(a)</td>
                <td colspan="3" style="border: 1px solid black;"> Income (or admissible loss) from house property reported by
                    employee offered for TDS</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">0.00</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;"></td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(b)</td>
                <td colspan="3" style="border: 1px solid black;"> Income under the head Other Sources offered for TDS
                </td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">0.00</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;"></td>
            </tr>

            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">8.</td>
                <td colspan="3" style="border: 1px solid black;"> Total amount of other income reported by the employee
                    [7(a)+7(b)]
                </td>
                <td colspan="1" style="border: 1px solid black; text-align: right;"></td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">0.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">9.</td>
                <td colspan="3" style="border: 1px solid black;">Gross total income (6+8)
                </td>
                <td colspan="1" style="border: 1px solid black; text-align: right;"></td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">2325433.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">10.</td>
                <td colspan="3" style="border: 1px solid black;">Deductions under Chapter VI-A
                </td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">Gross Amount</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">Deductible Amount</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(a)</td>
                <td colspan="3" style="border: 1px solid black;">Deduction in respect of life insurance premia, contributions to
                    provident fund etc. under section 80C
                </td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">150000.00</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">150000.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(b)</td>
                <td colspan="3" style="border: 1px solid black;"> Deduction in respect of contribution to certain pension funds
                    under section 80CCC
                </td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">0.00</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">0.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(c)</td>
                <td colspan="3" style="border: 1px solid black;"> Deduction in respect of contribution by taxpayer to pension
                    scheme under section 80CCD (1)</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">0.00</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">0.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(d)</td>
                <td colspan="3" style="border: 1px solid black;">Total deduction under section 80C, 80CCC and 80CCD(1)
                </td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">150000.00</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">150000.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(e)</td>
                <td colspan="3" style="border: 1px solid black;"> Deductions in respect of amount paid/deposited to notified
                    pension scheme under section 80CCD (1B)</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">0.00</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">0.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(f)</td>
                <td colspan="3" style="border: 1px solid black;">Deduction in respect of contribution by Employer to pension
                    scheme under section 80CCD (2)</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">2400.00</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;"></td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(g)</td>
                <td colspan="3" style="border: 1px solid black;">Deduction in respect of health insurance premia under section
                    80D
                </td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">0.00</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">0.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">(h)</td>
                <td colspan="3" style="border: 1px solid black;">Deduction in respect of interest on loan taken for higher
                    education under section 80E
                </td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">0.00</td>
                <td colspan="1" style="border: 1px solid black; text-align: right;">0.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">11.</td>
                <td colspan="3" style="border: 1px solid black;">Aggregate of deductible amount under Chapter VI-A
                    [10(d)+10(e)+10(f)+10(g)+10(h)+10(i)+10(j)+10(l)] </td>
                <td colspan="2" style="border: 1px solid black; text-align: right;">150000.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">12.</td>
                <td colspan="3" style="border: 1px solid black;">Total taxable income (9-11) </td>
                <td colspan="2" style="border: 1px solid black; text-align: right;">2175433.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">13.</td>
                <td colspan="3" style="border: 1px solid black;">Tax on total income
                </td>
                <td colspan="2" style="border: 1px solid black; text-align: right;">465132.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">14.</td>
                <td colspan="3" style="border: 1px solid black;">Rebate under section 87A, if applicable </td>
                <td colspan="2" style="border: 1px solid black; text-align: right;">0.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">15.</td>
                <td colspan="3" style="border: 1px solid black;">Surcharge, wherever applicable</td>
                <td colspan="2" style="border: 1px solid black; text-align: right;">0.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">16.</td>
                <td colspan="3" style="border: 1px solid black;">Health and education cess
                </td>
                <td colspan="2" style="border: 1px solid black; text-align: right;">18605.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">17.</td>
                <td colspan="3" style="border: 1px solid black;">Tax payable (13+15+16-14) </td>
                <td colspan="2" style="border: 1px solid black; text-align: right;">483737.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">18.</td>
                <td colspan="3" style="border: 1px solid black;">Less: Relief under section 89 (attach details) </td>
                <td colspan="2" style="border: 1px solid black; text-align: right;">0.00</td>
            </tr>
            <tr style="border: 1px solid black;">
                <td style="border: 1px solid black;text-align: center;">19.</td>
                <td colspan="3" style="border: 1px solid black;">Net tax payable (17-18) </td>
                <td colspan="2" style="border: 1px solid black; text-align: right;">483737.00</td>
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
                    <b>Full Name:</b>
                </th>
            </tr>


        </table>
        <br />

        <div style="position: absolute; bottom: 10px; right: 10px;">
            <img src="http://localhost/gpsadvanced/public/uploads/form16/1718622655140.png" alt="Right Side Image" style="height: 110px;">
        </div>

    </div>
</body>

</html>