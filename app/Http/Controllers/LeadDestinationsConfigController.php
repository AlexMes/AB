<?php

namespace App\Http\Controllers;

use App\LeadDestinationDriver;
use Illuminate\Http\Request;

class LeadDestinationsConfigController extends Controller
{
    public const PARAM_REQUIRED    = 'required';
    public const PARAM_NO_REQUIRED = 'not_required';

    protected const TYPE_NULL      = 'null';
    protected const TYPE_VARIABLE  = 'variable';
    protected const TYPE_STRING    = 'string';
    protected const TYPE_ARRAY     = 'array';
    protected const TYPE_VALUE     = 'value';
    protected const TYPE_REQUIRED  = 'required';

    private const TYPE_ALL = [
        self::TYPE_NULL     => "/\[(.*?)\?\?(.*?)null(.*?);/",
        self::TYPE_VARIABLE => "/\[(.*?)\?\?(.*?)this(.*?);/",
        self::TYPE_STRING   => "/\[(.*?)\?\?(.*?)\';/",
        self::TYPE_ARRAY    => "/\[(.*?)\?\?(.*?)\];/",
        self::TYPE_VALUE    => "/\[(.*?)\?\?(.*?);/",
        self::TYPE_REQUIRED => "/\[(.*?);/",
    ];

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $this->authorize('showConfiguration', LeadDestinationDriver::class);

        $driver = collect((new LeadDestinationDriver())->getRows())
            ->where('id', $request->get('driver')['id'])
            ->first();

        if (!$driver) {
            throw new \Exception("Driver {$request->get('driver')['id']} not found.");
        }

        $str = [
            "search" => [
                '\\',
                'App',
            ],
            "replace" => [
                '/',
                'app',
            ],
        ];

        $fileContent = file_get_contents(base_path(
            str_replace($str['search'], $str['replace'], $driver['implementation'] . '.php')
        ));

        if (!$fileContent) {
            throw new \Exception("Empty file, can't get configuration.");
        }

        $content = stristr($fileContent, '__construct');
        $content = stristr($content, '{');
        $content = stristr($content, '}', true);

        if (!$content) {
            throw new \Exception("Error with reading data from file");
        }

        foreach (self::TYPE_ALL as $key => $value) {
            $count   = preg_match_all($value, $content, $attachments);
            $content = str_replace($attachments[0], '', $content);

            for ($i = 0; $i < $count; $i++) {
                if ($key === self::TYPE_VARIABLE) {
                    if (preg_match_all("/this->(.*?);/", $attachments[0][$i], $matches)) {
                        $paramName = stristr($attachments[0][$i], '[\'');
                        $paramName = stristr($paramName, '\']', true);
                        $paramName = str_replace("['", "", $paramName);
                        preg_match('/\$' . $matches[1][0] . '(.*?)[^=];/', $fileContent, $result);

                        $foundParameter[self::PARAM_NO_REQUIRED][$paramName] =
                            trim(str_replace(['$' . $matches[1][0], '=', ';'], "", $result[0]));
                    }
                }

                if ($key === self::TYPE_VALUE) {
                    if (preg_match_all("/\'(.*?)\'/", $attachments[0][$i], $matches)) {
                        preg_match("/\?\?\s(.*?);/", $attachments[0][$i], $result);
                        $foundParameter[self::PARAM_NO_REQUIRED][$matches[1][0]] =
                            str_replace(['??', ' ', ';'], "", $result[0]);
                    }
                }

                if ($key === self::TYPE_ARRAY) {
                    if (preg_match_all("/\'(.*?)\'/", $attachments[0][$i], $matches)) {
                        preg_match("/\?\?\s\[(.*?)](.*?);/", $attachments[0][$i], $result);
                        $foundParameter[self::PARAM_NO_REQUIRED][$matches[1][0]] =
                            str_replace(['??', ';'], "", $result[0]);
                    }
                }

                if ($key === self::TYPE_STRING) {
                    if (preg_match_all("/\'(.*?)\'/", $attachments[0][$i], $matches)) {
                        $foundParameter[self::PARAM_NO_REQUIRED][$matches[1][0]] = $matches[1][1];
                    }
                }

                if (in_array($key, [self::TYPE_REQUIRED, self::TYPE_NULL])) {
                    if (preg_match("/\'(.*?)'/", $attachments[0][$i], $matches)) {
                        $foundParameter[self::TYPE_NULL === $key ? self::PARAM_NO_REQUIRED : self::PARAM_REQUIRED][$matches[1]] = null;
                    }
                }
            }
        }

        if (empty($foundParameter)) {
            throw new \Exception("Can't found parameter in config");
        }

        return json_encode([
            'data' => $foundParameter
        ], true);
    }
}
