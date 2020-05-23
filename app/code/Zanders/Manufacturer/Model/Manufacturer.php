<?php
/**
 * @category   Zanders
 * @package    Zanders_Manufacturer
 */

namespace Zanders\Manufacturer\Model;

use Magento\Framework\Model\AbstractModel;
use Zanders\Manufacturer\Api\Data\ManufacturerInterface;

class Manufacturer extends AbstractModel implements ManufacturerInterface {
	/**
	 * Define resource model
	 */
	protected function _construct() {
		$this->_init('Zanders\Manufacturer\Model\ResourceModel\Manufacturer');
	}

	/**
	 * Get ID
	 *
	 * @return int
	 */
	public function getId() {
		return parent::getData(self::MANUFACTURER_ID);
	}

	/**
	 * Set ID
	 *
	 * @param int $id
	 * @return \Zanders\Manufacturer\Api\Data\ManufacturerInterface
	 */
	public function setId($id) {
		return $this->setData(self::MANUFACTURER_ID, $id);
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->getData(self::NAME);
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 * @return \Zanders\Manufacturer\Api\Data\ManufacturerInterface
	 */
	public function setName($name) {
		return $this->setData(self::NAME, $name);
	}

	/**
	 * Get address
	 *
	 * @return string
	 */
	public function getAddress() {
		return $this->getData(self::ADDRESS);
	}

	/**
	 * Set address
	 *
	 * @param string $address
	 * @return \Zanders\Manufacturer\Api\Data\ManufacturerInterface
	 */
	public function setAddress($address) {
		return $this->setData(self::ADDRESS, $address);
	}

	/**
	 * Get serialized display on
	 *
	 * @return string
	 */
	public function getSerializedDisplayOn() {
		return $this->getData(self::SERIALIZED_DISPLAY_ON);
	}

	/**
	 * Set serialized display on
	 *
	 * @param string $serialized_display_on
	 * @return \Zanders\Manufacturer\Api\Data\ManufacturerInterface
	 */
	public function setSerializedDisplayOn($serialized_display_on) {
		return $this->setData(self::SERIALIZED_DISPLAY_ON, $serialized_display_on);
	}

	/**
	 * Get serialized text
	 *
	 * @return string
	 */
	public function getSerializedText() {
		return $this->getData(self::SERIALIZED_TEXT);
	}

	/**
	 * Set serialized text
	 *
	 * @param string $serialized_text
	 * @return \Zanders\Manufacturer\Api\Data\ManufacturerInterface
	 */
	public function setSerializedText($serialized_text) {
		return $this->setData(self::SERIALIZED_TEXT, $serialized_text);
	}

	/**
	 * Get phone
	 *
	 * @return string
	 */
	public function getPhone() {
		return $this->getData(self::PHONE);
	}

	/**
	 * Set phone
	 *
	 * @param string $phone
	 * @return \Zanders\Manufacturer\Api\Data\ManufacturerInterface
	 */
	public function setPhone($phone) {
		return $this->setData(self::PHONE, $phone);
	}

	/**
	 * Get website
	 *
	 * @return string
	 */
	public function getWeb() {
		return $this->getData(self::WEB);
	}

	/**
	 * Set website
	 *
	 * @param string $web
	 * @return \Zanders\Manufacturer\Api\Data\ManufacturerInterface
	 */
	public function setWeb($web) {
		return $this->setData(self::WEB, $web);
	}

	/**
	 * Get image
	 *
	 * @return string
	 */
	public function getImage() {
		return $this->getData(self::IMAGE);
	}

	/**
	 * Set image
	 *
	 * @param string $image
	 * @return \Zanders\Manufacturer\Api\Data\ManufacturerInterface
	 */
	public function setImage($image) {
		return $this->setData(self::IMAGE, $image);
	}

	/**
	 * Get status
	 *
	 * @return bool
	 */
	public function getEnable() {
		return (bool) $this->getData(self::STATUS);
	}

	/**
	 * Set status
	 *
	 * @param int|bool $status
	 * @return \Zanders\Manufacturer\Api\Data\ManufacturerInterface
	 */
	public function setEnable($status) {
		return $this->setData(self::STATUS, $status);
	}
}
