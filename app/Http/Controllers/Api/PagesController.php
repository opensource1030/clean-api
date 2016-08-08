<?php
namespace WA\Http\Controllers\Api;

use App;
use WA\DataStore\Page\PageTransformer;
use WA\Repositories\Pages\PagesInterface;
use Input;


/**
 * Pages resource.
 *
 * @Resource("Pages", uri="/pages")
 */
class PagesController extends ApiController
{


    /**
     * @var PagesInterface
     */
    protected $pages;

    /**
     * Pages Controller constructor
     *
     * @param PagesInterface $pages
     */
    public function __construct(PagesInterface $pages)
    {
        $this->pages = $pages;
    }

    /**
     * Show all pages
     *
     * Get a payload of all pages
     *
     */
    public function index()
    {
        //$pages = $this->pages->byPage();
       // return $this->response()->withPaginator($pages, new PagesTransformer());

        $pages = $this->pages->getAllPages();
        return $this->response()->collection($pages, new PageTransformer(),['key' => 'pages']);

    }

    /**
     * Show a single page
     *
     * Get a payload of a single page
     *
     * @Get("/{id}")
     */
    public function show($id)
    {
        $page = $this->pages->byId($id);
        return $this->response()->item($page, new PageTransformer(), ['key' => 'pages']);
    }

    /**
     * Store contents for the page
     *
     * @return mixed
     */
    public function store()
    {
        $data = Input::all();
        $page = $this->pages->update($data);
        return $page;


    }


}
