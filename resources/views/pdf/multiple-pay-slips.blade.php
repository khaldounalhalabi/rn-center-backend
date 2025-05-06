<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Pay Slip Report</title>
</head>
<body>
<style>
    body {
        font-family: poppins, sans-serif;
    }

    .left-headers-table {
        padding: 1px;
        text-align: center;
        border-bottom: 1px solid #C7C7CC;
        min-width: 75%;
        max-width: 75%;
    }

    .right-headers-table {
        padding: 1px;
        text-align: center;
        border-bottom: 1px solid #C7C7CC;
        min-width: 25%;
        max-width: 25%;
    }

    .data-table {
        border-collapse: collapse;
        border: 1px solid #C7C7CC;
        min-width: 100%;
        max-width: 100%;
    }

    .left-cells {
        padding: 5px 10px 5px 10px;
        text-align: left;
        border-bottom: 1px solid #C7C7CC;
        border-right: 1px solid #C7C7CC;
        border-collapse: collapse;
        font-weight: bold;
    }

    .right-cells {
        padding: 5px 10px 5px 10px;
        text-align: right;
        border-bottom: 1px solid #C7C7CC;
        font-weight: bold;
    }

    .total-cell {
        text-align: right !important;
        font-weight: bold;
    }

    .container {
        width: 100%;
        margin-bottom: 150px;
    }

    .left, .right {
        margin-top: 10px;
    }

    .left {
        width: 65%;
        float: left;
    }

    .right {
        width: 35%;
        float: right;
    }

    /* Add page break after each pay slip */
    .page-break {
        page-break-after: always;
    }
</style>

@foreach($data as $payslipData)
    <div class="pay-slip">
        <div style="text-align: center">
            <h1 style="font-weight: bold; font-size: 16px">Pay Slip</h1>
            <h2 style="font-weight: lighter; font-size: 16px; line-height: 24px;">{{$company_name}}</h2>
        </div>
        <div class="container">
            <div class="left">
                <p>
                    <span style="width: 50%; font-size: 12px">Date Of Joining : </span>
                    <span
                        style="width: 50%; font-size: 12px; font-weight: bold">{{$payslipData['date_of_joining']}}</span>
                </p>

                <p>
                    <span style="width: 50%; font-size: 12px">Pay Period : </span>
                    <span style="width: 50%; font-size: 12px; font-weight: bold">{{$payslipData['pay_period']}}</span>
                </p>

                <p>
                    <span style="width: 50%; font-size: 12px">Worked Days : </span>
                    <span style="width: 50%; font-size: 12px; font-weight: bold">{{$payslipData['worked_days']}}</span>
                </p>
            </div>

            <div class="right">
                <p>
                    <span style="width: 50%; font-size: 12px">Employee name : </span>
                    <span
                        style="width: 50%; font-size: 12px; font-weight: bold">{{$payslipData['full_name']}}</span>
                </p>
                <p>
                    <span style="width: 50%; font-size: 12px">Designation : {{$payslipData['role']}}</span>
                    <span style="width: 50%; font-size: 12px; font-weight: bold"></span>
                </p>
            </div>
        </div>

        <!--Earnings Table-->
        <div>
            <table class="data-table">
                <tr style="background-color: #D9D9D9;">
                    <th class="left-headers-table">
                        Earnings
                    </th>
                    <th class="right-headers-table">
                        Amount
                    </th>
                </tr>
                @foreach($payslipData['earnings'] as $item)
                    <tr>
                        <td class="left-cells">{{$item['label']}}</td>
                        <td class="right-cells">{{$item['value']}}</td>
                    </tr>
                @endforeach

                <!--Total Earnings-->
                <tr>
                    <td class="left-cells" style="padding: 12px"></td>
                    <td class="right-cells"></td>
                </tr>
                <tr>
                    <td class="left-cells total-cell">Total Earnings</td>
                    <td class="right-cells">{{$payslipData['total_earnings']}}</td>
                </tr>
            </table>
        </div>

        <!--Deductions Table-->
        <div style="margin-top: 35px">
            <table class="data-table">
                <tr style="background-color: #D9D9D9;">
                    <th class="left-headers-table">
                        Deductions
                    </th>
                    <th class="right-headers-table">
                        Amount
                    </th>
                </tr>
                @foreach($payslipData['deductions'] as $item)
                    <tr>
                        <td class="left-cells">{{$item['label']}}</td>
                        <td class="right-cells">{{$item['value']}}</td>
                    </tr>
                @endforeach

                <!--Total Earnings-->
                <tr>
                    <td class="left-cells" style="padding: 12px"></td>
                    <td class="right-cells"></td>
                </tr>
                <tr>
                    <td class="left-cells total-cell">Total Deductions</td>
                    <td class="right-cells">{{$payslipData['total_deductions']}}</td>
                </tr>
            </table>
        </div>

        <!--Total-->
        <div style="text-align: center">
            <p style="font-weight:bold;font-size: 47px; padding: 0; margin: 30px; font-family: poppins, sans-serif">
                {{$payslipData['total_pay']}}
            </p>
            <p style="text-transform: capitalize; font-weight: bold;font-size: 16px">
                {{\Illuminate\Support\Number::spell($payslipData['total_pay'])}}
            </p>
        </div>
    </div>

    <!-- Add page break after each pay slip -->
    <div class="page-break"></div>

@endforeach


<!--Total-->
<div style="text-align: center">
    <p style="font-weight:bold;font-size: 47px; padding: 0; margin: 30px; font-family: poppins, sans-serif">
        total payslips : {{$total}}
    </p>
    <p style="text-transform: capitalize; font-weight: bold;font-size: 16px">
        {{\Illuminate\Support\Number::spell($total)}}
    </p>
</div>
</body>
</html>
