<?php

namespace App\Services;

class BelinkCurl {

    protected $apilink = 'https://server.belink.ir/api/search/profile';
    protected $originurl = 'https://belink.ir';
    protected $authserver = 'server.belink.ir';
    protected $fakedata = '{"type":"COMPANY","value":"","limit":10,"offset":1,"filters":null,"seo":false}';
    protected $authkey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJlbWFpbCI6ImhvbWVpbHlAZ21haWwuY29tIiwidXNlcklkIjoiNjNkYWI4YzFhNDQ4NWIxNWYzOTUxMmRjIiwidHlwZSI6Ik5PUk1BTCIsImlhdCI6MTY3NTI3ODU5NSwiZXhwIjoxNjc1MzY0OTk1fQ.wiIaN4vukQKt5O1HW3aovAYwSGH_l9j8GgO30ilRRQI";

    protected function call ($data=null) {

        if(is_null($data)) {
            $data = $this->fakedata;
        }

        $curl = curl_init();

            curl_setopt_array($curl, [
            CURLOPT_URL => $this->apilink,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => [
                // Set here requred headers
                'authority: ' . $this->authserver,
                'accept: application/json, text/plain, */*',
                'accept-language: en-US,en;q=0.9,fa;q=0.8,ar;q=0.7',
                'authorization: Bearer ' . $this->authkey,
                'content-type: application/json',
                'origin: ' . $this->originurl,
                'referer: ' . $this->originurl,
                'sec-ch-ua: ".Not/A)Brand";v="99", "Google Chrome";v="103", "Chromium";v="103"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "QarchOS"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-site',
                'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36',
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);

        return $response;

    }

    public function testcall () {

        return $this->call();
    }
}