<?php

$search = "психолог";
$dateFrom = "2023-01-01T00:00:01Z";
$dateTo = "2023-12-31T23:59:00Z";

function main($search, $dateFrom, $dateTo) {
    $offset = 1;
    $limit = 100;
    $wh = true;

    // $hFile = fopen("res.json", "a+");
    $hFile = fopen("res.csv", "a+");
    // fwrite($hFile, '{"result": [');
    fputcsv($hFile, [
        'id',
        'region name',
        'company name',
        'creation-date',
        'salary',
        'salary_min',
        'salary_max',
        'job-name',
        'employment',
        'schedule',
        'duty',
        'vac_url',
        'category specialisation',
        'requirement education',
        'requirement experience',
        'addresses location',
    ], ';', '"');

    do {
        var_dump($offset);
        $query = [
            'text' => $search,
            'offset' => $offset,
            'limit' => $limit,
            'modifiedFrom' => $dateFrom,
            'modifiedTo' => $dateTo
        ];
        $result = file_get_contents("http://opendata.trudvsem.ru/api/v1/vacancies?" . http_build_query($query));
        $jsonRes = json_decode($result, true);

        if (isset($jsonRes['results']) && isset($jsonRes['results']['vacancies'])) {
            foreach($jsonRes['results']['vacancies'] as $key => $item) {
                // fwrite($hFile, json_encode($item, JSON_UNESCAPED_UNICODE) . " ,");
                $arr = [
                    $item['vacancy']['id'],
                    $item['vacancy']['region']['name'],
                    $item['vacancy']['company']['name'],
                    $item['vacancy']['creation-date'],
                    $item['vacancy']['salary']??"",
                    $item['vacancy']['salary_min']??"",
                    $item['vacancy']['salary_max']??"",
                    $item['vacancy']['job-name']??"",
                    $item['vacancy']['employment']??"",
                    $item['vacancy']['schedule']??"",
                    $item['vacancy']['duty']??"",
                    $item['vacancy']['vac_url']??"",
                    $item['vacancy']['category']['specialisation']??"",
                    $item['vacancy']['requirement']['education']??"",
                    $item['vacancy']['requirement']['experience']??"",
                    $item['vacancy']['addresses']['address'][0]['location']??""
                ];
                fputcsv($hFile, $arr, ';', '"');
            }

            $offset++;
        } else {
            $wh = false;
        }
        var_dump($wh);
    } while($wh);

    // fwrite($hFile, ']}');
    fclose($hFile);
}

main($search, $dateFrom, $dateTo);
