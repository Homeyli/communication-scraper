<?php

namespace App\Services;

use PhpQuery\PhpQuery;

class BelinkCurl {

    protected $listLink = 'https://server.belink.ir/api/search/profile';
    protected $originurl = 'https://belink.ir';
    protected $authserver = 'server.belink.ir';
    protected $authkey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJlbWFpbCI6ImhvbWVpbHlAZ21haWwuY29tIiwidXNlcklkIjoiNjNkYWI4YzFhNDQ4NWIxNWYzOTUxMmRjIiwidHlwZSI6Ik5PUk1BTCIsImlhdCI6MTY3NTM3NTE5MywiZXhwIjoxNjc1NDYxNTkzfQ.RAWCNtJTgpAmpv86XOK8FFkJBXzwdi-40d1ukSZ7-hw";
    protected $domain = 'belink.ir';

    protected function call ($data=null,$method="POST",$link=null,$jsontype=true) {

        $curl = curl_init();

        $accepttype = $jsontype ? 'application/json, text/plain, */*' : 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9';
        
        //die($accepttype);
        // print_r($link);die();
        //print_r($data);die();
        //die($method);
        
        curl_setopt_array($curl, [
            CURLOPT_URL => is_null($link) ? $this->listLink : $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => is_null($data) ? null : json_encode($data),
            CURLOPT_HTTPHEADER => [
                // Set here requred headers
                'authority: ' . $this->authserver,
                "accept: $accepttype",
                'accept-language: en-US,en;q=0.9,fa;q=0.8,ar;q=0.7',
                'authorization: Bearer ' . $this->authkey,
                'content-type: application/json',
                'origin: ' . $this->originurl,
                'referer: https://belink.ir/',
                'sec-ch-ua: ".Not/A)Brand";v="99", "Google Chrome";v="103", "Chromium";v="103"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Linux"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: same-site',
                'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36',
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);

        //print_r($response);die('t');
        return $jsontype ? json_decode($response) : $response;

    }

    protected function createData($type = 'COMPANY',$value='',$limit=1,$offset=0,$filters=null,$seo=null) {

        return [

            'type' => $type,
            'value' => $value,
            'limit' => $limit,
            'offset' => $offset,
            'filters' => $filters,
            'seo' => $seo,
        ];
    }

    public function find($type='company',$slug=null) {

        $link = "https://belink.ir/$type/" . urlencode(trim($slug));

        $_content = $this->call(
            method: "GET",
            link: $link,
            jsontype: false
        );

        $pq = new PhpQuery;
        $pq->load_str($_content);
        $dom = $pq->query('script[type=application/ld+json]');
        $_json_string = $pq->innerHTML($dom[2]);

        return json_decode($_json_string);
    }

    protected function getCompanyDetails ($_company) {

        $company = [
            'uniqid' => $_company->_id,
            'slug' => $_company->nameId->value,
            'name' => @$_company->overview->shortDescription->value,
            'description' => @$_company->overview->longDescription->value,
            'other' => $this->find(slug: $_company->nameId->value)
        ];



        return $company;
    }

    public function testcall () {

        return $this->call();
    }

    
    public function getCompaniesCount () {

        return $this->call($this->createData(

            limit: 1,
            offset: 0

        ))->result->totalDocs;
    }

    public function getLimitCompanies ($limit = 1,$offset = 0) {

        $_companies =  $this->call($this->createData(
            limit: $limit,
            offset: $offset
        ));

        $companies = [];

        foreach ($_companies->result->docs as $company) {
            array_push($companies,$this->getCompanyDetails($company));
        }

        print_r($companies);die();
    }



    public function getCompanies () {

        
        $data = $this->getLimitCompanies();
        die($data->result->totalDocs . 't');
    }




}