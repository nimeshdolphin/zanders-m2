<?php
/**
 * @category   Zanders
 * @package    Zanders_Manufacturer
 */

namespace Zanders\Manufacturer\Api\Data;

/**
 * CMS page interface.
 * @api
 */
interface ManufacturerInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const MANUFACTURER_ID = 'id';
    const NAME = 'name';
    const ADDRESS = 'address';
    const SERIALIZED_DISPLAY_ON = 'serialized_display_on';
    const SERIALIZED_TEXT = 'serialized_text';
    const PHONE = 'phone';
    const WEB = 'web';
    const IMAGE = 'image_type';
    const STATUS = 'enable';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Zanders\Manufacturer\Api\Data\ManufacturerInterface
     */
    public function setId($id);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return \Zanders\Manufacturer\Api\Data\ManufacturerInterface
     */
    public function setName($name);

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress();

    /**
     * Set address
     *
     * @param string $address
     * @return \Zanders\Manufacturer\Api\Data\ManufacturerInterface
     */
    public function setAddress($address);

    /**
     * Get serialized display on
     *
     * @return string
     */
    public function getSerializedDisplayOn();

    /**
     * Set serialized display on
     *
     * @param string $serialized_display_on
     * @return \Zanders\Manufacturer\Api\Data\ManufacturerInterface
     */
    public function setSerializedDisplayOn($serialized_display_on);

    /**
     * Get serialized text
     *
     * @return string
     */
    public function getSerializedText();

    /**
     * Set serialized text
     *
     * @param string $serialized_text
     * @return \Zanders\Manufacturer\Api\Data\ManufacturerInterface
     */
    public function setSerializedText($serialized_text);

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone();

    /**
     * Set phone
     *
     * @param string $phone
     * @return \Zanders\Manufacturer\Api\Data\ManufacturerInterface
     */
    public function setPhone($phone);

    /**
     * Get website
     *
     * @return string
     */
    public function getWeb();

    /**
     * Set website
     *
     * @param string $web
     * @return \Zanders\Manufacturer\Api\Data\ManufacturerInterface
     */
    public function setWeb($web);

    /**
     * Get image
     *
     * @return string
     */
    public function getImage();

    /**
     * Set image
     *
     * @param string $image
     * @return \Zanders\Manufacturer\Api\Data\ManufacturerInterface
     */
    public function setImage($image);

    /**
     * Get Status
     *
     * @return bool
     */
    public function getStatus();

    /**
     * Set Status
     *
     * @param int|bool $status
     * @return \Zanders\Manufacturer\Api\Data\ManufacturerInterface
     */
    public function setStatus($status);
}
