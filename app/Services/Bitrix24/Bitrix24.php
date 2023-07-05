<?php

namespace App\Services\Bitrix24;

use App\Lead;
use App\LeadOrderAssignment;
use App\Leads\PoolAnswer;
use Illuminate\Support\Facades\Http;

class Bitrix24
{
    public const LEAD_UPDATE_HOOK = 'ONCRMLEADUPDATE';

    /**
     * @var string
     */
    protected $url;

    /**
     * Bitrix24 constructor.
     *
     * @param $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Dispatch lead to destination
     *
     * @param \App\Lead $lead
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return mixed|null
     */
    public function addLead(Lead $lead)
    {
        $response = Http::post(sprintf("%s/crm.lead.add.json", $this->url), $this->formatLead($lead))
            ->throw()
            ->json();

        return $response['result'] ?? null;
    }

    /**
     * Format lead payload
     *
     * @param \App\Lead $lead
     *
     * @return array
     */
    protected function formatLead(Lead $lead)
    {
        return [
            'fields' => [
                'TITLE'     => $lead->firstname . ' ' . $lead->lastname,
                'NAME'      => $lead->firstname,
                'LAST_NAME' => $lead->lastname,

                'STATUS_ID'            => 'NEW', // do not change
                'ADDRESS_CITY'         => optional($lead->ipAddress)->city, // ip user city
                'ADDRESS_COUNTRY'      => optional($lead->ipAddress)->country, // ip user country
                'OPENED'               => 'Y',
                'SOURCE_ID'            => '54', // do not change
                'UF_CRM_1590503243'    => $lead->domain, // Landing Name
                'UF_CRM_1668791858058' => 'subid', // subid
                'ASSIGNED_BY_ID'       => 1,

                'EMAIL' => [
                    [
                        "VALUE"      => $lead->email,
                        "VALUE_TYPE" => "WORK",
                    ]
                ],
                'PHONE' => [
                    [
                        "VALUE"      => $lead->formatted_phone,
                        "VALUE_TYPE" => "WORK",
                    ],
                ],
                'COMMENTS'      => $lead->hasPoll() ? $lead->pollResults()->map(fn (PoolAnswer $question) => $question->getQuestion().' -> '.$question->getAnswer())->implode(' | ') : '',
            ],
            'params' => ["REGISTER_SONET_EVENT" => "Y"],
        ];
    }

    /**
     * Get single lead from destination
     *
     * @param \App\LeadOrderAssignment $assignment
     *
     *@throws \Illuminate\Http\Client\RequestException
     * @throws \Throwable
     *
     * @return array|null
     *
     *
     */
    public function getLead(LeadOrderAssignment $assignment)
    {
        throw_if($assignment->external_id === null, \Exception::class, ['No external ID on lead(assignment)']);

        $response = Http::get(sprintf("%s/crm.lead.get.json", $this->url), [
            'id' => $assignment->external_id,
        ])
            ->throw()
            ->json();

        \Log::error(json_encode($response));

        return $response['result'] ?? null;
    }

    /**
     * Get leads from destination
     *
     * @throws \Illuminate\Http\Client\RequestException
     *
     * @return array
     */
    public function getLeads()
    {
        $response = Http::get(sprintf("%s/crm.lead.list.json", $this->url))
            ->throw()
            ->json();

        return $response ?? [];
    }

    /**
     * Get comments for the lead
     *
     * @param LeadOrderAssignment $assignment
     *
     *@throws \Illuminate\Http\Client\RequestException
     *
     * @return array|mixed
     *
     */
    public function getLeadComments(LeadOrderAssignment $assignment)
    {
        $response = Http::get(sprintf("%s/crm.timeline.comment.list.json", $this->url), [
            'filter' => [
                'ENTITY_ID'   => $assignment->external_id,
                'ENTITY_TYPE' => "lead",
            ],
        ])
            ->throw()
            ->json();

        return $response ?? [];
    }
}
