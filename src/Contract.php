<?php

namespace AP\Docs;

/**
 * Interface Contract
 * @package AP\Docs
 */
interface Contract
{
    /**
     * @param $vendor
     * @param null $version
     * @return mixed
     */
    public function getIndex($vendor, $version = null);

    /**
     * @param $vendor
     * @param null $version
     * @return mixed
     */
    public function getDefaultPage($vendor, $version = null);

    /**
     * @return mixed
     */
    public function getVendors();

    /**
     * @param $vendor
     * @return mixed
     */
    public function getVersions($vendor);

    /**
     * @param $vendor
     * @param $version
     * @return mixed
     */
    public function getPages($vendor, $version, $onlyList = false);

    /**
     * @param $vendor
     * @param $version
     * @param $page
     * @return mixed
     */
    public function getPage($vendor, $version, $page);

    /**
     * @param $file
     * @return mixed
     */
    public function getExtension($file);

    /**
     * @return mixed
     */
    public function canGuessExtension();

    /**
     * @param $file
     * @return mixed
     */
    public function guessExtension($file);

    /**
     * @return mixed
     */
    public function allowedExtensions();

    /**
     * @return mixed
     */
    public function willFilterExtensions();

    /**
     * @return mixed
     */
    public function excludedExtensions();

    /**
     * @param $file
     * @return mixed
     */
    public function isFile($file);
}