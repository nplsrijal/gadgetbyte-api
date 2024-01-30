<html>

<head>
    <style>
        @page {
            size: A8 landscape;
            margin: 5px;
            /* Change page size and orientation */
        }

        /* Set margins (top, right, bottom, left) */

        body {
            font-family: sans-serif;
            font-size: 10pt;
        }

        p {
            margin: 0pt;
        }

        table.items {
            border: 0.1mm solid #e7e7e7;
        }

        td {
            vertical-align: top;
        }

        .items td {
            border-left: 0.1mm solid #e7e7e7;
            border-right: 0.1mm solid #e7e7e7;
        }

        table thead td {
            text-align: center;
            border: 0.1mm solid #e7e7e7;
        }

        .items td.blanktotal {
            background-color: #EEEEEE;
            border: 0.1mm solid #e7e7e7;
            background-color: #FFFFFF;
            border: 0mm none #e7e7e7;
            border-top: 0.1mm solid #e7e7e7;
            border-right: 0.1mm solid #e7e7e7;
        }

        .items td.totals {
            text-align: right;
            border: 0.1mm solid #e7e7e7;
        }

        .items td.cost {
            text-align: "."center;
        }

        @media print {
            flex {
                display: flex;
            }
        }

    </style>


</head>

<body>

    <!--     
    <table width="100%" style="font-family: sans-serif;" cellpadding="10">
        <tr>
            <td width="49%" style="border: 0.1mm solid #eee;">{{$patientInfo->depname}}<br>Hospital No. : {{$patientInfo->hospital_no}}<br>Name : {{$patientInfo->name}}<br>Age : {{$patientInfo->age ." ". strtoupper(substr($patientInfo->agetype, 0, 1)) . " / " . strtoupper(substr($patientInfo->gender, 0, 1))}}
                <br>Address : {{$patientInfo->address}} <br>Contact No : {{$patientInfo->mobileno}}</td>
            <td width="2%">&nbsp;</td>         
                           
        </tr>
    </table> -->
    <table width="100%" style="font-family: sans-serif;" cellpadding="10">
        <!-- <tr class="flex"> -->
        <tr tr class="flex">
            <td style="padding: 0; font-size: 16px; font-weight: bold;">{{ $patientInfo->depname }}</td>
            <td style="padding:0">{{$patientInfo->visittype}}</td>
        </tr>
        <tr>
            <td style="padding:0; width:70%" text-align: right;> Hospital No. : {{$patientInfo->hospital_no}} </td>
            @if ($patientInfo->charge > 0){
            <td style="padding:0; display:flex"> <span> Charge :</span> <span>{{$patientInfo->charge}}</span></td>
            }
            @endif
        </tr>
        <tr>
            <td style="padding:0"> Name : {{$patientInfo->name}}</td>
            {{-- <td style="padding:0;"> {{$patientInfo->charge}} </td> --}}
        </tr>
        <tr>
            <td style="padding:0">Age : {{$patientInfo->age ." ". strtoupper(substr($patientInfo->agetype, 0, 1)) . " / " . strtoupper(substr($patientInfo->gender, 0, 1))}}</td>
        </tr>
        <tr>
            <td style="padding:0"> Address : {{$patientInfo->address}} </td>
        </tr>
        <tr>
            <td style="padding:0"> Contact No : {{$patientInfo->mobileno}} </td>
        </tr>
        <tr>
            <td style="padding:0"> Visit Date : {{$patientInfo->visitdate}} </td>
        </tr>
        <tr>
        <td style="padding:0">
        {!! $barcode !!}
        </td>

        </tr>
    </table>
</body>

</html>
