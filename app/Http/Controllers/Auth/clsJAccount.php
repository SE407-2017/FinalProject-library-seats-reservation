<?php

use Illuminate\Support\Facades\Session;

class JAccountCrypt
{
    /* Function tripleDESEncrypt use 3DES to encrypt a string
    string is encoded to UTF8 first, encypted, then encoded to base64 if encodedForUrl is true
    $src: string, source string to be encrpyted with 3DES
    $key: string in bytes form, 3DES key
    $encodedForUrl: boolean, if src is URL, set $encodedForUrl to true */
    function tripleDESEncrypt($src, $key, $encodedForUrl)
    {
        if ($src == NULL) return NULL;

        $srcb =iconv("GBK", "UTF-8", $src);
        $srcb= $this->PKCS5Padding($srcb);
        /* Encrypt data */
        $encryptedData = $this->tripleDESEncrypt1($srcb,$key);
        if ($encryptedData != NULL) {
            $encryptedData=base64_encode($encryptedData);
            //echo "base64 encoded:" . strlen($encryptedData)."|".bin2hex($encryptedData)."<br>\n";
            if ($encodedForUrl) {
                $encryptedData =urlencode($encryptedData);
            }
        }
        return $encryptedData;
    }

    /* Function tripleDESEncrypt1 use 3DES to encrypt a string
    $src: string, source string to be encrpyted with 3DES
    $key: string in bytes form, 3DES key  */
    function tripleDESEncrypt1($src, $key)
    {
        if ($src == NULL) return NULL;
        $iv =$this->my_mcrypt_create_iv();
        /* Open module, and create IV */
        $td = mcrypt_module_open(MCRYPT_3DES, '', 'cbc', '');
        if ($key == NULL) $key="";
        $key = substr($key, 0, mcrypt_enc_get_key_size($td));

        /* Initialize encryption handle */
        if (mcrypt_generic_init($td, $key, $iv) != -1) {
            /* Encrypt data */
            $encryptedData = mcrypt_generic($td, $src);
            $iv_size=mcrypt_get_iv_size(MCRYPT_3DES, MCRYPT_MODE_CBC);
            $encryptedData=chr($iv_size).$iv. $encryptedData;
            mcrypt_generic_deinit($td);
            //echo "src is:" . $src. "<br>\n";
            //echo "iv is :" .bin2hex($iv)."<br>\n";
            //echo "key is :" .bin2hex($key)."<br>\n";
        }else {
            $encryptedData=null ;
        }
        mcrypt_module_close($td);
        return $encryptedData;
    }


    function tripleDESDecrypt($src, $key)
    {
        if ($src == NULL) return NULL;
        $src=base64_decode($src);
        $decryptedData = $this->tripleDESDecrypt1($src, $key);
        if ($decryptedData != NULL) {
            $decryptedData = iconv("UTF-8", "GBK", $decryptedData);
        }
        return $decryptedData;
    }

    function tripleDESDecrypt1($src, $key)
    {
        if ($src == NULL) return NULL;
        /* Open module, and create IV */
        $td = mcrypt_module_open(MCRYPT_3DES, '', 'cbc', '');
        if ($key == NULL) return NULL;
        $iv_size=ord($src);
        $iv=substr($src,1,$iv_size);
        $key = substr($key, 0, mcrypt_enc_get_key_size($td));

        /* Initialize encryption handle */
        if (mcrypt_generic_init($td, $key, $iv) != -1) {
            /* Encrypt data */
            $srcb=substr($src,$iv_size+1);
            $decryptedData = mdecrypt_generic($td, $srcb);
            mcrypt_generic_deinit($td);
            $decryptedData =$this->PKCS5UnPadding($decryptedData);
        }else {
            $decryptedData=null ;
        }
        mcrypt_module_close($td);

        return $decryptedData;
    }

    function hex2bin($HexStr) {
        return pack('H*', $HexStr);
    }

    /* Windows mycrypt_create _iv has bug, it always return the same value, so
    I create my own */
    function my_mcrypt_create_iv(){
        $size=mcrypt_get_iv_size(MCRYPT_3DES, MCRYPT_MODE_CBC);
        $iv=md5("me".rand().rand().rand());
        $iv=substr($this->hex2bin($iv),0,$size);
        return $iv;
    }

    function PKCS5Padding($Str,$Size=8){
        $strLen=strlen($Str);
        $n=fmod($strLen,$Size);
        $n=$Size-$n;
        return ($Str . str_repeat(chr($n),$n));
    }

    function PKCS5UnPadding($Str,$Size=8){
        $l=strlen($Str);
        $n=ord(substr($Str,-1,1));
        if ($n>$Size) return $Str;
        return substr($Str,0,$l-$n);
    }
}

class JAccountManager
{
    //private java.lang.String uaBaseURL = "http://jaccount.sjtu.edu.cn/jaccount/";
    var $uaBaseURL;
    var $siteID;
    var $hasTicketInURL; //public boolean hasTicketInURL;
    var $userProfile; // Hast Table
    var $elapsedTimestamp; //private static long elapsedTimestamp = 0;
    var $ticketFromServer; //private java.lang.String ticketFromServer;
    var $returnURL; //private java.lang.String returnURL;
    var $siteKey; //private byte[] siteKey = null;

    function __construct($sid, $keyDir) {
        //initialize viarables
        $this->siteID = $sid;
        $this->uaBaseURL="http://jaccount.sjtu.edu.cn/jaccount/";
        //$this->uaBaseURL="http://202.112.26.68:8080/jaccount/";
        //$this->uaBaseURL="http://202.120.2.55/jaccount/";
        $this->hasTicketInURL=false;
        $this->userProfile=NULL;
        if (Session::get('JAM_elapsedTimestamp')!==NULL){
            $this->elapsedTimestamp=Session::get('JAM_elapsedTimestamp');
        }else{
            $this->elapsedTimestamp=0;
            Session::put('JAM_elapsedTimestamp', 0);
        }
        $this->siteKey=$this->readKey($sid, $keyDir);
    }

    function readKey($sid, $keyDir){
        //$sep="\\";
        $sep="/";
        $filename = $keyDir. $sep . $sid. "_desede.key";
        $handle = fopen($filename, "rb");
        $contents = fread($handle, filesize($filename));
        fclose($handle);
        return $contents;
    }

    function checkLogin($returnURL,$ticket){
        if ($this->hasValidTicket($ticket)) {
            $this->setSiteCookie();
            $this->hasTicketInURL = true;
            return $this->userProfile;
        } else {
            if ($this->isSiteCookieValid()) {
                $this->hasTicketInURL = false;
                return $this->userProfile;
            }else {
                if ($this->isSiteCookieValid()) {
                    $this->hasTicketInURL = false;
                    return $this->userProfile;
                }else {
                    $this->hasTicketInURL = false;
                    $scheme = $this->getCurrentScheme();
                    if (isset($_SERVER["HTTP_HOST"])) {
                        $rurl = $scheme. '://'. $_SERVER["HTTP_HOST"];
                    } else {
                        $rurl = $scheme. '://localhost:8081/';
                    }
                    $rurl = $rurl . $returnURL;
                    $JAcc_redirectURL= $this->uaBaseURL . "jalogin?sid=" . $this->siteID . "&returl=" . $this->encrypt($rurl). "&se=" .$this->encrypt(session_id());
                    $this->redirectURL($JAcc_redirectURL) ;
                    return $this->userProfile;
                    return null;
                }
            }
        }
   }

    function decrypt($src) {
        if ($this->siteKey == NULL) return NULL;
        $JAccCrypt=new JAccountCrypt();
        return $JAccCrypt->tripleDESDecrypt($src, $this->siteKey);
    }

    function encrypt($src) {
        if ($this->siteKey == NULL) return NULL;
        $JAccCrypt=new JAccountCrypt();
        return $JAccCrypt->tripleDESEncrypt($src, $this->siteKey, true);
    }

    function getCurrentScheme() {
        return Request::secure() ? "https" : "http";
    }

    //protected boolean hasValidTicket(HttpServletRequest request, HttpSession session)
     function hasValidTicket($ticket){
         // $ticket = Input::get("jatkt");
         if ($ticket == null || strlen($ticket) == 0) return false;
         $decry = $this->decrypt($ticket);
         $this->parseUserProfile($decry);
         //echo $decry;
         //print_r($this->userProfile);
         //exit;
         if ($this->userProfile == NULL) {
             return false;
         }else {
             $ses = $this->userProfile["ja3rdpartySessionID"];
             if ($ses == NULL || !$ses==session_id())    return false;
             $timestamp = $this->userProfile["jaThisLoginTime"];
             if ($this->isTimestampValid($timestamp)) {
                $this->ticketFromServer = $ticket;
                $this->returnURL = $this->userProfile["jaReturnUrl"];
                return true;
              }else {
                  return false;
              }
        }
    }

     //protected boolean isSiteCookieValid(HttpServletRequest request)
     function isSiteCookieValid(){
         if (NULL !== Cookie::get('JASiteCookie')) {
             $siteCookieValue = $_COOKIE["JASiteCookie"];
             $siteCookieValue = $this->decrypt($siteCookieValue);
             if ($siteCookieValue == NULL) return false;
             $this->parseUserProfile($siteCookieValue);
             if ($this->userProfile == NULL) return false;
             return true;
         }else {
             return false;
         }
     }

     //protected synchronized static boolean isTimestampValid(Long timestamp)
     function isTimestampValid($timestamp){
         $ts = $timestamp/1000;
         $valid = false;
         if ($ts > $this->elapsedTimestamp) {
             $valid = true;
             $this->elapsedTimestamp = $ts;
             $_SESSION['JAM_elapsedTimestamp']=$ts;
             //echo $_session['JAM_elapsedTimestamp'] .'<>' . $ts ;
             //exit;
         }
         return $valid;
     }

     //public boolean logout(HttpServletRequest request, HttpServletResponse response, String returnURL)
     function logout($returnURL){
        /*
         $lo = $_GET["logout"];
         if ($lo != NULL && $lo=="1") {
             return true;
         }
         */
         setcookie("JASiteCookie", "", 0, "/");
         $scheme="https";
         if ($_SERVER["HTTPS"]="off") $scheme="http";
         $rurl = $scheme. '://'. $_SERVER["HTTP_HOST"];
         $rurl = $rurl . $returnURL;         
         $JAcc_redirectURL=$this->uaBaseURL . "ulogout?sid=" . $this->siteID . "&returl=" .$this->encrypt($rurl);
         $this->redirectURL($JAcc_redirectURL) ;
         return false;
     }

     //protected Hashtable parseUserProfile($ticket)
     function parseUserProfile($ticket){
         $t=chr(10);
         $userProf1=explode($t,$ticket);
         $sep=1;
         foreach($userProf1 as $value) {
             $pos=strpos($value,"=");
             if (!$pos === false){
                 $name=substr($value,0,$pos);
                 $name_value= substr($value,$pos+1);
                 if (($pos==0) Or ($sep==1 && !($name=="jaThisLoginTime"))Or($sep==2 && !($name=="jaLastLoginTime"))) {
                     unset($this->userProfile);
                     $this->userProfile=NULL;
                     break;
                 }else{
                     $this->userProfile[$name]=$name_value;
                 }
             }
             $sep++;
         }
     }

     //public void redirectWithoutTicket(HttpServletResponse response)
     function redirectWithoutTicket(){
         $this->redirectURL($this->returnURL);
         //header("Location: ".$this->returnURL);
         /* Make sure that code below does not get executed when we redirect. */
         exit;
     }

     //protected void setSiteCookie(HttpServletResponse response)
     function setSiteCookie(){
         setcookie("JASiteCookie", $this->ticketFromServer, 0, "/");
     }

    function redirectURL($redirectURL){
        echo "<script language='javascript' type='text/javascript'>";
        echo "window.location.href='$redirectURL'";
        echo "</script>";
    }
}

class JaHelper {
    public static function jalogin($ticket,$url){

         function converthex2bin($hexstr){
             return pack('H*',$hexstr);
         }

         $jam = new JAccountManager('japroposal20140313', app_path().'/jaccount');
         $returnURL = $url;
         $ht = $jam->checkLogin($returnURL,$ticket);

/*         if (($ht != NULL ) && ($jam->hasTicketInURL)){
                  $jam->redirectWithoutTicket();
         }*/
         
         $displayform = False;
         if(isset($_POST['Random_iv']) && ($_POST['Random_iv'] == '343243abdecf3a7e')){
         $src = $_POST['Name'].'@'.$_POST['Domain'];
         $encrypted = $jam->encrypt($src);
         $decrypted = $jam->decrypt(urldecode($encrypted));
         }else{
            $displayform = True;
         }
         return $ht;
    }

    public static function jalogout($returnURL){

         function converthex2bin($hexstr){
             return pack('H*',$hexstr);
         }

         $jam = new JAccountManager('japroposal20140313', app_path().'/jaccount');
         $ht = $jam->logout($returnURL);

    }
}

?>
