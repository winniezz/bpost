<?php
namespace Bpost;

require_once __DIR__ . '/../../../../../autoload.php';

use TijsVerkoyen\Bpost\Bpost\Order\Customer;
use TijsVerkoyen\Bpost\Bpost\Order\Address;

class CustomerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests Customer->toXMLArray
     */
    public function testToXMLArray()
    {
        $data = array(
            'name' => 'Tijs Verkoyen',
            'company' => 'Sumo Coders',
            'address' => array(
                'streetName' => 'Afrikalaan',
                'number' => '289',
                'box' => '3',
                'postalCode' => '9000',
                'locality' => 'Gent',
                'countryCode' => 'BE',
            ),
            'emailAddress' => 'bpost@verkoyen.eu',
            'phoneNumber' => '+32 9 395 02 51',
        );

        $address = new Address(
            $data['address']['streetName'],
            $data['address']['number'],
            $data['address']['box'],
            $data['address']['postalCode'],
            $data['address']['locality'],
            $data['address']['countryCode']
        );

        $customer = new Customer();
        $customer->setName($data['name']);
        $customer->setCompany($data['company']);
        $customer->setAddress($address);
        $customer->setEmailAddress($data['emailAddress']);
        $customer->setPhoneNumber($data['phoneNumber']);

        $xmlArray = $customer->toXMLArray();

        $this->assertEquals($data, $xmlArray);
    }

    /**
     * Test validation in the setters
     */
    public function testFaultyProperties()
    {
        $customer = new Customer();

        try {
            $customer->setEmailAddress(str_repeat('a', 51));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals('Invalid length, maximum is 50.', $e->getMessage());
        }

        try {
            $customer->setPhoneNumber(str_repeat('a', 21));
        } catch (\Exception $e) {
            $this->assertInstanceOf('TijsVerkoyen\Bpost\Exception', $e);
            $this->assertEquals('Invalid length, maximum is 20.', $e->getMessage());
        }
    }
}