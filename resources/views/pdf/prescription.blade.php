<html
    lang="{{ str_replace("_", "-", app()->getLocale()) }}"
    dir="{{ app()->getLocale() == "ar" ? "rtl" : "ltr" }}"
>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>{{ trans("site.prescription") }}</title>
    </head>
    <body>
        <style>
            body {
                font-family: 'Amiri', sans-serif !important;
            }

            .data-table {
                border-collapse: collapse;
                border: 1px solid #c7c7cc;
                min-width: 100%;
                max-width: 100%;
                width: 100%;
            }
        </style>
        <h1 style="font-weight: bold; text-align: center">
            {{ trans("site.center_name") }}
        </h1>
        <h2 style="font-weight: bold; text-align: center">
            {{ trans("site.prescription") }}
        </h2>
        <span>
            <span style="width: 50%; font-size: 18px">
                {{ trans("site.full_name") }} :
            </span>
            <span style="width: 50%; font-size: 18px; font-weight: bold">
                {{ $customer->user?->full_name }}
            </span>
        </span>

        <span>
            <span style="width: 50%; font-size: 18px">
                {{ trans("site.age") }} :
            </span>
            <span style="width: 50%; font-size: 18px; font-weight: bold">
                {{ round($customer->birth_date?->diffInYears(now())) }}
            </span>
        </span>

        <h2>{{ trans("site.prescription") }}</h2>
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
        @if (isset($prescription->other_data))
            <h2 style="text-align: start">
                {{ trans("validation.attributes.other_data") }}
            </h2>
            <table border="1" class="data-table">
                <tr style="background-color: #d9d9d9">
                    <th style="width: 35%"></th>
                    <th style="width: 65%"></th>
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
        @endif

        <h2 style="text-align: start">
            {{ trans("site.medicines") }}
        </h2>
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
    </body>
</html>
