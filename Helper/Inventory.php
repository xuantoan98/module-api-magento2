<?php
namespace AHT\Blog\Helper;
 
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\HTTP\Client\Curl;
 
class Inventory extends AbstractHelper
{
    public function __construct()
    {

    }
    public function api($url)
    {
        $token = "vmw9ulabc7ypi38qpquiwk5z8ajc9a4m";

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));

        $result = curl_exec($ch);

        $result = json_decode($result);

        return $result;
    }
}