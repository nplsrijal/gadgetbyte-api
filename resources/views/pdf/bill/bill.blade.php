<html>
<head>
    <style>
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

    </style>
</head>

<body>
    <table width="100%" style="font-family: sans-serif; font-size: 14px;">
        <br>
        <tr>
            <td>

                <table width="100%" align="left" style="font-family: sans-serif; text-align: center;">
                    <tr>
                        <td style="padding: 0px; line-height: 28px; font-size: 28px">
                            <strong>Hospital Name</strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0px; line-height: 19px; font-size: 18px">
                            Hospital Address
                            <br>
                            Phone No.: +9800000000
                            <br>
                            Email: midastechnologies.com.np
                        </td>
                </table>

            </td>
        </tr>
        <br>
    </table>

    <table width="100%" style="font-family: sans-serif;" cellpadding="10">

    <tr>
            <td width="100%" style="text-align: center; font-size: 20px; font-weight: bold; padding: 0px;">
                <span style="position:absolute; right:0">{!! $barcode !!}</span>
                <span >INVOICE</span>
            </td>
        </tr>
        
        
        @if (substr($billNo, 0, 2) === 'RF') {
        <tr>
            <td width="100%" style="text-align: center; font-size: 15px; font-weight: bold; padding: 0px;">
                (REFUND)
            </td>
        </tr>
        }
        @endif
        <tr>
            <td height="10" style="font-size: 0px; line-height: 10px; height: 10px; padding: 0px;">&nbsp;</td>
        </tr>
    </table>
    <table width="100%" style="font-family: sans-serif;" cellpadding="10">
        <tr>
            <td width="49%" style="border: 0.1mm solid #eee;">Hospital No. : {{ $patientInfo->hospital_no}}<br>Name : {{strtoupper($patientInfo->name)}}<br>Age : {{$patientInfo->age ." ". strtoupper(substr($patientInfo->agetype, 0, 1)) . " / " . strtoupper(substr($patientInfo->gender, 0, 1))}}
                <br>Address : {{strtoupper($patientInfo->address)}} <br>Contact No : {{$patientInfo->mobileno}}</td>
            <td width="2%">&nbsp;</td>
            <td width="49%" style="border: 0.1mm solid #eee; text-align: left;"><strong>Invoice No : {{$billNo}}</strong><br>Invoice Date : {{$billDetails[0]->date_vs ." ". $billDetails[0]->time }}</td>

        </tr>
    </table>

    <br>
    <table class="items" width="100%" style="font-size: 14px; border-collapse: collapse;" cellpadding="8">
        <thead>
            <tr>
                <td width="15%" style="text-align: left;"><strong>SN</strong></td>
                <td width="45%" style="text-align: left;"><strong>Particulars</strong></td>
                <td width="20%" style="text-align: left; text-align: right;"><strong>Rate</strong></td>
                <td width="20%" style="text-align: left; text-align: right;"><strong>Qty</strong></td>
                <td width="20%" style="text-align: left; text-align: right;"><strong>Amount </strong></td>
            </tr>
        </thead>
        <tbody>
            <!-- ITEMS HERE -->

            @php
            $grossAmount = 0;
            $discount = 0;
            $refundAmount = 0;
            $totalAmount = 0;
            @endphp

            @foreach ($billDetails as $key => $billDetail)
            <tr>
                <td style="padding: 0px 7px; line-height: 20px; font-size: 12px;">{{ $key + 1 }}</td>
                <td style="padding: 0px 7px; line-height: 20px; font-size: 12px;">{{ $billDetail->service}}</td>
                <td style="padding: 0px 7px; line-height: 20px; font-size: 12px; text-align: right;">{{ $billDetail->amount }}</td>
                <td style="padding: 0px 7px; line-height: 20px; font-size: 12px; text-align: right;">{{ $billDetail->qty }}</td>
                <td style="padding: 0px 7px; line-height: 20px; font-size: 12px; text-align: right;">{{ $billDetail->amount * $billDetail->qty }}</td>
                <td></td>
            </tr>

            @php
            $grossAmount += $billDetail->amount * $billDetail->qty;
            $discount += $billDetail->amount * $billDetail->qty * $billDetail->disper / 100;
            if ($billDetail->refundamount)
            $refundAmount += $billDetail->refundamount;
            @endphp

            @endforeach

        </tbody>
    </table>
    <br>
    <table width="100%" style="font-family: sans-serif; font-size: 14px;">
        <tr>
            <td>
                <table width="60%" align="left" style="font-family: sans-serif; font-size: 14px;">
                    <tr>
                        <td style="padding: 0px; line-height: 20px;">&nbsp;</td>
                    </tr>
                </table>
                <table width="40%" align="right" style="font-family: sans-serif; font-size: 14px;">
                    <tr>
                        <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;"><strong>Amount</strong></td>
                        <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;" align="right"><strong>{{$grossAmount}}</strong></td>
                    </tr>
                    <tr>
                        <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;"><strong>Discount</strong></td>
                        <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;" align="right"><strong>{{$discount}}</strong></td>
                    </tr>

                    @if ($refundAmount>0) {
                    <tr>
                        <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;"><strong>Refund Amount</strong></td>
                        <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;" align="right"><strong>{{$refundAmount}}</strong></td>
                    </tr>
                    }
                    @endif

                    <tr>
                        <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;"><strong>Total Amount</strong></td>
                        <td style="border: 1px #eee solid; padding: 0px 8px; line-height: 20px;" align="right"><strong>{{$grossAmount - $discount - $refundAmount}}</strong></td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
    <br>

</body>
</html>
