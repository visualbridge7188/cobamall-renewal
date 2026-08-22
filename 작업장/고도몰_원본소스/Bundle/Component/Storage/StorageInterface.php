<?php
/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link http://www.godo.co.kr
 */

namespace Bundle\Component\Storage;


interface StorageInterface
{
    public function isFile($filePath);

    /**
     * @param string|null $filePath
     *
     * @return string
     */
    public function getHttpPath(?string $filePath = null): string;

    public function getFilename($filePath);

    public function getMountPath($filePath);

    /**
     * @param string|null $filePath
     *
     * @return string
     */
    public function getRealPath(?string $filePath = null): string;

    public function getDownLoadPath($filePath);

    public function download($filePath , $downloadFilename);

}
