<?php
/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Enamoo S5 to newer
 * versions in the future.
 *
 * @copyright Copyright (c) 2015 GodoSoft.
 * @link http://www.godo.co.kr
 */

namespace Bundle\Service;

use Framework\StaticProxy\Proxy\Encryptor;
use Framework\Utility\ArrayUtils;
use Globals;

class GDUtils
{
    /**
     * DB객체 리턴
     *
     * @static
     * @return object
     */
    public static function getDB()
    {
        $db = \App::load('DB');
        return $db->dbConnection();
    }

    /**
     * 암호화
     *
     * @static
     * @param $password
     * @return boolean
     */
    public static function encodePassword($password)
    {
        return \App::getInstance('password')->hash($password);
    }

    /**
     * 비밀번호 체크
     *
     * @static
     * @param 비교할 비밀번호(비암호화 상태)
     * @param 비교대상 비밀번호(암호화 상태)
     * @return boolean
     */
    public static function verifyPassword($inputPassword, $targetPassword)
    {
        return \App::getInstance('password')->verify($inputPassword, $targetPassword);
    }

    /**
     * Returns the decrypted string.
     *
     * @param string $value
     * @param string $salt
     *
     * @return mixed
     */
    public static function decrypt($value , $salt = '')
    {
        return Encryptor::decrypt($value,$salt);
    }

    /**
     * Returns the encrypted string.
     *
     * @param mixed $value
     * @param string $salt
     *
     * @return string
     */
    public static function encrypt($value, $salt = '')
    {
        return Encryptor::encrypt($value,$salt);
    }
}
