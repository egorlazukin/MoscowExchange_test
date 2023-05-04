<?
class ApiRequest {
    private $header = [
	'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
    'Accept-Encoding: gzip, deflate, br',
    'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
    'Host: www.moex.com',
    'Pragma: no-cache',
    'sec-ch-ua-mobile: ?0',
    'sec-ch-ua-platform: "Windows"',
    'Sec-Fetch-Dest: document',
    'Sec-Fetch-Mode: navigate',
    'Sec-Fetch-Site: none',
    'Sec-Fetch-User: ?1',
    'Upgrade-Insecure-Requests: 1',
	'User-Agent: PostmanRuntime/7.32.2',
	'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
	'Accept-Encoding: gzip, deflate, br',
	'Connection: keep-alive',
    'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
	];
	private $cookie = '_ym_uid=1683127090126929661; _ym_d=1683127090; _ga=GA1.2.397190181.1683127090; _gid=GA1.2.76919165.1683127090; _ym_isad=2';
    private $apiUrl;
    private $sears = "Объем торгов в ";
    public function __construct($apiUrl) {
        $this->apiUrl = $apiUrl;
    }

    public function sendRequest($endpoint) {
        if (strpos($endpoint, 'http') !== false) {
            $ch = curl_init($endpoint);
            curl_setopt($ch, CURLOPT_ENCODING ,"");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
            $response = curl_exec($ch);             
            curl_close($ch);
            return $response;    
        }
        $ch = curl_init($this->apiUrl.$endpoint);
        curl_setopt($ch, CURLOPT_ENCODING ,"");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
    public function SearsUrl($response)
    {
    	include 'simple_html_dom.php';
		$dom = new simple_html_dom();
		$dom->load($response);

        $links = $dom->find('a[plaintext^="Объем торгов в"]');
        $AllInfo = "";
        foreach ($links as $link) {
            $href = "";
            if ($link->href[0] == '/')
                $href = 'https://www.moex.com'.$link->href;
            else
                $href = $link->href;
            if (substr($href,-1) == '/') {
                $href = substr($href,0,-1);
            }
            $Request_Results = $this->sendRequest($href);
            $dom2 = new simple_html_dom();
            $dom2->load($Request_Results);
            $elements = $dom2->find('*[plaintext^="Объем торгов на срочном рынке"]');
            $element_plaintext = $elements[0]->plaintext;

            $this->WriteTxtFile($link->plaintext . ',' . $href, $element_plaintext, "savertxt");
            $pattern = '/\d+,\d+ (млрд|млн|трлн)/';
            preg_match($pattern, $element_plaintext, $matches);
            if (!empty($matches)) {
                $value = $matches[0];
                $element_plaintext = $value;
            } else {
                $element_plaintext = 'Значение не найдено';
            }

            if (strripos($element_plaintext, 'трлн') !== false) {
                $element_plaintext = str_replace('трлн', '', $element_plaintext);
                $element_plaintext = $element_plaintext*1000000000000;
                // 1000000000000 - трлн
            }
            elseif (strripos($element_plaintext, 'млрд') !== false) {
                $element_plaintext = str_replace('млрд', '', $element_plaintext);
                $element_plaintext = $element_plaintext*1000000000;
                // 1000000000 - млрд
            }
            elseif (strripos($element_plaintext, 'млн') !== false) {
                $element_plaintext = str_replace('млн', '', $element_plaintext);
                $element_plaintext = $element_plaintext*1000000;
                // 1000000 - млн
            }

            if (empty($AllInfo)) 
                $AllInfo = $link->plaintext . ';' . $href . ';' . $element_plaintext;
            else
                $AllInfo = $AllInfo.PHP_EOL.$link->plaintext . ';' . $href . ';' . $element_plaintext . " рублей";
        }
        $this->WriteCsvFile($AllInfo, "saver");
    }
    public function WriteTxtFile($namefile, $write, $path)
    {
        if (is_dir($path)) {
            $return_new_path = $path.'\\'.str_replace('/', '_', explode('//', explode('?', $namefile)[0])[1]).".txt";
            file_put_contents($return_new_path, $write, FILE_APPEND);
            return $return_new_path;
        }
        echo "Path not found";
        return null;

    }
    public function WriteCsvFile($write, $path)
    {
        $path_new = $this->CsvSave($write, $path);
        $this->CsvDownload($path_new);
    }
    public function CsvDownload($path)
    {
        $file = fopen($path, 'r');

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="Отчёт.csv"');
        fpassthru($file);
        fclose($file);
    }
    public function CsvSave($write, $path)
    {
        if (is_dir($path)) {
            $return_new_path = $path.'\\'.date("Y-m-d H.i.s").".csv";
            file_put_contents($return_new_path, $write, FILE_APPEND);
            return $return_new_path;
        }
        echo "Path not found";
        return null;
    }

}


?>