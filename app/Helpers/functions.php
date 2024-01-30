<?php

use App\Models\Date;
use App\Models\PatientVisit;
use App\Models\BillMaster;
use App\Models\RefundBillMaster;
use App\Models\PaymentMaster;
use App\Models\SequenceDefinition;
use App\Models\SubSequenceDetails;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Response;
use App\Models\Deposit;
use App\Models\Member;
use App\Services\CacheService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;


if (!function_exists('app_name')) {

    function app_name()
    {
        return config('app.name');
    }
}

if (!function_exists('convertDate')) {
    /**
     * Converts a date from one format to another.
     *
     * @param string $date The date to be converted.
     * @param string $dateType The type of date provided. Valid values are 'bs' and 'ad'.
     * @throws \Exception If the conversion fails.
     * @return string The converted date.
     */

    function convertDate($date, $dateType)
    {
        if (!empty($date) && !empty($dateType)) {
            $returnType = $dateType == 'bs' ? 'date_ad' : 'date_bs';
            $result = Date::where("date_{$dateType}", $date)->first()?->{$returnType};
            (new CacheService())
                ->setCacheExpireTime(60)
                ->setCacheKey('dates:' . $date . $dateType)
                ->remember($result);
            return $result;
        }

        return '';
    }
}

if (!function_exists('getEnum')) {
    /**
     * @param string $key = Name of the Key from Config/Enum file
     * @param bool $validation = optional if its is passed true it will return string for validation
     * @return bool|array|string
     */

    function getEnum(string $key = '', $validation = false): array | bool | string
    {
        if ($key == '') {
            return false;
        } else {
            if ($validation) {
                return config('enum.' . $key) == null ? false : implode(',', config('enum.' . $key));
            } else {
                return config('enum.' . $key) == null ? false : config('enum.' . $key);
            }
        }
    }
}

if (!function_exists('getUniqueBillNo')) {
    /**
     * @param string $date = current date NP
     * @return string unique billno
     */

    function getUniqueBillNo(string $date)
    {
        $year = substr($date, 2, 2);
        $month = substr($date, 6, 2);
        if ($month < 4) {
            $fiscalyear = ($year - 1) . '/' . $year;
        } else {
            $fiscalyear = $year . '/' . ($year + 1);
        }
        $prefix = $fiscalyear . '-';

        $maxInvoiceNo = BillMaster::where('bill_no_unique', 'like', "{$prefix}%")->max('bill_no_unique') ?? '0-0';
        $maxInvoiceNo = explode('-', $maxInvoiceNo);
        $maxInvoiceNo = $maxInvoiceNo[1] + 1;
        $newInvoiceNo = $prefix . str_pad($maxInvoiceNo, 6, '0', STR_PAD_LEFT);

        return $newInvoiceNo;
    }
}


if (!function_exists('getUniqueDepositNo')) {
    /**
     * @param string $date = current date NP
     * @return string unique deposit no
     */

    function getUniqueDepositNo(string $date)
    {
        $year = substr($date, 2, 2);
        $month = substr($date, 6, 2);
        if ($month < 4) {
            $fiscalyear = ($year - 1) . '/' . $year;
        } else {
            $fiscalyear = $year . '/' . ($year + 1);
        }
        $prefix = $fiscalyear . '-';

        $maxInvoiceNo = Deposit::where('bill_no_unique', 'like', "{$prefix}%")->max('bill_no_unique') ?? '0-0';
        $maxInvoiceNo = explode('-', $maxInvoiceNo);
        $maxInvoiceNo = $maxInvoiceNo[1] + 1;
        $newInvoiceNo = $prefix . str_pad($maxInvoiceNo, 6, '0', STR_PAD_LEFT);

        return $newInvoiceNo;
    }
}

if (!function_exists('getUniqueRecieptNo')) {
    /**
     * @param string $date = current date NP
     * @return string unique receipt no
     */

    function getUniqueRecieptNo(string $date, $type)
    {
        $year = substr($date, 2, 2);
        $month = substr($date, 6, 2);
        if ($month < 4) {
            $fiscalyear = ($year - 1) . '/' . $year;
        } else {
            $fiscalyear = $year . '/' . ($year + 1);
        }
        $prefix = $fiscalyear . '-';

        $maxReceiptNo = PaymentMaster::where('receipt_no', 'like', $type . $prefix . "%")->max('receipt_no') ?? '0-0';
        $maxReceiptNo = explode('-', $maxReceiptNo);
        $maxReceiptNo = $maxReceiptNo[1] + 1;
        $newReceiptNo = $type . $prefix . str_pad($maxReceiptNo, 6, '0', STR_PAD_LEFT);

        return $newReceiptNo;
    }
}

if (!function_exists('getUniqueRefundBillNo')) {
    /**
     * @param string $date = current date NP
     * @return string unique refund billno
     */

    function getUniqueRefundBillNo(string $date)
    {
        $year = substr($date, 2, 2);
        $month = substr($date, 6, 2);
        if ($month < 4) {
            $fiscalyear = ($year - 1) . '/' . $year;
        } else {
            $fiscalyear = $year . '/' . ($year + 1);
        }
        $prefix = $fiscalyear . '-';

        $maxInvoiceNo = RefundBillMaster::where('refund_no_unique', 'like', "{$prefix}%")->max('refund_no_unique') ?? '0-0';
        $maxInvoiceNo = explode('-', $maxInvoiceNo);
        $maxInvoiceNo = $maxInvoiceNo[1] + 1;
        $newInvoiceNo = $prefix . str_pad($maxInvoiceNo, 6, '0', STR_PAD_LEFT);

        return $newInvoiceNo;
    }
}

if (!function_exists('getToken')) {

    /**
     * Retrieves the access token for a given service.
     *
     * @param string $service The name of the service.
     * @throws Exception When an error occurs while retrieving the access token.
     * @return string The access token.
     */
    function getToken($service)
    {
        $serviceLower = strtolower($service);
        // Check if the access token is cached
        if (Redis::exists("{$serviceLower}_service_access_token")) {
            return Redis::get("{$serviceLower}_service_access_token");
        }

        try {
            // Make a POST request to the other Laravel application to obtain the access token
            $response = Http::post(env("{$service}_SERVICE_TOKEN_REQUEST_URL"), [
                'grant_type' => 'client_credentials',
                'client_id' => env("{$service}_SERVICE_ID"),
                'client_secret' => env("{$service}_SERVICE_SECRET"),
                'scope' => '',
            ]);

            // Extract the access token from the response JSON
            $accessTokenResponse = $response->json();
            $accessToken = $accessTokenResponse['access_token'];


            // Redis the access token for future use
            $expiresIn = $accessTokenResponse['expires_in'];
            Redis::set("{$serviceLower}_service_access_token", $accessToken, $expiresIn);

            return $accessToken;
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}


if (!function_exists('calculateDaysDifference')) {
    /**
     * @param string $startDateAd = start date AD
     * * @param string $endDateAd = End date AD
     * @return string|string days
     */
    function calculateDaysDifference($startDateAd, $endDateAd)
    {
        $startDate = new DateTime($startDateAd);
        $endDate = new DateTime($endDateAd);

        $interval = $startDate->diff($endDate);
        $daysDifference = $interval->days;

        return $daysDifference;
    }
}

if (!function_exists('getNextSequence')) {
    function getNextSequence($depId= false, $testId=false,$name,$date)
    {
        try {            
            $sequenceDefinition = SequenceDefinition::where('name', $name)
                ->where('active',true)
                ->when($depId, function ($query) use ($depId) {
                    $query->where('department_id', $depId);
                })
                ->when($testId, function ($query) use ($testId) {
                    $query->where('test_id', $testId);
                })->first();
            if (empty($sequenceDefinition)) {
                throw new \Exception('Invalid Request', 400);
            }

            $now = Carbon::now();
            $dateAD = $now->format('Y-m-d');
            $dateBS = convertDate($dateAD, 'ad');
            $nowBS = Carbon::parse($dateBS); 

             // COnvert date to BS if isNepDateSys is true
            if ($sequenceDefinition->isNepDateSys) {
                $twoDigitYear = $nowBS->format('y');
                $fourDigitYear = $nowBS->format('Y');
                $month = $nowBS->format('m');
                $day = $nowBS->format('d');
                
                if ($nowBS->gte($fourDigitYear.'/04/01')){
                    $fiscalYear='0'.$twoDigitYear.'/'.($twoDigitYear + 1);
                    $fy='0'.$twoDigitYear.'/'.($twoDigitYear + 1).'-';                    
                }else if ($nowBS->lt($fourDigitYear.'/04/01')){
                    $fiscalYear='0'.($twoDigitYear - 1).'/'.$twoDigitYear;
                    $fy='0'.($twoDigitYear - 1).'/'.$twoDigitYear.'-';
                }
            }else{
                $twoDigitYear = $now->format('y');
                $fourDigitYear = $now->format('Y');
                $month = $now->format('m');
                $day = $now->format('d');
                $fiscalYear='';
                $fy='';
            }

            $nextValueFromDB = $sequenceDefinition->next_value;
            $paddedNextValue = str_pad($nextValueFromDB, $sequenceDefinition->length, '0', STR_PAD_LEFT);
            $generatedNumber = str_replace(['%FY%','%fy%','%YYYY%', '%YY%', '%MM%', '%DD%'], [$fiscalYear,$fy,$fourDigitYear, $twoDigitYear, $month, $day], $sequenceDefinition->prefix);
            $generatedNumber .= $paddedNextValue;
            $generatedNumber .= str_replace(['%FY%','%fy%','%YYYY%', '%YY%', '%MM%', '%DD%'], [$fiscalYear,$fy,$fourDigitYear, $twoDigitYear, $month, $day], $sequenceDefinition->suffix);

            $sequenceDefinition->next_value = $nextValueFromDB + $sequenceDefinition->step;
            $sequenceDefinition->save();
            return $generatedNumber;
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Sequence definition not found'], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()],Response::HTTP_BAD_REQUEST);
        }
    }

    if (!function_exists('getNextLabNo')) {
        /**
         * @param string $date = current date NP
         * @return string unique refund billno
         */

        function getNextLabNo()
        {
            // $prefix = 'LAB';
            // $maxLabNo = BillMaster::where('lab_id', 'like', "{$prefix}%")->max('lab_id') ?? 'LAB0';
            // $maxLabNo = explode($prefix, $maxLabNo);
            // $maxLabNo = $maxLabNo[1] + 1;
            // $maxLabNo = $prefix . str_pad($maxLabNo, 7, '0', STR_PAD_LEFT);
            // return $maxLabNo;
            $maxLabNo = BillMaster::selectRaw('COALESCE(max(CAST(lab_id  AS INTEGER)),0) as lab_no')
                ->first();
            $maxLabNo = $maxLabNo->lab_no + 1;
            return $maxLabNo;
        }
    }

    if (!function_exists('getQueueNo')) {
        function getQueueNo($queueType, $queueTypeId)
        {
            if (empty($queueType)) {
                $queueType = 'DEP';
            }
            $todaysDateAd = date('Y-m-d');
            $todaysDateVs = convertDate($todaysDateAd, 'ad');

            if ($queueType == 'DOC') {
                $queueNo = PatientVisit::where('visit_date_vs', $todaysDateVs)
                    ->where('queue_type', 'DOC')
                    ->where('employee_id', $queueTypeId)
                    ->max('queue_no') + 1;
            } else {
                $queueNo = PatientVisit::where('visit_date_vs', $todaysDateVs)
                    ->where('queue_type', 'DEP')
                    ->where('department_id', $queueTypeId)
                    ->max('queue_no') + 1;
            }
            return $queueNo;
        }
    }
}


if (!function_exists('forgetCache')) {
    /**
     * Clear cache for specified keys or flush all cache.
     *
     * @param array $keys       The keys to clear cache for.
     * @param string $connection The cache connection to use ('redis' or 'default').
     *
     * @return void
     */
    function forgetCache(array $keys = [], $connection = 'redis')
    {
        try {
            if (count($keys)) {
                $cache = ($connection === 'redis') ? Redis::del($keys) : Cache::forget($keys);
            } else {
                $cache = ($connection === 'redis') ? Redis::flushall() : Cache::flush();
            }
            return $cache;
        } catch (\Exception $e) {
            info($e->getMessage());
            return false;
        }
    }
}

if (!function_exists('currentMessageBroker')) {
    function currentMessageBroker() : ?string
    {
        return config('message-broker.CURRENT_MESSAGE_BROKER');
    }
}

if (!function_exists('formatDataForConsumer')) {
    function formatDataForConsumer($data)
    {
        return json_decode($data, true);
    }
}