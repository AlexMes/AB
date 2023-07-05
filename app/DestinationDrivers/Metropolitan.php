<?php

namespace App\DestinationDrivers;

use App\Lead;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class Metropolitan implements Contracts\DeliversLeadToDestination
{
    protected string $url = 'https://api.metropolitan.realestate/webhooks/partners/RE/ki75jspMngsh33/';
    protected $language;

    public function __construct($configuration = null)
    {
        $this->language        = $configuration['lang'] ?? null;
    }

    public function send(Lead $lead): void
    {
        $this->response = Http::asForm()->post(
            $this->url,
            $payload = [
                'f_name' => $lead->firstname,
                'l_name' => $lead->lastname,
                'phone' => $lead->phone,
                'email' => $lead->getOrGenerateEmail(),
                'lang' => $this->language,
            ]
        );

        $lead->addEvent('payload', $payload);
        $lead->addEvent('responded', $this->response->json());

        var_dump(  $this->response  );
        //var_dump( $this->response->body() );

    }

    public function isDelivered(): bool
    {
        return $this->response->successful() && data_get($this->response->json(), 'error') === false;
    }

    public function getError(): ?string
    {
        return data_get($this->response->json(), 'status', $this->response->body());
    }

    public function getRedirectUrl(): ?string
    {
        return null;
    }

    public function getExternalId(): ?string
    {
        return data_get($this->response->json(), 'lead_id', null);
    }
}

/*
Illuminate\Http\Client\Response {#8805
    #response: GuzzleHttp\Psr7\Response {#8830
-reasonPhrase: "OK"
-statusCode: 200
-headers: array:11 [
    "Date" => array:1 [
    0 => "Thu, 29 Jun 2023 09:07:34 GMT"
]
  "Content-Type" => array:1 [
    0 => "text/html; charset=UTF-8"
]
  "Transfer-Encoding" => array:1 [
    0 => "chunked"
]
  "Connection" => array:1 [
    0 => "keep-alive"
]
  "access-control-allow-origin" => array:1 [
    0 => "*"
]
  "access-control-allow-credentials" => array:1 [
    0 => "true"
]
  "access-control-allow-methods" => array:1 [
    0 => "POST, GET"
]
  "CF-Cache-Status" => array:1 [
    0 => "DYNAMIC"
]
  "Set-Cookie" => array:1 [
    0 => "__cf_bm=LtNonoTRFSaU0ahhDGQcmJApC34qe6k9oSGUsh1qZVU-1688029654-0-AU8zKIdBuvC55M+V3uooAkVUyUQOT1lF1fZ7HKvH8A2waAvqE6xR+Ff+cCCoXQMmeafrhxAhCEW17e9FxJexqW0=; path=/; expires=Thu, 29-Jun-23 09:37:34 GMT; domain=.metropolit
an.realestate; HttpOnly; Secure; SameSite=None"
]
  "Server" => array:1 [
    0 => "cloudflare"
]
  "CF-RAY" => array:1 [
    0 => "7ded051b3d681cc7-FRA"
]
]
-headerNames: array:11 [
    "date" => "Date"
  "content-type" => "Content-Type"
  "transfer-encoding" => "Transfer-Encoding"
  "connection" => "Connection"
  "access-control-allow-origin" => "access-control-allow-origin"
  "access-control-allow-credentials" => "access-control-allow-credentials"
  "access-control-allow-methods" => "access-control-allow-methods"
  "cf-cache-status" => "CF-Cache-Status"
  "set-cookie" => "Set-Cookie"
  "server" => "Server"
  "cf-ray" => "CF-RAY"
]
-protocol: "1.1"
-stream: GuzzleHttp\Psr7\Stream {#8829
    -stream: stream resource {@1533
    wrapper_type: "PHP"
    stream_type: "TEMP"
    mode: "w+b"
    unread_bytes: 0
    seekable: true
    uri: "php://temp"
    options: []
  }
  -size: null
    -seekable: true
    -readable: true
    -writable: true
    -uri: "php://temp"
    -customMetadata: []
}
}
#decoded: null
+"cookies": GuzzleHttp\Cookie\CookieJar {#8809
-cookies: array:1 [
    0 => GuzzleHttp\Cookie\SetCookie {#8835
    -data: array:10 [
        "Name" => "__cf_bm"
      "Value" => "LtNonoTRFSaU0ahhDGQcmJApC34qe6k9oSGUsh1qZVU-1688029654-0-AU8zKIdBuvC55M+V3uooAkVUyUQOT1lF1fZ7HKvH8A2waAvqE6xR+Ff+cCCoXQMmeafrhxAhCEW17e9FxJexqW0="
      "Domain" => ".metropolitan.realestate"
      "Path" => "/"
      "Max-Age" => null
      "Expires" => 1688031454
      "Secure" => true
      "Discard" => false
      "HttpOnly" => true
      "SameSite" => "None"
    ]
  }
]
-strictMode: false
}
+"transferStats": GuzzleHttp\TransferStats {#8831
-request: GuzzleHttp\Psr7\Request {#8825
    -method: "POST"
    -requestTarget: null
    -uri: GuzzleHttp\Psr7\Uri {#8815
        -scheme: "https"
        -userInfo: ""
        -host: "api.metropolitan.realestate"
        -port: null
        -path: "/webhooks/partners/RE/ki75jspMngsh33/"
        -query: ""
        -fragment: ""
  }
  -headers: array:4 [
        "Content-Length" => array:1 [
        0 => "84"
    ]
    "User-Agent" => array:1 [
        0 => "GuzzleHttp/6.5.5 curl/7.76.1 PHP/8.0.8"
    ]
    "Host" => array:1 [
        0 => "api.metropolitan.realestate"
    ]
    "Content-Type" => array:1 [
        0 => "application/x-www-form-urlencoded"
    ]
  ]
  -headerNames: array:4 [
        "content-length" => "Content-Length"
    "user-agent" => "User-Agent"
    "host" => "Host"
    "content-type" => "Content-Type"
  ]
  -protocol: "1.1"
    -stream: GuzzleHttp\Psr7\Stream {#8816
        -stream: stream resource {@1526
      wrapper_type: "PHP"
      stream_type: "TEMP"
      mode: "w+b"
      unread_bytes: 0
      seekable: true
      uri: "php://temp"
      options: []
    }
    -size: 84
        -seekable: true
        -readable: true
        -writable: true
        -uri: "php://temp"
        -customMetadata: []
  }
}
-response: GuzzleHttp\Psr7\Response {#8830}
    -transferTime: 0.595585
    -handlerStats: array:38 [
        "url" => "https://api.metropolitan.realestate/webhooks/partners/RE/ki75jspMngsh33/"
  "content_type" => "text/html; charset=UTF-8"
  "http_code" => 200
  "header_size" => 615
  "request_size" => 295
  "filetime" => -1
  "ssl_verify_result" => 0
  "redirect_count" => 0
  "total_time" => 0.595585
  "namelookup_time" => 0.060304
  "connect_time" => 0.097839
  "pretransfer_time" => 0.173236
  "size_upload" => 84.0
  "size_download" => 0.0
  "speed_download" => 0.0
  "speed_upload" => 141.0
  "download_content_length" => -1.0
  "upload_content_length" => 84.0
  "starttransfer_time" => 0.592896
  "redirect_time" => 0.0
  "redirect_url" => ""
  "primary_ip" => "104.18.15.86"
  "certinfo" => []
  "primary_port" => 443
  "local_ip" => "10.8.0.46"
  "local_port" => 60599
  "http_version" => 2
  "protocol" => 2
  "ssl_verifyresult" => 0
  "scheme" => "HTTPS"
  "appconnect_time_us" => 173125
  "connect_time_us" => 97839
  "namelookup_time_us" => 60304
  "pretransfer_time_us" => 173236
  "redirect_time_us" => 0
  "starttransfer_time_us" => 592896
  "total_time_us" => 595585
  "appconnect_time" => 0.173125
]
-handlerErrorData: 0
  }
}

*/
