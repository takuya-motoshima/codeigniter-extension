<?php
/**
 * Base model class
 *
 * @author     Takuya Motoshima <https://www.facebook.com/takuya.motoshima.7>
 * @license    MIT License
 * @copyright  2017 Takuya Motoshima
 * @property CI_DB_query_builder $db
 */
namespace X\Model;
class AddressModel extends Model {

  /**
   * Get address by post code
   *
   * @return array
   */
  public function getAddressByPostCode(string $postCode):array {
    if (!preg_match('/^\d{3}-?\d{4}$/', $postCode)) {
      return '';
    }
    $postCode = str_replace('-', '', $postCode);
    $addresses = json_decode(file_get_contents(X_APP_PATH . 'Data/address.json'), true);
    if (!isset($addresses[$postCode])) {
      return '';
    }
    return [
      'prefectureCode' => $addresses[$postCode][0],
      'address' => implode(' ', array_slice($addresses[$postCode], 1)),
    ];
  }
}