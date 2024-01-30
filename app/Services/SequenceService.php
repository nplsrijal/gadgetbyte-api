<?php

namespace App\Services;

use App\Models\SequenceDefinition;
use App\Models\SubSequenceDetails;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class SequenceService
{
    public function getSequence($depType, $depId, $testId, $name)
    {
        try {
            $sequenceDefinition = SequenceDefinition::where('name', $name)
                ->when($depId, function ($query) use ($depId) {
                    $query->where('department_id', $depId);
                })
                ->when($depType, function ($query) use ($depType) {
                    $query->where('dep_type', $depType);
                })
                ->when($testId, function ($query) use ($testId) {
                    $query->where('test_id', $testId);
                })->first();
            if (empty($sequenceDefinition)) {
                throw new \Exception('Invalid Request', 400);
            }
            $now = Carbon::now();
            $twoDigitYear = $now->format('y');
            $fourDigitYear = $now->format('Y');
            $month = $now->format('m');
            $day = $now->format('d');

            if (!$sequenceDefinition->active) {
                throw new \Exception('Sequence is not active', 400);
            }

            if ($sequenceDefinition->sub_sequence) {
                $validSubSequence = null;
                $subsequenceDetails = SubSequenceDetails::where('sequence_definition_id', $sequenceDefinition->id)->get();

                if ($subsequenceDetails) {
                    foreach ($subsequenceDetails as $subsequenceDetail) {
                        if ($subsequenceDetail->valid_from && $now->lt($subsequenceDetail->valid_from)) {
                            continue;
                        }
                        if ($subsequenceDetail->valid_to && $now->gt($subsequenceDetail->valid_to)) {
                            continue;
                        }
                        $validSubSequence = $subsequenceDetail;
                        break;
                    }
                }

                if ($validSubSequence) {
                    $sequence_name = $sequenceDefinition->sequence_name;
                    $nextValueFromSequence = DB::select("SELECT nextval('$sequence_name')")[0]->nextval;
                    $nextValueFromDB = $sequenceDefinition->next_value;

                    if ($nextValueFromSequence !== $nextValueFromDB) {
                        return response()->json(['message' => 'Invalid sequence generated.'], Response::HTTP_BAD_REQUEST);
                    }

                    $paddedNextValue = str_pad($nextValueFromSequence, $sequenceDefinition->length, '0', STR_PAD_LEFT);

                    if ($validSubSequence->sub_sequence_details) {
                        $generatedNumber = str_replace(['%YYYY%', '%YY%', '%MM%', '%DD%'], [$fourDigitYear, $twoDigitYear, $month, $day], $validSubSequence->sub_sequence_prefix);
                        $generatedNumber .= $paddedNextValue;
                        $generatedNumber .= str_replace(['%YYYY%', '%YY%', '%MM%', '%DD%'], [$fourDigitYear, $twoDigitYear, $month, $day], $validSubSequence->sub_sequence_suffix);
                    } else {
                        $generatedNumber = str_replace(['%YYYY%', '%YY%', '%MM%', '%DD%'], [$fourDigitYear, $twoDigitYear, $month, $day], $sequenceDefinition->prefix);
                        $generatedNumber .= $paddedNextValue;
                        $generatedNumber .= str_replace(['%YYYY%', '%YY%', '%MM%', '%DD%'], [$fourDigitYear, $twoDigitYear, $month, $day], $sequenceDefinition->suffix);
                    }
                } else {
                    throw new \Exception('No valid sub-sequence details found for the current date.', 400);
                }
            } else {
                $sequence_name = $sequenceDefinition->sequence_name;
                $nextValueFromSequence = DB::select("SELECT nextval('$sequence_name')")[0]->nextval;
                $paddedNextValue = str_pad($nextValueFromSequence, $sequenceDefinition->length, '0', STR_PAD_LEFT);
                $generatedNumber = str_replace(['%YYYY%', '%YY%', '%MM%', '%DD%'], [$fourDigitYear, $twoDigitYear, $month, $day], $sequenceDefinition->prefix);
                $generatedNumber .= $paddedNextValue;
                $generatedNumber .= str_replace(['%YYYY%', '%YY%', '%MM%', '%DD%'], [$fourDigitYear, $twoDigitYear, $month, $day], $sequenceDefinition->suffix);
            }

            $sequenceDefinition->next_value = $nextValueFromSequence + $sequenceDefinition->step;
            $sequenceDefinition->save();

            return response()->json(['new_sequence' => $generatedNumber], Response::HTTP_OK);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Sequence definition not found'], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
