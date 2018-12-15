<?php

class MySeleniumSuite extends PHPUnit_Extensions_Selenium2TestCase {

    public function setUp(){


        $this -> configHost = require __DIR__ . "/config/host.php";
        $this->setBrowser("chrome");
        $this -> configPageUrl = require __DIR__ . "/config/pageUrl.php";
        $this -> configEnvironment = require __DIR__ . "/config/environment.php";
        $this -> configInjection = require __DIR__ . "/config/injection.php";
        $this -> configUserAgent = require __DIR__ . "/config/userAgent.php";
        $this -> configWindowSize = require __DIR__ . "/config/windowSize.php";
        $this -> templateHeader = require __DIR__ . "/template/bootstrap.php";
        $this -> setHost($this -> configHost["host"]);
        $this -> setPort($this -> configHost["port"]);
        $this -> setBrowserUrl($this -> configEnvironment["cpo-production"]);
        // $this->setSeleniumServerRequestsTimeout(10);
        $this->assertTrue(true);
        $windowSize = $this -> configWindowSize["Desktop"];
        $userAgent = $this -> configUserAgent["Desktop"];
        $chromeOptionsArr = array(

            "args" => array(
                '--headless',
                "--window-size=$windowSize",
                "--user-agent=$userAgent",
            ),
        );
        $param = array(
            "acceptInsecureCerts" => true,
            "chromeOptions" => $chromeOptionsArr,
            "goog:chromeOptions" => $chromeOptionsArr,
        );

        $this->setDesiredCapabilities($param);
        $this -> filename = __DIR__ . "/report/cpoPciRemediation-result.html";

        $this -> fp = fopen($this -> filename, 'w');
        $data = 'Environment: ' . $this -> configEnvironment["cpo-production"] .$this -> templateHeader["sql-header"];
        fwrite($this->fp, $data);
    }

    public function testCart()
   {
      $this->pass = 0;
      $this->fail = 0;
      $this->validateForms();
      $data = '</tbody></table>
                  </div>
              </div>
              <div class="card-footer">
              <table><tr>
              <td class="result">Total number of PASSED: <b>' . $this->pass. '</b></td><td> Total number of FAILED: <b>' . $this->fail . '</b></td>
              </tr></table></div>
          </div>
      </div>

      <!-- Bootstrap core JavaScript-->
      <script src="vendor/jquery/jquery.min.js"></script>
      <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
      <!-- Core plugin JavaScript-->
      <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
      <!-- Page level plugin JavaScript-->
      <script src="vendor/datatables/jquery.dataTables.js"></script>
      <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
      <!-- Custom scripts for all pages-->
      <script src="js/sb-admin.min.js"></script>
      <!-- Custom scripts for this page-->
      <script src="js/sb-admin-datatables.min.js"></script>
      </body>
      </html>';
      fwrite($this->fp, $data);
      fclose($this->fp);
   }

   public function validateForms(){
       // $this->url("/");
       // $this->cookie()->clear();
       $this->additem();

   }

public function additem(){
  $url = $this -> configPageUrl["cpo-additem"];
  $inject = $this -> configInjection["cpo-code-injection"];
  $fields = array(
  "qty" =>  rawurlencode("1"),
  "year" =>  rawurlencode($inject),
  "make" =>  rawurlencode("Acura"),
  "model" =>  rawurlencode("MDX"),
  "submodel" =>  rawurlencode("Base"),
  "engine" =>  rawurlencode("6 Cyl 3.7L"),
  "p_id" =>  rawurlencode("koolvueac24er-spassengerside"),
  "srcPageAtc" => rawurlencode("Product Listing Page"),
  );
  $content = $this->loadCurlHeader($url ,$fields , "-");
  // print_r($fields);
  echo "\n\nValidate Additem Vulnerability - CPO Remediation";
  $formName = "additem";
  $this->validateStatusCode($url,$content,$formName);

}

 public function validateStatusCode($url,$content,$formName){

   $content[0] = "http_status: " . $content[0];
   $temp = explode("\n", $content[0]);

   $tmpArr = array();
   $httpStatus = str_replace("http_status: ", "", "$temp[0]");
   $scenario = "[CPO] Validate PCI Remediation ".$formName;
   $expected = "true";

   // if(strcasecmp($httpStatus,"HTTP/1.1 200 OK") == 0){
   $statusCompare = (!strpos($httpStatus, "Forbidden") && !strpos($httpStatus, "Not Found"));
  if ($statusCompare !== false) {
     // $timestamp = strtotime('now');
     // $screenshotFile = $timestamp . ".png";
     // $this -> createScreenshot($screenshotFile);
     echo "\nHTTP Status- PASSED - " . $httpStatus;
     echo "\nURL: " . $url;
     $actual = "true";
     $this->writeReport($scenario, $expected, $actual,$httpStatus,$formName);
     // return $httpStatus;
     // $this->back();
     $this->pass++;

   }else{
     echo "\nHTTP Status- FAILED - " . $httpStatus;
     echo "\nURL: " . $url;
     $actual = "false";
     $this->writeReport($scenario, $expected, $actual,$httpStatus);
     $this->fail++;
   }

 }
 public function writeReport($scenario, $expected, $actual,$httpStatus) {
     if (is_bool($expected) and ( $expected == true)) {
         $expected_text = "HTTP/1.1 200 OK";
     } elseif (is_bool($expected) and ( $expected == false)) {
         $expected_text = "false";
     } else {
         $expected_text = "HTTP/1.1 200 OK";
     }

     if (is_bool($actual) && ($actual == true)) {
         $actual_text = $httpStatus;
     } elseif (is_bool($actual) && ($actual == false)) {
         $actual_text = $httpStatus;
     } else {
         $actual_text = $httpStatus;
     }
     if ($expected == $actual) {
         $status = "Passed";
         $color = "#629632";
     } else {
         $status = "Failed";
         $color = "#FF0000";
     }
     $data = '<tr>
                            <td>' . $scenario . '</td>
                            <td>' . $expected_text . '</td>
                            <td>' . $actual_text . '</td>
                            <td><b><font color =' . $color . '>' . $status . '</font></b></td>
                          </tr>';

     fwrite($this->fp, $data);
 }

 // public function onNotSuccessfulTest(Throwable $e) {
 //
 //     $this -> createScreenshot("thereIsError.png");
 //     echo $e -> getMessage() . "\n\n";
 //     echo $e -> getTraceAsString();
 // }
 //
 // public function createScreenshot($fileName = "fileNameNotSet.png") {
 //     $screenshotDir = __DIR__ . "/screenshots/";
 //     $base64 = base64_decode($this -> screenshot());
 //     file_put_contents($screenshotDir . $fileName, $base64);
 // }
 public function loadCurlHeader($url,$fields) {


$fields_string = "";

if(!empty($fields)){
foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string, '&');
}
// echo "\n\n" .$fields_string;
$fields_string = "?" . $fields_string;
echo "\n\n" .$fields_string;
$ch = curl_init();
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
      curl_setopt($ch, CURLOPT_URL, $url . $fields_string);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:15.0) Gecko/20100101 Firefox/58.0.1 usap_selenium');
      // curl_setopt($ch,CURLOPT_POST, count($fields));
      // curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  //    curl_setopt($ch, CURLOPT_SSLVERSION, 3);
      curl_setopt($ch, CURLOPT_HEADER, true);
      //curl_setopt($ch, CURLOPT_NOBODY, true);
      $content = curl_exec($ch);
      curl_close($ch);
     //echo "raw: \n"; print_r($content); echo "\n";
     $arrRequests = explode("\r\n\r\n", $content);
     $headers = array();
     $headers = $arrRequests;
     // print_r($headers);
     return $headers;
 }

}

?>
