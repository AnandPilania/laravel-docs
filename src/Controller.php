<?php

namespace AP\Docs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Base;

/**
 * Class Controller
 * @package AP\Docs
 */
class Controller extends Base
{
    /**
     * @var Contract
     */
    protected $repo;

    /**
     * Controller constructor.
     * @param Contract $repo
     */
    public function __construct(Contract $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @return mixed
     */
    public function showVendors()
    {
        return view('docs::partials.vendors', ['vendors' => $this->repo->getVendors()]);
    }

    /**
     * @param Request $request
     * @param $vendor
     * @return mixed
     */
    public function showVersionsOrPages(Request $request, $vendor)
    {
        return view('docs::partials.versions', ['title' => $vendor, 'versions' => $this->repo->getVersions($vendor)]);
    }

    /**
     * @param Request $request
     * @param $vendor
     * @param $versionOrPage
     * @return mixed
     */
    public function showPagesOrShowPage(Request $request, $vendor, $versionOrPage)
    {
        $title = $vendor;
        $response = $this->repo->getPages($vendor, $versionOrPage);

        if(is_array($response)){
            return view('docs::partials.pages', ['title' => $title, 'pages' => $response]);
        }else{
            if($this->repo->isFile($vendor.'/'.$versionOrPage)){
                return view('docs::partials.page', ['title' => $title, 'versionOrPage' => $versionOrPage, 'page' => $response]);
            }

            return view('docs::partials.page', ['title' => $title, 'versionOrPage' => $versionOrPage, 'index' => $response, 'page' => $this->repo->getDefaultPage($vendor, $versionOrPage)]);
        }
    }

    /**
     * @param Request $request
     * @param $vendor
     * @param $version
     * @param $page
     * @return mixed
     */
    public function showPage(Request $request, $vendor, $version, $page)
    {
        return view('docs::partials.page', ['title' => $vendor.' ['.$version.'] : '.$page, 'index' => $this->repo->getPages($vendor, $version, true), 'page' => $this->repo->getPage($vendor, $version, $page)]);
    }
}