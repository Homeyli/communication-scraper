<?php

namespace App\Http\Controllers;

use App\Services\BelinkCurl;
use Illuminate\Http\Request;

use PhpQuery\PhpQuery;

class Wellcome extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    //protected $link = 'https://belink.ir/company/%D8%A7%DB%8C%D8%B1%D8%A7%D9%86_%D8%A7%D8%B3%D8%AA%D8%A7%D8%B1%D8%AA%D8%A2%D9%BE_%D8%A7%D8%B3%D8%AA%D9%88%D8%AF%DB%8C%D9%88';
    protected $link = 'https://belink.ir/company/%D8%AA%D8%AC%D9%87%DB%8C%D8%B2%D8%A7%D8%AA_%D8%A2%D8%B2%D9%85%D8%A7%DB%8C%D8%B4%DA%AF%D8%A7%D9%87%DB%8C_%D9%BE%DA%A9%D9%88_-%D8%B2%DB%8C%D8%B3%DA%A9%D9%88';
    protected $domain = 'belink.ir';
    protected $originurl = 'https://belink.ir';
    protected $authserver = 'server.belink.ir';
    protected $authkey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJlbWFpbCI6ImhvbWVpbHlAZ21haWwuY29tIiwidXNlcklkIjoiNjNkYWI4YzFhNDQ4NWIxNWYzOTUxMmRjIiwidHlwZSI6Ik5PUk1BTCIsImlhdCI6MTY3NTM3NTE5MywiZXhwIjoxNjc1NDYxNTkzfQ.RAWCNtJTgpAmpv86XOK8FFkJBXzwdi-40d1ukSZ7-hw";


    public function __invoke(BelinkCurl $sraper)
    {


        // $curl = curl_init();

        // $accepttype = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9';
        

        // curl_setopt_array($curl, [
        //     CURLOPT_URL => $this->link,
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => "",
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 30000,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_POSTFIELDS => null,
        //     CURLOPT_CUSTOMREQUEST => "GET",
        //     CURLOPT_HTTPHEADER => [
        //         // Set here requred headers
        //         'authority: ' . $this->authserver,
        //         "accept: $accepttype",
        //         'accept-language: en-US,en;q=0.9,fa;q=0.8,ar;q=0.7',
        //         'authorization: Bearer ' . $this->authkey,
        //         'content-type: application/json',
        //         'origin: ' . $this->originurl,
        //         'referer: https://belink.ir/',
        //         'sec-ch-ua: ".Not/A)Brand";v="99", "Google Chrome";v="103", "Chromium";v="103"',
        //         'sec-ch-ua-mobile: ?0',
        //         'sec-ch-ua-platform: "Linux"',
        //         'sec-fetch-dest: empty',
        //         'sec-fetch-mode: cors',
        //         'sec-fetch-site: same-site',
        //         'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36',
        //     ],
        // ]);

        // $response = curl_exec($curl);
        // $err = curl_error($curl);
        
        // curl_close($curl);

        // print_r($response);die();
        // return json_decode($response);

        $response = $sraper->find(slug: '%D8%AA%D8%AC%D9%87%DB%8C%D8%B2%D8%A7%D8%AA_%D8%A2%D8%B2%D9%85%D8%A7%DB%8C%D8%B4%DA%AF%D8%A7%D9%87%DB%8C_%D9%BE%DA%A9%D9%88_-%D8%B2%DB%8C%D8%B3%DA%A9%D9%88');

        //die($response);
        $pq = new PhpQuery;
        $pq->load_str($response);
// 
        $dom = $pq->query('script[type=application/ld+json]');
        //die($dom);
        var_dump($dom);
        $_data = $pq->innerHTML($dom[2]);
        

        print_r(json_decode($_data));die();
    }
}
