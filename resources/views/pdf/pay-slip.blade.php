<html
    lang="{{ str_replace("_", "-", app()->getLocale()) }}"
    dir="{{ app()->getLocale() == "ar" ? "rtl" : "ltr" }}"
>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Payslip Report</title>
    </head>
    <body>
        @php
            \Illuminate\Support\Number::useLocale(app()->getLocale());
        @endphp

        <style>
            body {
                font-family: 'Amiri', sans-serif !important;
            }
        </style>
        <h1 style="font-weight: bold; font-size: 16px; text-align: center">
            {{ trans("site.pay_slip") }}
        </h1>
        <h2 style="font-weight: lighter; font-size: 16px; text-align: center">
            {{ $company_name }}
        </h2>
        <table>
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <span>
                            <span style="width: 50%; font-size: 15px">
                                {{ trans("site.date_of_joining") }} :
                            </span>
                            <span
                                style="
                                    width: 50%;
                                    font-size: 15px;
                                    font-weight: bold;
                                "
                            >
                                {{ $date_of_joining }}
                            </span>
                        </span>
                    </td>

                    <td>
                        <span>
                            <span style="width: 50%; font-size: 15px">
                                {{ trans("site.worked_days") }} :
                            </span>
                            <span
                                style="
                                    width: 50%;
                                    font-size: 15px;
                                    font-weight: bold;
                                "
                            >
                                {{ $worked_days }}
                            </span>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span>
                            <span style="width: 50%; font-size: 15px">
                                {{ trans("site.pay_period") }} :
                            </span>
                            <span
                                style="
                                    width: 50%;
                                    font-size: 15px;
                                    font-weight: bold;
                                "
                            >
                                {{ $pay_period }}
                            </span>
                        </span>
                    </td>
                    <td>
                        <span>
                            <span style="width: 50%; font-size: 15px">
                                {{ trans("site.designation") }} :
                                {{ trans("site.$role") }}
                            </span>
                            <span
                                style="
                                    width: 50%;
                                    font-size: 15px;
                                    font-weight: bold;
                                "
                            ></span>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span>
                            <span style="width: 50%; font-size: 15px">
                                {{ trans("site.full_name") }} :
                            </span>
                            <span
                                style="
                                    width: 50%;
                                    font-size: 15px;
                                    font-weight: bold;
                                "
                            >
                                {{ $full_name }}
                            </span>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>

        <!--Earnings Table-->
        <table border="1">
            <tr style="background-color: #d9d9d9">
                <th>
                    {{ trans("site.earnings") }}
                </th>
                <th>
                    {{ trans("site.amount") }}
                </th>
            </tr>
            @foreach ($earnings as $item)
                <tr>
                    <td style="text-align: center">
                        <span>{{ $item["label"] }}</span>
                        @if (isset($item["errors"]))
                            @if (is_array($item["errors"]))
                                @foreach ($item["errors"] as $err)
                                    <span style="color: red">
                                        {{ $err["message"] }}
                                    </span>
                                @endforeach
                            @elseif (is_string($item["errors"]))
                                <span style="color: red">
                                    {{ $item["errors"] }}
                                </span>
                            @endif
                        @endif
                    </td>
                    <td style="text-align: center">{{ $item["value"] }}</td>
                </tr>
            @endforeach

            <!--Total Earnings-->

            <tr>
                <td style="padding: 12px"></td>
                <td></td>
            </tr>
            <tr style="background-color: #3d6f78">
                <td style="font-weight: bolder">
                    {{ trans("site.total_earnings") }}
                </td>
                <td style="text-align: center">{{ $total_earnings }}</td>
            </tr>
        </table>

        <!--Deductions Table-->
        <table border="1">
            <tr>
                <th colspan="2"></th>
            </tr>
            <tr style="background-color: #d9d9d9">
                <th>
                    {{ trans("site.deductions") }}
                </th>
                <th>
                    {{ trans("site.amount") }}
                </th>
            </tr>
            @foreach ($deductions as $item)
                <tr>
                    <td style="text-align: center">
                        <span>{{ $item["label"] }}</span>
                        @if (isset($item["errors"]))
                            @if (is_array($item["errors"]))
                                @foreach ($item["errors"] as $err)
                                    <span style="color: red">
                                        {{ $err["message"] }}
                                    </span>
                                @endforeach
                            @elseif (is_string($item["errors"]))
                                <span style="color: red">
                                    {{ $item["errors"] }}
                                </span>
                            @endif
                        @endif
                    </td>
                    <td style="text-align: center">{{ $item["value"] }}</td>
                </tr>
            @endforeach

            <!--Total Earnings-->

            <tr>
                <td></td>
                <td></td>
            </tr>
            <tr style="background-color: #3d6f78">
                <td style="font-weight: bolder">
                    {{ trans("site.total_deductions") }}
                </td>
                <td style="text-align: center">{{ $total_deductions }}</td>
            </tr>
        </table>

        <!--Total-->

        <table border="1">
            <tr>
                <th colspan="2"></th>
            </tr>
            <tr style="background-color: #d9d9d9">
                <th colspan="2" style="font-weight: bolder">
                    {{ trans("site.total") }}
                </th>
            </tr>
            <tr style="background-color: #3d6f78">
                <td style="text-align: center">{{ $total_pay }}</td>
                <td style="text-align: center">
                    {{ \Illuminate\Support\Number::spell($total_pay) }}
                </td>
            </tr>
        </table>
    </body>
</html>
