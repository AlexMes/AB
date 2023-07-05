<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

/**
 * App\LeadDestinationDriver
 *
 * @property string|null $id
 * @property string|null $name
 * @property string|null $implementation
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LeadDestinationDriver newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadDestinationDriver newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadDestinationDriver query()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadDestinationDriver whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadDestinationDriver whereImplementation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadDestinationDriver whereName($value)
 * @mixin \Eloquent
 */
class LeadDestinationDriver extends Model
{
    use Sushi;

    public const DEFAULT        = self::CRM;
    public const CRM            = 'crm';
    public const B24            = 'b24';
    public const FXG24          = 'fxg24';
    public const GSHEETS        = 'gsheets';
    public const OLYMPUS        = 'olympus';
    public const GLOBALWISE     = 'globalwise';
    public const AFFFBOAT       = 'affboat';
    public const UNITEDMARKETS  = 'united-markets';
    public const STARLAB        = 'starlab';
    public const HUGEOFFERS     = 'hugeoffers';
    public const EUROHOT        = 'eurohot';
    public const ZURICH         = 'zurich';
    public const CLICKMATE      = 'clickmate';
    public const GOLDVER        = 'goldver';
    public const HOTLEADS       = 'hotleads';
    public const TRAFFICON      = 'trafficon';
    public const SUPERMEDIA     = 'supermedia';
    public const TOPCONVERT     = 'topconvert';
    public const GLOBALALLIANCE = 'globalalliance';
    public const FINANTICK      = 'finantick';
    public const TCRM           = 'tcrm';
    public const ECLIPSE        = 'eclipse';
    public const SAFETRADE      = 'safetrade';
    public const BINARYX        = 'binaryx';
    public const CMAFFS         = 'cmaffs';
    public const BNB            = 'bnb';
    public const AFFCLUB        = 'affclub';
    public const CMAFFS_ALT     = 'cmaffs_alt';
    public const NEOGARA        = 'neogara';
    public const AIVIX          = 'aivix';
    public const ODIXEN         = 'odixen';
    public const AFFILIATES360  = 'affiliates-360';
    public const BALOJAH        = 'balojah';
    public const TRACKBOX       = 'trackbox';
    public const EDEAL          = 'edeal';
    public const INVESTEX       = 'investex';
    public const FOREXCATS      = 'forexcats';
    public const LOSER          = 'loser';
    public const TRAFFICDANDY   = 'trafficdandy';
    public const CONVERTINGTEAM = 'convertingteam';
    public const ROYALCRM       = 'royalcrm';
    public const AFFCOUNTRY     = 'affcountry';
    public const LPCRM          = 'lpcrm';
    public const MAXIPARTNERS   = 'maxipartners';
    public const JUNODIGITAL    = 'juno-digital';
    public const AXELA          = 'axela';
    public const MAESTRO        = 'maestro';
    public const MOEKS          = 'moeks';
    public const PAYMENTS       = 'payments';
    public const YOOSE          = 'yoose';
    public const PLATFORM500    = 'platform500';
    public const MEDIAPRO       = 'mediapro';
    public const LEGACYFX       = 'legacyfx';
    public const AFFLION        = 'afflion';
    public const VOIPPLUS       = 'voipplus';
    public const AMOCRM         = 'amocrm';
    public const UBGROUP        = 'ubgroup';
    public const AWFULCRM       = 'awful';
    public const SOMEONES       = 'someones';
    public const PLANETINV      = 'planetinv';
    public const HYPER          = 'hyper';
    public const TRUMP          = 'trump';
    public const CLICKSAFF      = 'clicksaff';
    public const PROFITC        = 'profitc';
    public const CRYPTOCENTER   = 'cryptocenter';
    public const EXBINARY       = 'exbinary';
    public const EPC            = 'epc';
    public const CRYPIM         = 'crypim';
    public const SCAMCOIN       = 'scamcoin';
    public const SHEIKS         = 'sheiks';
    public const MONIACC        = 'moniacc';
    public const VANI           = 'vani';
    public const JAGCRM         = 'jagcrm';
    public const TGI            = 'tgi';
    public const HISTMO         = 'histmo';
    public const BILLTYPE       = 'billtype';
    public const THRBUYERS      = 'thrbuyers';
    public const AFFTEK         = 'afftek';
    public const CARECORP       = 'carecorp';
    public const BOARD          = 'board';
    public const A2A            = 'a2a';
    public const DIGIT          = 'digit';
    public const AFFMIDAS       = 'affmidas';
    public const PUNKS          = 'punks';
    public const REVOLUT        = 'revolut';
    public const FINUTRADE      = 'finutrade';
    public const SCALEO         = 'scaleo';
    public const HULU           = 'hulu';
    public const SKYNET         = 'skynet';
    public const ARION          = 'arion';
    public const FRANKLIN       = 'franklin';
    public const BEATSCLICK     = 'beatsclick';
    public const REBEL          = 'rebel';
    public const UKRTRAFF       = 'ukrtraff';
    public const THM            = 'thm';
    public const DOZATEAM       = 'dozateam';
    public const TIMGROUP       = 'timgroup';
    public const ONEXCPA        = 'onexcpa';
    public const NORMAL         = 'normal';
    public const PLEXUS         = 'plexus';
    public const OCEAN          = 'ocean';
    public const OVO            = 'ovo';
    public const SIXTNINE       = 'sixtnine';
    public const GLOBALFINANCE  = 'globalfinance';
    public const HUMMUS         = 'hummus';
    public const CLICKOUT       = 'clickout';
    public const LGCINVESTMENTS = 'lgcinvestments';
    public const TRADESKILLS    = 'tradeskills';
    public const REDCRM         = 'redcrm';
    public const GASTRADE       = 'gastrade';
    public const LUNOCRYPT      = 'lunocrypt';
    public const NORMALNEW      = 'nornew';
    public const MOONSTAR       = 'moonstar';
    public const SWISSROI       = 'swissroi';
    public const HEADSINVEST    = 'headsinvest';
    public const WOODBROOK      = 'woodbrook';
    public const HYPERNET       = 'hypernet';
    public const PREMIUMFINSOL  = 'premiumfinsol';
    public const SCHRM          = 'schrm';
    public const VALLETTATRADE  = 'vallettatrade';
    public const GOLDCRM        = 'goldcrm';
    public const GOLDENBIT      = 'goldenbit';
    public const CRYPTOTRAFFIC  = 'cryptotraffic';
    public const AFFTENOR       = 'afftenor';
    public const SAFECAPINVEST  = 'safecapinvest';
    public const SINERGIA       = 'sinergia';
    public const MEDIFIN        = 'medifin';
    public const ATSINVEST      = 'atsinvest';
    public const KINGSOFTRAFFIC = 'kingsoftraffic';
    public const VEXXSEL        = 'vexxsel';
    public const TRUSTLEADS     = 'trustleads';
    public const ALPHAINVEST    = 'alphainvest';
    public const PAFNET         = 'pafnet';
    public const ADSBOARDAFF    = 'adsboardaff';
    public const HELLLEADS      = 'hellleads';
    public const PARTNERSTCRM   = 'partnerstcrm';
    public const MFURY          = 'mfury';
    public const SOULCAPITAL    = 'soulcapital';
    public const BELMAR         = 'belmar';
    public const LXCRM          = 'lxcrm';
    public const SENSUMGLOBAL   = 'sensumglobal';
    public const GLOBINC        = 'globink';
    public const VIRGINGROUP    = 'virgingroup';
    public const LEADPANDA      = 'leadpanda';
    public const OPTIMUS        = 'optimus';
    public const METROPOLITAN   = 'metropolitan';

    /**
     * Hide attributes from JSON
     *
     * @var array
     */
    protected $hidden = ['implementation'];

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @var string[]
     */
    protected $rows = [
        [
            'id'             => self::DEFAULT,
            'name'           => 'Default',
            'implementation' => DestinationDrivers\Crm::class,
        ],
        [
            'id'             => self::CRM,
            'name'           => 'CRM',
            'implementation' => DestinationDrivers\Crm::class,
        ],
        [
            'id'             => self::GSHEETS,
            'name'           => 'Google Spreadsheets',
            'implementation' => DestinationDrivers\GoogleSpreadsheets::class,
        ],
        [
            'id'             => self::B24,
            'name'           => 'Bitrix24',
            'implementation' => DestinationDrivers\Bitrix24::class,
        ],
        [
            'id'             => self::FXG24,
            'name'           => 'FXG-24',
            'implementation' => DestinationDrivers\Fxg24::class,
        ],
        [
            'id'             => self::OLYMPUS,
            'name'           => 'Olympics Holding',
            'implementation' => DestinationDrivers\Olympus::class,
        ],
        [
            'id'             => self::GLOBALWISE,
            'name'           => 'Global Wise',
            'implementation' => DestinationDrivers\GlobalWise::class,
        ],
        [
            'id'             => self::AFFFBOAT,
            'name'           => 'AffBoat',
            'implementation' => DestinationDrivers\AffBoat::class,
        ],
        [
            'id'             => self::UNITEDMARKETS,
            'name'           => 'United Markets',
            'implementation' => DestinationDrivers\UnitedMarkets::class,
        ],
        [
            'id'             => self::STARLAB,
            'name'           => 'StarLab',
            'implementation' => DestinationDrivers\StarLab::class,
        ],
        [
            'id'             => self::HUGEOFFERS,
            'name'           => 'StarLab',
            'implementation' => DestinationDrivers\HugeOffers::class,
        ],
        [
            'id'             => self::EUROHOT,
            'name'           => 'EuroHot',
            'implementation' => DestinationDrivers\EuroHot::class,
        ],
        [
            'id'             => self::ZURICH,
            'name'           => 'Zurich',
            'implementation' => DestinationDrivers\Zurich::class,
        ],
        [
            'id'             => self::CLICKMATE,
            'name'           => 'ClickMate',
            'implementation' => DestinationDrivers\ClickMate::class,
        ],
        [
            'id'             => self::GOLDVER,
            'name'           => 'GoldVer',
            'implementation' => DestinationDrivers\Goldver::class,
        ],
        [
            'id'             => self::HOTLEADS,
            'name'           => 'HotLeads',
            'implementation' => DestinationDrivers\HotLeads::class,
        ],
        [
            'id'             => self::TRAFFICON,
            'name'           => 'Trafficon',
            'implementation' => DestinationDrivers\Trafficon::class,
        ],
        [
            'id'             => self::SUPERMEDIA,
            'name'           => 'Supermedia',
            'implementation' => DestinationDrivers\Supermedia::class,
        ],
        [
            'id'             => self::TOPCONVERT,
            'name'           => 'TopConvert',
            'implementation' => DestinationDrivers\TopConvert::class,
        ],
        [
            'id'             => self::GLOBALALLIANCE,
            'name'           => 'Global Alliance',
            'implementation' => DestinationDrivers\GlobalAlliance::class,
        ],
        [
            'id'             => self::FINANTICK,
            'name'           => 'Finantick',
            'implementation' => DestinationDrivers\Finantick::class,
        ],
        [
            'id'             => self::TCRM,
            'name'           => 'TCRM',
            'implementation' => DestinationDrivers\TCRM::class,
        ],
        [
            'id'             => self::ECLIPSE,
            'name'           => 'Eclipse',
            'implementation' => DestinationDrivers\Eclipse::class,
        ],
        [
            'id'             => self::SAFETRADE,
            'name'           => 'SafeTrade',
            'implementation' => DestinationDrivers\SafeTrade::class,
        ],
        [
            'id'             => self::BINARYX,
            'name'           => 'Binaryx',
            'implementation' => DestinationDrivers\Binaryx::class,
        ],
        [
            'id'             => self::CMAFFS,
            'name'           => 'Cmaffs',
            'implementation' => DestinationDrivers\Cmaffs::class,
        ],
        [
            'id'             => self::BNB,
            'name'           => 'BullsNBears',
            'implementation' => DestinationDrivers\BullsAndBears::class,
        ],
        [
            'id'             => self::AFFCLUB,
            'name'           => 'Affclub',
            'implementation' => DestinationDrivers\Affclub::class,
        ],
        [
            'id'             => self::CMAFFS_ALT,
            'name'           => 'Cmaffs Alternative',
            'implementation' => DestinationDrivers\CmaffsAlternative::class,
        ],
        [
            'id'             => self::NEOGARA,
            'name'           => 'Neogara',
            'implementation' => DestinationDrivers\Neogara::class,
        ],
        [
            'id'             => self::AIVIX,
            'name'           => 'Aivix',
            'implementation' => DestinationDrivers\Aivix::class,
        ],
        [
            'id'             => self::ODIXEN,
            'name'           => 'Odixen',
            'implementation' => DestinationDrivers\Odixen::class,
        ],
        [
            'id'             => self::AFFILIATES360,
            'name'           => '360 Affiliates',
            'implementation' => DestinationDrivers\Affiliates360::class,
        ],
        [
            'id'             => self::BALOJAH,
            'name'           => 'Balojah',
            'implementation' => DestinationDrivers\Balojah::class,
        ],
        [
            'id'             => self::TRACKBOX,
            'name'           => 'TrackBox',
            'implementation' => DestinationDrivers\TrackBox::class,
        ],
        [
            'id'             => self::EDEAL,
            'name'           => 'Edeal',
            'implementation' => DestinationDrivers\Edeal::class,
        ],
        [
            'id'             => self::INVESTEX,
            'name'           => 'Investex',
            'implementation' => DestinationDrivers\Investex::class,
        ],
        [
            'id'             => self::FOREXCATS,
            'name'           => 'ForexCats',
            'implementation' => DestinationDrivers\ForexCats::class,
        ],
        [
            'id'             => self::LOSER,
            'name'           => 'Loser',
            'implementation' => DestinationDrivers\Loser::class,
        ],
        [
            'id'             => self::TRAFFICDANDY,
            'name'           => 'TrafficDandy',
            'implementation' => DestinationDrivers\TrafficDandy::class,
        ],
        [
            'id'             => self::CONVERTINGTEAM,
            'name'           => 'Converting Team',
            'implementation' => DestinationDrivers\ConvertingTeam::class,
        ],
        [
            'id'             => self::ROYALCRM,
            'name'           => 'RoyalCRM',
            'implementation' => DestinationDrivers\RoyalCrm::class,
        ],
        [
            'id'             => self::AFFCOUNTRY,
            'name'           => 'AffCountry',
            'implementation' => DestinationDrivers\Affcountry::class,
        ],
        [
            'id'             => self::LPCRM,
            'name'           => 'LP CRM',
            'implementation' => DestinationDrivers\LpCrm::class,
        ],
        [
            'id'             => self::MAXIPARTNERS,
            'name'           => 'Maxipartners',
            'implementation' => DestinationDrivers\Maxipartners::class,
        ],
        [
            'id'             => self::JUNODIGITAL,
            'name'           => 'Juno Digital',
            'implementation' => DestinationDrivers\JunoDigital::class,
        ],
        [
            'id'             => self::AXELA,
            'name'           => 'Axela',
            'implementation' => DestinationDrivers\Axela::class,
        ],
        [
            'id'             => self::MAESTRO,
            'name'           => 'Maestro',
            'implementation' => DestinationDrivers\MaestroNetwork::class,
        ],
        [
            'id'             => self::MOEKS,
            'name'           => 'Moeks',
            'implementation' => DestinationDrivers\Moeks::class,
        ],
        [
            'id'             => self::PAYMENTS,
            'name'           => 'Payments',
            'implementation' => DestinationDrivers\Payments::class,
        ],
        [
            'id'             => self::YOOSE,
            'name'           => 'Yoose',
            'implementation' => DestinationDrivers\Yoose::class,
        ],
        [
            'id'             => self::PLATFORM500,
            'name'           => 'Platform-500',
            'implementation' => DestinationDrivers\Platform500::class,
        ],
        [
            'id'             => self::MEDIAPRO,
            'name'           => 'MediaPro',
            'implementation' => DestinationDrivers\MediaPro::class,
        ],
        [
            'id'             => self::LEGACYFX,
            'name'           => 'LegacyFx',
            'implementation' => DestinationDrivers\LegacyFx::class,
        ],
        [
            'id'             => self::AFFLION,
            'name'           => 'AffLion',
            'implementation' => DestinationDrivers\AffLion::class,
        ],
        [
            'id'             => self::VOIPPLUS,
            'name'           => 'VoipPlus',
            'implementation' => DestinationDrivers\VoipPlus::class,
        ],
        [
            'id'             => self::AMOCRM,
            'name'           => 'AmoCRM',
            'implementation' => DestinationDrivers\AmoCrm::class,
        ],
        [
            'id'             => self::UBGROUP,
            'name'           => 'UbGroup',
            'implementation' => DestinationDrivers\UbGroup::class,
        ],
        [
            'id'             => self::AWFULCRM,
            'name'           => 'AwfulCRM',
            'implementation' => DestinationDrivers\AwfulCRM::class,
        ],
        [
            'id'             => self::SOMEONES,
            'name'           => 'Someones',
            'implementation' => DestinationDrivers\Someones::class,
        ],
        [
            'id'             => self::PLANETINV,
            'name'           => 'PlanetInv',
            'implementation' => DestinationDrivers\PlanetInv::class,
        ],
        [
            'id'             => self::HYPER,
            'name'           => 'Hyper',
            'implementation' => DestinationDrivers\Hyper::class,
        ],
        [
            'id'             => self::CLICKSAFF,
            'name'           => 'ClicksAff',
            'implementation' => DestinationDrivers\ClicksAff::class,
        ],
        [
            'id'             => self::PROFITC,
            'name'           => 'Profit',
            'implementation' => DestinationDrivers\ProfitCenter::class,
        ],
        [
            'id'             => self::CRYPTOCENTER,
            'name'           => 'Crypto Center',
            'implementation' => DestinationDrivers\CryptoCenter::class,
        ],
        [
            'id'             => self::EXBINARY,
            'name'           => 'ExBinary',
            'implementation' => DestinationDrivers\ExBinary::class,
        ],
        [
            'id'             => self::EPC,
            'name'           => 'EPC',
            'implementation' => DestinationDrivers\Epc::class,
        ],
        [
            'id'             => self::CRYPIM,
            'name'           => 'CrypIM',
            'implementation' => DestinationDrivers\CrypIM::class,
        ],
        [
            'id'             => self::SCAMCOIN,
            'name'           => 'ScamCoin',
            'implementation' => DestinationDrivers\ScamCoin::class,
        ],
        [
            'id'             => self::SHEIKS,
            'name'           => 'AdSheiks',
            'implementation' => DestinationDrivers\Adsheiks::class,
        ],
        [
            'id'             => self::MONIACC,
            'name'           => 'Moniacc',
            'implementation' => DestinationDrivers\Moniacc::class,
        ],
        [
            'id'             => self::VANI,
            'name'           => 'VaniInvest',
            'implementation' => DestinationDrivers\VaniInvest::class,
        ],
        [
            'id'             => self::JAGCRM,
            'name'           => 'Jag CRM',
            'implementation' => DestinationDrivers\JagCrm::class,
        ],
        [
            'id'             => self::TGI,
            'name'           => 'TGI',
            'implementation' => DestinationDrivers\Tgi::class,
        ],
        [
            'id'             => self::HISTMO,
            'name'           => 'Histmo',
            'implementation' => DestinationDrivers\Histmo::class,
        ],
        [
            'id'             => self::BILLTYPE,
            'name'           => 'BillType',
            'implementation' => DestinationDrivers\BillType::class,
        ],
        [
            'id'             => self::THRBUYERS,
            'name'           => '13Buyers',
            'implementation' => DestinationDrivers\ThrBuyers::class,
        ],
        [
            'id'             => self::AFFTEK,
            'name'           => 'AffTek',
            'implementation' => DestinationDrivers\Afftek::class,
        ],
        [
            'id'             => self::CARECORP,
            'name'           => 'CareCorp',
            'implementation' => DestinationDrivers\CareCorp::class,
        ],
        [
            'id'             => self::BOARD,
            'name'           => 'Board',
            'implementation' => DestinationDrivers\Board::class
        ],
        [
            'id'             => self::A2A,
            'name'           => 'a2a',
            'implementation' => DestinationDrivers\AtoA::class
        ],
        [
            'id'             => self::DIGIT,
            'name'           => 'Digit',
            'implementation' => DestinationDrivers\Digit::class
        ],
        [
            'id'             => self::AFFMIDAS,
            'name'           => 'AffMidas',
            'implementation' => DestinationDrivers\Affmidas::class
        ],
        [
            'id'             => self::PUNKS,
            'name'           => 'Punks',
            'implementation' => DestinationDrivers\MediaPunks::class
        ],
        [
            'id'             => self::REVOLUT,
            'name'           => 'Revolut',
            'implementation' => DestinationDrivers\Revolut::class
        ],
        [
            'id'             => self::FINUTRADE,
            'name'           => 'FinUTrade',
            'implementation' => DestinationDrivers\Finutrade::class
        ],
        [
            'id'             => self::SCALEO,
            'name'           => 'Scaleo',
            'implementation' => DestinationDrivers\Scaleo::class
        ],
        [
            'id'             => self::HULU,
            'name'           => 'Hulu',
            'implementation' => DestinationDrivers\Hulu::class
        ],
        [
            'id'             => self::SKYNET,
            'name'           => 'Skynet',
            'implementation' => DestinationDrivers\SkynetLeads::class
        ],
        [
            'id'             => self::ARION,
            'name'           => 'Arion',
            'implementation' => DestinationDrivers\Arion::class
        ],
        [
            'id'             => self::FRANKLIN,
            'name'           => 'Franklin',
            'implementation' => DestinationDrivers\Franklin::class
        ],
        [
            'id'             => self::BEATSCLICK,
            'name'           => 'Beatsclick',
            'implementation' => DestinationDrivers\BeatsClick::class
        ],
        [
            'id'             => self::REBEL,
            'name'           => 'Rebel',
            'implementation' => DestinationDrivers\Rebel::class
        ],
        [
            'id'             => self::UKRTRAFF,
            'name'           => 'UkrTraff',
            'implementation' => DestinationDrivers\UkrTraff::class
        ],
        [
            'id'             => self::THM,
            'name'           => '3HM',
            'implementation' => DestinationDrivers\ThreeHm::class
        ],
        [
            'id'             => self::DOZATEAM,
            'name'           => 'DozaTeam',
            'implementation' => DestinationDrivers\DozaTeam::class
        ],
        [
            'id'             => self::TIMGROUP,
            'name'           => 'TimGroup',
            'implementation' => DestinationDrivers\TimGroup::class
        ],
        [
            'id'             => self::ONEXCPA,
            'name'           => '1xCpa',
            'implementation' => DestinationDrivers\OneXCpa::class
        ],
        [
            'id'             => self::NORMAL,
            'name'           => 'Normal',
            'implementation' => DestinationDrivers\NormalTraffic::class
        ],
        [
            'id'             => self::PLEXUS,
            'name'           => 'Plexus',
            'implementation' => DestinationDrivers\Plexus::class
        ],
        [
            'id'             => self::OCEAN,
            'name'           => 'Ocean',
            'implementation' => DestinationDrivers\Ocean::class
        ],
        [
            'id'             => self::OVO,
            'name'           => 'Ovo',
            'implementation' => DestinationDrivers\Ovo::class
        ],
        [
            'id'             => self::SIXTNINE,
            'name'           => 'SixtyNine',
            'implementation' => DestinationDrivers\SixtyNine::class
        ],
        [
            'id'             => self::GLOBALFINANCE,
            'name'           => 'Global Finance',
            'implementation' => DestinationDrivers\GlobalFinance::class
        ],
        [
            'id'             => self::HUMMUS,
            'name'           => 'Hummus',
            'implementation' => DestinationDrivers\Hummus::class
        ],
        [
            'id'             => self::CLICKOUT,
            'name'           => 'ClickOut',
            'implementation' => DestinationDrivers\ClickOut::class
        ],
        [
            'id'             => self::LGCINVESTMENTS,
            'name'           => 'Lgc Investments',
            'implementation' => DestinationDrivers\LgcInvestments::class
        ],
        [
            'id'             => self::TRADESKILLS,
            'name'           => 'TradeSkills',
            'implementation' => DestinationDrivers\TradeSkills::class
        ],
        [
            'id'             => self::REDCRM,
            'name'           => 'Red CRM',
            'implementation' => DestinationDrivers\RedCrm::class
        ],
        [
            'id'             => self::GASTRADE,
            'name'           => 'Gas Trade',
            'implementation' => DestinationDrivers\GasTrade::class
        ],
        [
            'id'             => self::LUNOCRYPT,
            'name'           => 'LunoCrypt',
            'implementation' => DestinationDrivers\LunoCrypt::class
        ],
        [
            'id'             => self::NORMALNEW,
            'name'           => 'Normal NEW',
            'implementation' => DestinationDrivers\NormalTrafficNew::class
        ],
        [
            'id'             => self::MOONSTAR,
            'name'           => 'MoonStar',
            'implementation' => DestinationDrivers\Moonstar::class
        ],
        [
            'id'             => self::SWISSROI,
            'name'           => 'SwissRoi',
            'implementation' => DestinationDrivers\SwissRoi::class
        ],
        [
            'id'             => self::HEADSINVEST,
            'name'           => 'Headsinvest',
            'implementation' => DestinationDrivers\Headsinvest::class
        ],
        [
            'id'             => self::WOODBROOK,
            'name'           => 'WoodBrook',
            'implementation' => DestinationDrivers\WoodBrook::class
        ],
        [
            'id'             => self::HYPERNET,
            'name'           => 'HyperNet',
            'implementation' => DestinationDrivers\HyperNet::class
        ],
        [
            'id'             => self::PREMIUMFINSOL,
            'name'           => 'PremiumFinsol',
            'implementation' => DestinationDrivers\PremiumFinsol::class
        ],
        [
            'id'             => self::SCHRM,
            'name'           => 'ScHRM',
            'implementation' => DestinationDrivers\ScHrm::class
        ],
        [
            'id'             => self::VALLETTATRADE,
            'name'           => 'ValletaTrade',
            'implementation' => DestinationDrivers\VallettaTrade::class
        ],
        [
            'id'             => self::GOLDCRM,
            'name'           => 'GoldCRM',
            'implementation' => DestinationDrivers\GoldCrm::class
        ],
        [
            'id'             => self::GOLDENBIT,
            'name'           => 'GoldenBit',
            'implementation' => DestinationDrivers\GoldenBit::class
        ],
        [
            'id'             => self::CRYPTOTRAFFIC,
            'name'           => 'CryptoTraffic',
            'implementation' => DestinationDrivers\CryptoTraffic::class
        ],
        [
            'id'             => self::AFFTENOR,
            'name'           => 'Afftenor',
            'implementation' => DestinationDrivers\Afftenor::class
        ],
        [
            'id'             => self::SAFECAPINVEST,
            'name'           => 'SafeCapInvest',
            'implementation' => DestinationDrivers\SafeCapInvest::class
        ],
        [
            'id'             => self::SINERGIA,
            'name'           => 'Sinergia',
            'implementation' => DestinationDrivers\Sinergia::class
        ],
        [
            'id'             => self::MEDIFIN,
            'name'           => 'Medifin',
            'implementation' => DestinationDrivers\Medifin::class
        ],
        [
            'id'             => self::ATSINVEST,
            'name'           => 'AtsInvest',
            'implementation' => DestinationDrivers\AtsInvest::class
        ],
        [
            'id'             => self::KINGSOFTRAFFIC,
            'name'           => 'kingsoftraffic',
            'implementation' => DestinationDrivers\KingsOfTraffic::class
        ],
        [
            'id'             => self::VEXXSEL,
            'name'           => 'vexxsel',
            'implementation' => DestinationDrivers\Vexxsel::class
        ],
        [
            'id'             => self::TRUSTLEADS,
            'name'           => 'trustleads',
            'implementation' => DestinationDrivers\TrustLeads::class
        ],
        [
            'id'             => self::ALPHAINVEST,
            'name'           => 'alphainvest',
            'implementation' => DestinationDrivers\AlphaInvest::class
        ],
        [
            'id'             => self::PAFNET,
            'name'           => 'PAFnet',
            'implementation' => DestinationDrivers\PafNet::class
        ],
        [
            'id'             => self::ADSBOARDAFF,
            'name'           => 'AdsBoard',
            'implementation' => DestinationDrivers\AdsBoardAff::class
        ],
        [
            'id'             => self::HELLLEADS,
            'name'           => 'HellLeads',
            'implementation' => DestinationDrivers\HellLeads::class
        ],
        [
            'id'             => self::PARTNERSTCRM,
            'name'           => 'PartnersTCRM',
            'implementation' => DestinationDrivers\PartnersTCRM::class
        ],
        [
            'id'             => self::MFURY,
            'name'           => 'Mfury',
            'implementation' => DestinationDrivers\Mfury::class
        ],
        [
            'id'             => self::SOULCAPITAL,
            'name'           => 'SoulCapital',
            'implementation' => DestinationDrivers\Soulcapital::class
        ],
        [
            'id'             => self::BELMAR,
            'name'           => 'Belmar',
            'implementation' => DestinationDrivers\Belmar::class
        ],
        [
            'id'             => self::LXCRM,
            'name'           => 'LXCRM',
            'implementation' => DestinationDrivers\LXCRM::class
        ],
        [
            'id'             => self::SENSUMGLOBAL,
            'name'           => 'SensumGlobal',
            'implementation' => DestinationDrivers\SensumGlobal::class
        ],
        [
            'id'             => self::GLOBINC,
            'name'           => 'Globinc',
            'implementation' => DestinationDrivers\Globinc::class
        ],
        [
            'id'             => self::VIRGINGROUP,
            'name'           => 'VirginGroup',
            'implementation' => DestinationDrivers\VirginGroup::class
        ],
        [
            'id'             => self::LEADPANDA,
            'name'           => 'LeadPanda',
            'implementation' => DestinationDrivers\LeadPanda::class
        ],
        [
            'id'             => self::OPTIMUS,
            'name'           => 'Optimus',
            'implementation' => DestinationDrivers\Optimus::class
        ],
        [
            'id'             => self::METROPOLITAN,
            'name'           => 'Metropolitan',
            'implementation' => DestinationDrivers\Metropolitan::class
        ],
    ];
}
