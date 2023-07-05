<?php

namespace App\Http\Controllers;

use App\Http\Requests\Leads\ChangeLeftoversOffer;
use App\Imports\Leads\ChangeOffer;
use App\Lead;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class LeadsLeftoversChangeOfferController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param ChangeLeftoversOffer $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(ChangeLeftoversOffer $request)
    {
        Log::debug("Init change offer", ['change-offer-leftovers']);
        if ($request->hasFile('leads_file')) {
            Log::debug("File case", ['change-offer-leftovers']);
            $filename = time() . '_' . Str::random(40) . '.' . $request->file('leads_file')->getClientOriginalExtension();
            Log::debug("Filename generated", ['change-offer-leftovers']);
            Storage::disk('s3')->createDir('leads-change-offer');
            Log::debug("Folder created", ['change-offer-leftovers']);
            Storage::disk('s3')->putFileAs('leads-change-offer', $request->file('leads_file'), $filename);
            Log::debug("File put", ['change-offer-leftovers']);
            Excel::import(new ChangeOffer(), sprintf("leads-change-offer/%s", basename($filename)), 's3');
            Log::debug("Import finished", ['change-offer-leftovers']);
        } else {
            Lead::visible()
                ->leftovers([$request->input('since'), $request->input('until')], $request->input('offer_from'))
                ->get()->each->update([
                    'offer_id' => $request->input(['offer_to'])
                ]);
        }

        return response()->noContent();
    }
}
