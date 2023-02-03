<?php

namespace App\Services;

use PhpQuery\PhpQuery;
use App\Models\Communication;
use App\Models\CommunicationDetail;

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

    private function convert_num ($string) {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    
        $num = range(0, 9);
        $convertedPersianNums = str_replace($persian, $num, $string);
        $englishNumbersOnly = str_replace($arabic, $num, $convertedPersianNums);
        
        return $englishNumbersOnly;
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

        if (!empty($_content)) {
            $pq = new PhpQuery;
            $pq->load_str($_content);
            $dom = $pq->query('script[type=application/ld+json]');
            $_json_string = $pq->innerHTML($dom[2]);
        } else {

            echo ("\n$link\n");
        }

        return json_decode($_json_string);
    }

    protected function getCompanyDetails ($_company) {

        $_details = (array)$this->find(slug: $_company->nameId->value);
        //print_r($_company);print_r($_details);die();
        if (isset($_details['logo']) || isset($_details['image'])) {

            if (isset($_details['logo'])) 
                $imgKey = 'logo';

            if (isset($_details['image'])) 
            $imgKey = 'image';

            if (filter_var($_details[$imgKey], FILTER_VALIDATE_URL)) {
                $_details['imagehash'] = str_replace('https://server.belink.ir/images/','',$_details[$imgKey]);
            }
        }   

        if(isset($_details['telephone'])) {
            $_details['telephone'] = $this->convert_num($_details['telephone']);
        }

        if (isset($_details['url'])) {

            $_parse_url = parse_url($_details['url']);
            if ($_parse_url['host'] == $this->domain) {
                unset($_details['url']);
            } else {
            
                $generated_email = 'info@' . $_parse_url['host'];
                if (@$_details['email'] != $generated_email) {
                    $_details['generated_email'] = $generated_email;
                }
            }
        }

        if (isset($_details['email'])) {
            $_details['email'] = trim(urldecode($_details['email']));
        }

        if (isset($_details['location'])) {

            foreach ($_details['location'] as $key => $value) {
                $_details['location'][$key] = $value->streetAddress;
            }
        }

        if (isset($_details['employee'])) {

            foreach ($_details['employee'] as $key => $value) {
                $_details['employee'][$key] = $value->name;
            }
        }

        if (isset($_details['founders'])) {

            foreach ($_details['founders'] as $key => $value) {
                $_details['founders'][$key] = $value->name;
            }
        }

        if (isset($_details['funder'])) {

            foreach ($_details['funder'] as $key => $value) {
                $_details['funder'][$key] = $value->name;
            }
        }

        if (isset($_details['foundingDate']) && (is_null($_details['foundingDate']) || empty($_details['foundingDate']))) {
            unset($_details['foundingDate']);
        }

        if (isset($_details['dissolutionDate']) && (is_null($_details['dissolutionDate']) || empty($_details['dissolutionDate']))) {
            unset($_details['dissolutionDate']);
        }

        if (isset($_details['sameAs'])) {
            $_details['social'] = $_details['sameAs'];
            unset($_details['sameAs']);
        }

       
        unset($_details['@context']);
        unset($_details['@type']);
        unset($_details['@id']);
        unset($_details['name']);
        unset($_details['knowsAbout']);
        unset($_details['areaServed']);
        unset($_details['image']);
        unset($_details['logo']);
        unset($_details['description']);

        
        $company = [
            'uniqid' => $_company->_id,
            'slug' => $_company->nameId->value,
            'name' => @$_company->overview->companyName,
            'description' => @$_company->overview->longDescription->value,
            'details' => $_details
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

        return $companies;
    }


    public function storeCompany ($companies) {

        foreach ($companies as $company) {

            //print_r($company);//die();
            // create communication record
            $communication = Communication::create([
                'type' => 'company',
                'uniqid' => $company['uniqid'],
                'slug' => $company['slug'],
                'name' => $company['name'],
                'description' => $company['description'],
                'source' => $this->domain,
            ]);

            foreach ($company['details'] as $key => $details) {

                if (is_array($details)) {
                    foreach ($details as $value) {
                        CommunicationDetail::create([
                            'communication_id' => $communication->id,
                            'key' => $key,
                            'value' => $value
                        ]);
                    }
                } else {

                    CommunicationDetail::create([
                        'communication_id' => $communication->id,
                        'key' => $key,
                        'value' => $details
                    ]);

                }

            } 
        }
    }


    // public function getCompanies () {

    //     $data = $this->getLimitCompanies();
    // }




}