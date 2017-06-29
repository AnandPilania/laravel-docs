<?php

namespace AP\Docs;

use Route;
use ParsedownExtra;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

/**
 * Class Repository
 * @package AP\Docs
 */
class Repository implements Contract
{
    /**
     * @var
     */
    protected $disk;

    /**
     * @var bool
     */
    protected $config;

    /**
     * Repository constructor.
     */
    public function __construct()
    {
        $this->disk = Storage::disk('docs');
        $this->config = $this->config ?: (config('docs') ?: (is_file(__DIR__.'/../config/config.php' ? include __DIR__.'/../config/config.php' : [])));
    }

    /**
     * @param $vendor
     * @param null $version
     * @return bool|mixed
     */
    public function getDefaultPage($vendor, $version = null)
    {
        $index = $this->getKey('default.page');

        if(!$version){
            if($this->isFile($vendor.'/'.$index)){
                return $this->replaceLinks($vendor, '5.3', $this->getFile($vendor.'/'.$index, $vendor));
            }
            return false;
        }

        if($this->isFile($vendor.'/'.$version.'/'.$index)){
            return $this->replaceLinks($vendor, $version, $this->getFile($vendor.'/'.$version.'/'.$index, $vendor, $version));
        }

        return false;
    }

    /**
     * @param $vendor
     * @param null $version
     * @return bool|mixed
     */
    public function getIndex($vendor, $version = null)
    {
        $index = $this->getKey('default.index');

        if(!$version){
            if($this->isFile($vendor.'/'.$index)){
                return $this->replaceLinks($vendor, '5.3', $this->getFile($vendor.'/'.$index, $vendor));
            }
            return false;
        }

        if($this->isFile($vendor.'/'.$version.'/'.$index)){
            return $this->replaceLinks($vendor, $version, $this->getFile($vendor.'/'.$version.'/'.$index, $vendor, $version));
        }

        return false;
    }

    /**
     * Get list of vendors including files
     * @return array
     */
    public function getVendors()
    {
        return $this->securityNotPassed() ?: array_merge(['dirs' => $this->disk->directories()], ['files' => $this->getFiles()]);
    }

    /**
     * Get list of versions including files
     * @param $vendor
     * @return array
     */
    public function getVersions($vendor)
    {
        return $this->securityNotPassed($vendor) ?: array_merge(['dirs' => str_replace($vendor.'/', '', $this->disk->directories($vendor))], ['files' => str_replace($vendor.'/', '', $this->getFiles($vendor))]);
    }

    /**
     * Get page OR list of pages from versions dir
     * @param $vendor
     * @param $version
     * @return mixed
     */
    public function getPages($vendor, $version, $onlyList = false)
    {
        $path = $vendor . '/' . $version;

        if($onlyList){
            return $this->getIndex($vendor, $version) ?: ['files' => str_replace($vendor.'/'.$version.'/', '', $this->getFiles($path))];
        }

        return $this->securityNotPassed($path) ?: ($this->isFile($path) ? $this->getFile($path, $vendor, $version) : ($this->getIndex($vendor, $version) ?: ['files' => str_replace($vendor.'/'.$version.'/', '', $this->getFiles($path))]));
    }

    /**
     * Get page
     * @param $vendor
     * @param $version
     * @param $page
     * @return mixed
     */
    public function getPage($vendor, $version, $page)
    {
        return $this->securityNotPassed($vendor . '/' . $version) ?: $this->getFile($vendor . '/' . $version . '/' . $page, $vendor, $version);
    }

    /**
     * Get extension of the page
     * @param $file
     * @return mixed
     */
    public function getExtension($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION);
    }

    /**
     * Can autodetect/guess the extension of the page
     * @return mixed
     */
    public function canGuessExtension()
    {
        return $this->getKey('extensions.guess', false);
    }

    /**
     * IF canGuessExtensions, then guess from config -> 'extensions.supported' param
     * @param $file
     * @return bool
     */
    public function guessExtension($file)
    {
        return $this->canGuessExtension() ? empty($this->getExtension($file)) : false;
    }

    /**
     * Allowed extensions
     * @return array
     */
    public function allowedExtensions()
    {
        return array_prepend($this->getKey('extensions.supported'), $this->getKey('default.extension'));
        //return $this->willFilterExtensions() ? array_prepend($this->getKey('extensions.supported'), $this->getKey('default.extension')) : [$this->getKey('default.extension')];
    }

    /**
     * Can filter extensions of pages
     * @return mixed
     */
    public function willFilterExtensions()
    {
        return $this->getKey('extensions.filter');
    }

    /**
     * IF willFilterExtensions, then exclude the config -> 'extensions.exclude' from the response list
     * @return mixed
     */
    public function excludedExtensions()
    {
        return $this->getKey('extensions.exclude');
    }

    /**
     * Will check the config -> 'security.enabled'
     * @param null $path
     * @return bool|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    protected function securityNotPassed($path = null)
    {
        if($path && $this->getKey('security.enabled')){
            $securityFile = $this->getKey('security.file');
            if(str_contains($path, '/')){
                $explode = explode('/', $path);
                foreach($explode as $dir){
                    if($this->disk->exists($dir.'/'.$securityFile)){
                        $parameters = json_decode($this->disk->get($dir.'/'.$securityFile));
                    }
                }
                return isset($parameters) ? $this->returnSecurity($parameters) : false;
            }else{
                if($this->disk->exists($path.'/'.$securityFile)){
                    $parameters = json_decode($this->disk->get($path.'/'.$securityFile));
                    return $this->returnSecurity($parameters);
                }
            }
        }
        return false;
    }

    /**
     * IF securityNotPassed, then verify [auth && roles||permissions] from config -> 'security.file'
     * @param $parameters
     * @return bool|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    protected function returnSecurity($parameters)
    {
        if($this->auth()){
            view('docs::partials.security', compact('parameters'));
        }else{
            if(Route::has('login.get')){
                return redirect()->route('login.get');
            }elseif(Route::has('login')){
                return redirect()->route('login');
            }else{
                return view('docs::partials.security', compact('parameters'));
            }
        }

        return false;
    }

    /**
     * PHP is_file func,
     * IF canGuessExtensions || willFilterExtensions, than filter & return extensions
     * @param $file
     * @return bool|string
     */
    public function isFile($file)
    {
        if($this->guessExtension($file) || $this->willFilterExtensions()){
            foreach($this->allowedExtensions() as $ext){
                if((!empty($this->getExtension($file)) && $this->getExtension($file) == $ext) || is_file($this->getKey('disk.root').'/'.$file.'.'.$ext)){
                    return '.'.$ext;
                }
            }
        }else{
            return is_file($this->getKey('disk.root').'/'.$file);
        }

        return false;
    }

    /**
     * Get list of files with 'extensions.filter' => 'extensions.exclude'
     * @param null $path
     * @return array
     */
    protected function getFiles($path = null)
    {
        $files = $this->disk->files($path?:'');
        $canExclude = $this->getKey('extensions.filter');

        foreach($files as $key => $file){
            if($canExclude){
                foreach($this->excludedExtensions() as $ext){
                    if($this->getExtension($file) == $ext){
                        unset($files[$key]);
                    }
                }
            }

            if($this->willFilterExtensions()){
                foreach($this->allowedExtensions() as $ext){
                    if(str_contains($file, '.'.$ext)){
                        $response[] = str_replace('.'.$ext, '', $file);
                    }
                }
            }
        }

        return isset($response) ? $response : $files;
    }

    /**
     * Get rendered||default page,
     * rendering is based on the extensions of the page
     * @param null $path
     * @param null $vendor
     * @param null $version
     * @return mixed
     */
    protected function getFile($path = null, $vendor = null, $version = null)
    {
        if(!empty($this->getExtension($path))){
            $ext = $this->getExtension($path);
            return $this->renderContent($ext, $vendor, $version, $this->disk->get($path));
        }elseif($this->guessExtension($path) || $this->willFilterExtensions()){
            $ext = $this->isFile($path);
            if(!empty($ext)){
                $func = str_replace('.', '', $ext);
                return $this->renderContent($func, $vendor, $version, $this->disk->get($path.$ext));
            }else{
                return $this->disk->get($path.$this->isFile($path));
            }
        }

        return $this->disk->get($path);
    }

    /**
     * Render page
     * @param $func
     * @param null $vendor
     * @param null $version
     * @param $content
     * @return mixed
     */
    protected function renderContent($func, $vendor = null, $version = null, $content)
    {
        if(method_exists($this, $func)){
            $content = $this->$func($content);
            return $vendor && $version ? $this->replaceLinks($vendor, $version, $content) : $content;
        }else{
            return $content;
        }
    }

    /**
     * Replace parameters from content
     * @param $vendor
     * @param $version
     * @param $content
     * @return mixed
     */
    protected function replaceLinks($vendor, $version, $content)
    {
        return str_replace('{{version}}', $vendor.'/'.$version, $content);
    }

    /**
     * Get config keys
     * @param $key
     * @param null $default
     * @return mixed
     */
    protected function getKey($key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    /**
     * Scaffold auth, used for matching secutiry parameters
     * @return bool
     */
    protected function auth()
    {
        return auth()->check();
    }

    /**
     * Render .md pages
     * @param $text
     * @return mixed|string
     */
    protected function md($text)
    {
        return (new ParsedownExtra)->text($text);
        //return (new MDParser)->getBlocks((new ParsedownExtra)->text($text));
    }
}