<?php

namespace App\Http\Controllers;

use App\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ZvonobotReceiveIncomingLead extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $phone      = digits($request->input('call.phone'));
        $ivrAnswers = $request->input('call.ivr_answers');

        if (!empty($phone) && preg_match('~\|1(\|.+)?~', $ivrAnswers, $answers)) {
//            $name  = !empty($answers[1]) ? json_decode('"' . trim($answers[1], '|') . '"') : '';
//            $clean = app('dadata')->clean($name);
//            if (empty($name) || empty($clean[0]) || empty($clean[0]['name']) || empty($clean[0]['surname'])) {
//                return response()->noContent();
//            }

            $lead = Lead::wherePhone($phone)->first();

            Lead::firstOrCreate(
                [
                    'phone'    => $phone,
                    'offer_id' => 2217,
                ],
                [
                    //                    'firstname'   => $clean[0]['name'],
                    'firstname'   => $lead->firstname,
                    //                    'lastname'    => $clean[0]['surname'],
                    'lastname'    => $lead->lastname ?? 'Unknown',
                    //                    'middlename'  => $clean[0]['patronymic'],
                    'email'       => optional($lead)->email,
                    'ip'          => optional($lead)->ip,
                    'valid'       => optional($lead)->valid ?? true,
                    'phone_valid' => optional($lead)->phone_valid,
                    'gender_id'   => optional($lead)->gender_id,
                    'uuid'        => Str::uuid()->toString(),
                    'requestData' => $request->all(),
                    'user_id'     => 325,
                ]
            );
        }

        return response()->noContent();
    }
}
