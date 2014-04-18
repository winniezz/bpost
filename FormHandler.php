<?php
namespace TijsVerkoyen\Bpost;

/**
 * bPost Form handler class
 *
 * @author Tijs Verkoyen <php-bpost@verkoyen.eu>
 */
class FormHandler
{
    /**
     * bPost instance
     *
     * @var Bpost
     */
    private $bpost;

    /**
     * The parameters
     *
     * @var array
     */
    private $parameters = array();

    /**
     * Create bPostFormHandler instance
     *
     * @param string $accountId
     * @param string $passPhrase
     */
    public function __construct($accountId, $passPhrase)
    {
        $this->bpost = new Bpost($accountId, $passPhrase);
    }

    /**
     * Calculate the hash
     *
     * @return string
     */
    private function getChecksum()
    {
        // init vars
        $keysToHash = array(
            'accountId', 'action', 'costCenter', 'customerCountry',
            'deliveryMethodOverrides', 'extraSecure', 'orderReference'
        );
        $base = 'accountId=' . $this->bpost->getAccountId() . '&';

        // loop keys
        foreach ($keysToHash as $key) {
            if (isset($this->parameters[$key])) {
                $base .= $key . '=' . $this->parameters[$key] . '&';
            }
        }

        // add passhphrase
        $base .= $this->bpost->getPassPhrase();

        // return the hash
        return hash('sha256', $base);
    }

    /**
     * Get the parameters
     *
     * @param  bool  $form
     * @param  bool  $includeChecksum
     * @return array
     */
    public function getParameters($form = false, $includeChecksum = true)
    {
        $return = $this->parameters;

        if ($form && isset($return['orderLine'])) {
            foreach ($return['orderLine'] as $key => $value) {
                $return['orderLine[' . $key . ']'] = $value;
            }

            unset($return['orderLine']);
        }

        if ($includeChecksum) {
            $return['accountId'] = $this->bpost->getAccountId();
            $return['checksum'] = $this->getChecksum();
        }

        return $return;
    }

    /**
     * Set a parameter
     *
     * @param string $key
     * @param mixed  $value
     */
    public function setParameter($key, $value)
    {
        switch ((string) $key) {
            // limited values
            case 'action':
            case 'lang':
                $allowedValues['action'] = array('START', 'CONFIRM');
                $allowedValues['lang'] = array('NL', 'FR', 'EN', 'DE', 'Default');

                if (!in_array($value, $allowedValues[$key])) {
                    throw new Exception(
                        'Invalid value (' . $value . ') for ' . $key . ', allowed values are: ' .
                        implode(', ', $allowedValues[$key]) . '.'
                    );
                }
                $this->parameters[$key] = $value;
                break;

            // maximum 2 chars
            case 'customerCountry':
                if (mb_strlen($value) > 2) {
                    throw new Exception(
                        'Invalid length for ' . $key . ', maximum is 2.'
                    );
                }
                $this->parameters[$key] = (string) $value;
                break;

            // maximum 8 chars
            case 'customerStreetNumber':
            case 'customerBox':
                if (mb_strlen($value) > 8) {
                    throw new Exception(
                        'Invalid length for ' . $key . ', maximum is 8.'
                    );
                }
                $this->parameters[$key] = (string) $value;
                break;

            // maximum 20 chars
            case 'customerPhoneNumber':
                if (mb_strlen($value) > 20) {
                    throw new Exception(
                        'Invalid length for ' . $key . ', maximum is 20.'
                    );
                }
                $this->parameters[$key] = (string) $value;
                break;

            // maximum 32 chars
            case 'customerPostalCode':
                if (mb_strlen($value) > 32) {
                    throw new Exception(
                        'Invalid length for ' . $key . ', maximum is 32.'
                    );
                }
                $this->parameters[$key] = (string) $value;
                break;

            // maximum 40 chars
            case 'customerFirstName':
            case 'customerLastName':
            case 'customerStreet':
            case 'customerCity':
                if (mb_strlen($value) > 40) {
                    throw new Exception(
                        'Invalid length for ' . $key . ', maximum is 40.'
                    );
                }
                $this->parameters[$key] = (string) $value;
                break;

            // maximum 50 chars
            case 'orderReference':
            case 'costCenter':
            case 'customerEmail':
                if (mb_strlen($value) > 50) {
                    throw new Exception(
                        'Invalid length for ' . $key . ', maximum is 50.'
                    );
                }
                $this->parameters[$key] = (string) $value;
                break;

            // integers
            case 'orderTotalPrice':
            case 'orderWeight':
                $this->parameters[$key] = (int) $value;
                break;

            // array
            case 'orderLine':
                if(!isset($this->parameters[$key])) $this->parameters[$key] = array();
                $this->parameters[$key][] = $value;
                break;

            default:
                $this->parameters[$key] = $value;
        }
    }
}