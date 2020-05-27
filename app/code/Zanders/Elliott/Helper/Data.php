<?php
/**
 * @category   Zanders
 * @package    Zanders_Elliott
 */

namespace Zanders\Elliott\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\Stdlib\StringUtils;

class Data extends AbstractHelper
{
    protected $_shipViaList = array(
        'BW' => 'Best Way',
        'UG' => 'UPS Ground',
        'U1' => 'UPS Next Day',
        'U2' => 'UPS 2nd Day',
        "1S" => 'UPS Next Day Saturday',
        "2S" => 'UPS 2nd Day Saturday',
        "BG" => 'Buds',
        "BR" => 'Browning Export',
        "C1" => 'CTD UPS 1 AIR',
        "C2" => 'CTD UPS 2',
        "C3" => 'CTD UPS 3',
        "CG" => 'CTD UPS',
        "CM" => 'CTD',
        "CS" => 'CTD UPS 1 SAV',
        "F1" => 'FedEx Next Day',
        "F2" => 'FedEx 2nd Day',
        "F3" => 'FedEx Xpress Saver',
        "FG" => 'FedEx Ground',
        "FH" => 'FedEx Ground Home',
        "FX" => 'FedEx Xpress',
        "MA" => 'UPS Mail',
        "SP" => 'Spee-Dee',
        "TR" => 'Truck Line',
        "U3" => 'UPS 3 Day Select',
        "UM" => 'UPS SurePost',
        "US" => 'UPS Next Day Air Saver',
        "PU" => 'Pick Up'
    );

    /**
     * @var PricingHelper
     */
    protected $pricingHelper;

    /**
     * @var StringUtils
     */
    protected $stringUtility;

    /**
     * @param Context $context
     * @param PricingHelper $pricingHelper
     * @param StringUtils $stringUtility
     */
    public function __construct(
        Context $context,
        PricingHelper $pricingHelper,
        StringUtils $stringUtility
    )
    {
        $this->pricingHelper = $pricingHelper;
        $this->stringUtility = $stringUtility;
    }

    public function getTrackingFromNotes($notes)
    {
        $tnumbers = array();
        $tracknotes = array();
        foreach ($notes->Notes as $note) {
            $tracknotes[] = trim($note->NOTE_CONTENT_1);
            $tracknotes[] = trim($note->NOTE_CONTENT_2);
            $tracknotes[] = trim($note->NOTE_CONTENT_3);
            $tracknotes[] = trim($note->NOTE_CONTENT_4);
            $tracknotes[] = trim($note->NOTE_CONTENT_5);
            $tracknotes[] = trim($note->NOTE_CONTENT_6);
            $tracknotes[] = trim($note->NOTE_CONTENT_7);
            $tracknotes[] = trim($note->NOTE_CONTENT_8);
            $tracknotes[] = trim($note->NOTE_CONTENT_9);
            $tracknotes[] = trim($note->NOTE_CONTENT_10);
        }

        $tinfo = array();
        $i = 0;
        foreach ($tracknotes as $line) {
            $info = explode(':', str_replace(array('�', '�'), array('', ''), $line));
            if (trim($info[0]) == 'ShipVia') {
                if (isset($tinfo[$i]['shipvia'])) {
                    $i++;
                }
                $via = explode(' ', trim($info[1]));
                $tinfo[$i]['shipvia'] = trim($info[1]);
                $tinfo[$i]['viacode'] = strtolower($via[0]);
            }
            if (trim($info[0]) == 'Track #') {
                $tinfo[$i]['tnumber'] = trim($info[1]);
            }
            if (trim($info[0]) == 'Weight') {
                $tinfo[$i]['weight'] = trim($info[1]);
            }
            // Added This For Date
            if (trim($info[0]) == 'Ship Date') {
                $theshipdate = trim($info[1]);
                $theshipdatearray = explode("/", $theshipdate);
                $tinfo[$i]['shipdate'] = $theshipdatearray[2] . str_pad($theshipdatearray[0], 2, "0", STR_PAD_LEFT) . str_pad($theshipdatearray[1], 2, "0", STR_PAD_LEFT);
            }
            // End of New Date Add
        }

        foreach ($tinfo as $id => $trackinginfo) {
            if ($trackinginfo['viacode'] == 'ups') {
                $url = "http://wwwapps.ups.com/WebTracking/processInputRequest?HTMLVersion=5.0&loc=en_US&Requester=UPSHome&tracknum=" . $trackinginfo['tnumber'] . "+&AgreeToTermsAndConditions=yes";
            }
            if ($trackinginfo['viacode'] == 'fdx') {
                $url = "https://www.fedex.com/apps/fedextrack/?tracknumbers=" . $trackinginfo['tnumber'];
            }
            if ($trackinginfo['viacode'] == 'fedex') {
                $url = "https://www.fedex.com/apps/fedextrack/?tracknumbers=" . $trackinginfo['tnumber'];
            }
            if ($trackinginfo['viacode'] == 'usps') {
                $url = "https://tools.usps.com/go/TrackConfirmAction!execute.action?formattedLabel=" . $trackinginfo['tnumber'];
            }
            if ($trackinginfo['viacode'] == 'mail') {
                $url = "https://tools.usps.com/go/TrackConfirmAction!execute.action?formattedLabel=" . $trackinginfo['tnumber'];
            }
            if ($trackinginfo['viacode'] == 'spee-dee') {
                $url = "http://packages.speedeedelivery.com/packages.asp?tracking=" . $trackinginfo['tnumber'];
            }
            if ($trackinginfo['viacode'] == 'best' || !isset($url)) {
                $start = substr($trackinginfo['tnumber'], 0, 2);
                if ($start == '1Z') {
                    $url = "http://wwwapps.ups.com/WebTracking/processInputRequest?HTMLVersion=5.0&loc=en_US&Requester=UPSHome&tracknum=" . $trackinginfo['tnumber'] . "+&AgreeToTermsAndConditions=yes";
                }
                if ($start == '94') {
                    $url = "https://tools.usps.com/go/TrackConfirmAction!execute.action?formattedLabel=" . $trackinginfo['tnumber'];
                }
                if ($start == '01') {
                    $url = "https://www.fedex.com/apps/fedextrack/?tracknumbers=" . $trackinginfo['tnumber'];
                }
                if ($start == 'SP') {
                    $url = "http://packages.speedeedelivery.com/packages.asp?tracking=" . $trackinginfo['tnumber'];
                }
            }
            if (!is_null($trackinginfo['shipvia']) && !is_null($trackinginfo['tnumber']) && !is_null($trackinginfo['viacode'])) {
                $tracking = array(
                    'shipCompany' => $trackinginfo['viacode'],
                    'shipVia' => $trackinginfo['shipvia'],
                    'trackingNumber' => $trackinginfo['tnumber'],
                    'weight' => $trackinginfo['weight'],
                    'url' => $url,
                    'shipDate' => $trackinginfo['shipdate'] //New For Ship Date
                );
                $tnumbers[] = $tracking;
            }
        }
        return $tnumbers;
    }

    public function getShippingMethod($code)
    {
        if (array_key_exists($code, $this->_shipViaList)) {
            return $this->_shipViaList[$code];
        }
        return $this->_shipViaList['BW'];
    }

    public function formatPrice($amount, $format = true, $includeContainer = true)
    {
        return $this->pricingHelper->currency($amount, $format, $includeContainer);
    }

    public function splitInjection($str, $length = 50, $needle = '-', $insert = ' ')
    {
        return $this->stringUtility->splitInjection($str, $length, $needle, $insert);
    }

    public function validateFFLUploadScript()
    {
        return '<script type="text/javascript">
                function Filevalidation()
                {
                      var _validFileExtensions = ["jpg", "jpeg", "pdf", "gif", "png","tif","tiff"];
                      var fileName = document.querySelector("#fflfile").value;
                      var extension = fileName.substring(fileName.lastIndexOf(".") + 1);
                      extension = extension.toLowerCase();
                      if(_validFileExtensions.indexOf(extension) > -1)
                      {
                            var fdata = document.querySelector("#fflfile");
                            var maxSize = 1024 * 10;
                            if (fdata.files && fdata.files[0])
                            {
                                var fsize = fdata.files[0].size/1024;
                                if(fsize > maxSize)
                                {
                                    alert("Maximum file size exceed");
                                    return false;
                                }
                            }
                             document.getElementById("uploadfflfile").submit();
                      }
                      else
                      {
                          alert("Invalid File format.");
                      }
                }
        </script>';
    }
}
