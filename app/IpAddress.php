<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * App\IpAddress
 *
 * @property int $id
 * @property string $ip
 * @property string|null $city
 * @property string|null $region
 * @property string|null $region_code
 * @property string|null $country
 * @property string|null $country_code
 * @property string|null $country_code_iso3
 * @property string|null $country_name
 * @property string|null $country_capital
 * @property string|null $country_tld
 * @property string|null $continent_code
 * @property bool $in_eu
 * @property string|null $postal
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $timezone
 * @property string|null $utc_offset
 * @property string|null $country_calling_code
 * @property string|null $country_area
 * @property string|null $country_population
 * @property string|null $currency
 * @property string|null $currency_name
 * @property string|null $languages
 * @property string|null $asn
 * @property string|null $org
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress query()
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereAsn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereContinentCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereCountryArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereCountryCallingCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereCountryCapital($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereCountryCodeIso3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereCountryName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereCountryPopulation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereCountryTld($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereCurrencyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereInEu($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereLanguages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereOrg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress wherePostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereRegionCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IpAddress whereUtcOffset($value)
 * @mixin \Eloquent
 */
class IpAddress extends Model
{
    protected $guarded = [];

    public function setIpAttribute($value)
    {
        $this->attributes['ip'] = Str::before($value, ',');
    }

    public static function store($attributes = null)
    {
        if ($attributes === [] || $attributes === null) {
            return null;
        }

        return static::updateOrCreate(
            ['ip' => $attributes['ip']],
            Arr::only(
                $attributes,
                [
                    'city','region','region_code','country','country_code','country_code_iso_3','country_name','country_capital','country_tld',
                    'continent_code','in_eu','postal','latitude','longitude','utc_offset','country_calling_code','country_area','country_population',
                    'currency','currency_name','languages','asn','org'
                ]
            )
        );
    }
}
