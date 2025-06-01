<html
    lang="{{ str_replace("_", "-", app()->getLocale()) }}"
    dir="{{ app()->getLocale() == "ar" ? "rtl" : "ltr" }}"
>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Patient report</title>
    </head>
    <body>
        <style>
            body {
                font-family: 'poppins', sans-serif !important;
            }

            .data-table {
                border-collapse: collapse;
                border: 1px solid #c7c7cc;
                min-width: 100%;
                max-width: 100%;
            }

            .container {
                width: 100%;
                margin-bottom: 150px;
            }

            .left {
                width: 65%;
                float: left;
            }
        </style>
        <div style="text-align: center">
            <h1 style="font-weight: bold">
                {{ trans("site.patient_report") }}
            </h1>
        </div>
        <div class="container">
            <div class="left">
                <p>
                    <span style="width: 50%; font-size: 12px">
                        {{ trans("site.full_name") }} :
                    </span>
                    <span
                        style="width: 50%; font-size: 12px; font-weight: bold"
                    >
                        {{ $customer->user?->full_name }}
                    </span>
                </p>

                <p>
                    <span style="width: 50%; font-size: 12px">
                        {{ trans("site.age") }} :
                    </span>
                    <span
                        style="width: 50%; font-size: 12px; font-weight: bold"
                    >
                        {{ round($customer->birth_date?->diffInYears(now())) }}
                    </span>
                </p>

                <p>
                    <span style="width: 50%; font-size: 12px">
                        {{ trans("site.phone") }} :
                    </span>
                    <span
                        style="width: 50%; font-size: 12px; font-weight: bold"
                    >
                        {{ $customer->user?->phone }}
                    </span>
                </p>
            </div>
        </div>

        <!--Medical Record table Table-->
        <div>
            <h1 style="text-align: start">
                {{ trans("site.medical_records") }}
            </h1>
            <table border="1" class="data-table">
                <tr style="background-color: #d9d9d9">
                    {{-- <th class="left-headers-table"> --}}
                    {{-- </th> --}}
                    {{-- <th class="right-headers-table"> --}}
                    {{-- </th> --}}

                    <th style="width: 10%">
                        {{ trans("site.doctor") }}
                    </th>
                    <th style="width: 17%">
                        {{ trans("site.summary") }}
                    </th>

                    <th style="width: 17%">
                        {{ trans("site.diagnosis") }}
                    </th>

                    <th style="width: 17%">
                        {{ trans("site.treatment") }}
                    </th>

                    <th style="width: 17%">
                        {{ trans("site.allergies") }}
                    </th>

                    <th style="width: 17%">
                        {{ trans("site.notes") }}
                    </th>
                </tr>
                @foreach ($medicalRecords as $item)
                    <tr>
                        {{-- <td class="left-cells"></td> --}}
                        {{-- <td class="right-cells"></td> --}}

                        <td>
                            {{ $item->clinic?->user?->full_name }}
                        </td>
                        <td>
                            {{ $item->summary }}
                        </td>
                        <td>
                            {{ $item->diagnosis }}
                        </td>
                        <td>
                            {{ $item->treatment }}
                        </td>
                        <td>
                            {{ $item->allergies }}
                        </td>
                        <td>
                            {{ $item->notes }}
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>

        <!--Appointments tables Table-->
        <div style="margin-top: 35px">
            <h1 style="text-align: start">
                {{ trans("site.appointments") }}
            </h1>
            <table border="1" class="data-table">
                <tr style="background-color: #d9d9d9">
                    <th style="width: 25%">
                        {{ trans("site.doctor") }}
                    </th>
                    <th style="width: 25%">
                        {{ trans("site.service") }}
                    </th>
                    <th style="width: 25%">
                        {{ trans("site.date_time") }}
                    </th>
                    <th style="width: 25%">
                        {{ trans("site.status") }}
                    </th>
                </tr>
                @foreach ($appointments as $item)
                    <tr>
                        {{-- <td class="left-cells"></td> --}}
                        {{-- <td class="right-cells"></td> --}}
                        <td>
                            {{ $item->clinic->user->full_name }}
                        </td>

                        <td>
                            {{ $item->service->name }}
                        </td>

                        <td>
                            {{ $item->date_time?->format("Y-m-d H:i") }}
                        </td>

                        <td>
                            {{ trans("site." . $item->status) }}
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>

        <!-- Prescriptions -->
        <div style="margin-top: 35px">
            <h1 style="text-align: start">
                {{ trans("site.prescriptions") }}
            </h1>
            @foreach ($prescriptions as $prescription)
                <div style="margin-top: 35px; border: 1px solid #9e9e9e">
                    <table border="1" class="data-table">
                        <tr style="background-color: #d9d9d9">
                            <th style="width: 50%">
                                {{ trans("site.doctor") }}
                            </th>
                            <th style="width: 50%">
                                {{ trans("site.next_visit") }}
                            </th>
                        </tr>
                        <tbody>
                            <tr>
                                <td>
                                    {{ $prescription->clinic?->user?->full_name }}
                                </td>
                                <td>
                                    {{ $prescription->next_visit?->format("Y-m-d H:i") }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if (isset($prescription->other_data))
                    <div>
                        <h2 style="text-align: start">
                            {{ trans("validation.attributes.other_data") }}
                        </h2>
                        <table border="1" class="data-table">
                            <tr style="background-color: #d9d9d9">
                                <th style="width: 35%">Key</th>
                                <th style="width: 65%">Value</th>
                            </tr>
                            <tbody>
                                @foreach ($prescription->other_data as $otherData)
                                    <tr>
                                        <td>
                                            {{ $otherData["key"] ?? "" }}
                                        </td>
                                        <td>
                                            {{ $otherData["value"] ?? "" }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div style="margin-top: 35px">
                    <table border="1" class="data-table">
                        <tr style="background-color: #d9d9d9">
                            <th style="width: 25%" class="">
                                {{ trans("site.medicine") }}
                            </th>
                            <th style="width: 25%" class="">
                                {{ trans("site.dosage") }}
                            </th>
                            <th style="width: 25%">
                                {{ trans("site.dose_interval") }}
                            </th>
                            <th style="width: 25%">
                                {{ trans("site.comment") }}
                            </th>
                        </tr>
                        @foreach ($prescription->medicinePrescriptions as $item)
                            <tr>
                                {{-- <td class="left-cells"></td> --}}
                                {{-- <td class="right-cells"></td> --}}
                                <td>
                                    {{ $item->medicine?->name }}
                                </td>

                                <td>
                                    {{ $item->dosage }}
                                </td>

                                <td>
                                    {{ $item->dose_interval }}
                                </td>

                                <td>
                                    {{ $item->comment }}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @endforeach
        </div>
    </body>
</html>
