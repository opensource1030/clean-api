<?php
namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use WA\DataStore\Modification\ModificationTransformer;
use WA\Repositories\Modification\ModificationInterface;

/**
 * Modification resource.
 *
 * @Resource("Modification", uri="/Modification")
 */
class ModificationController extends ApiController
{
    /**
     * @var modificationInterface
     */
    protected $modification;

    /**
     * modification Controller constructor
     *
     * @param modificationInterface $modification
     */
    public function __construct(ModificationInterface $modification)
    {
        $this->modification = $modification;
    }

    /**
     * Show all modification
     *
     * Get a payload of all modification
     *
     */
    public function index()
    {
        $modification = $this->modification->byPage();
        return $this->response()->withPaginator($modification, new ModificationTransformer(),['key' => 'modifications']);

    }

    /**
     * Show a single modification
     *
     * Get a payload of a single modification
     *
     * @Get("/{id}")
     */
    public function show($id)
    {
        $modification = $this->modification->byId($id);
        return $this->response()->item($modification, new ModificationTransformer(), ['key' => 'modifications']);
    }

    /**
     * Update contents of a modification
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)   
    {
        $data = $request->all();       
        $data['id'] = $id;
        $modification = $this->modification->update($data);
        return $this->response()->item($modification, new ModificationTransformer(), ['key' => 'modifications']);
    }

    /**
     * Create a new modification
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $modification = $this->modification->create($data);
        return $this->response()->item($modification, new ModificationTransformer(), ['key' => 'modifications']);
    }

    /**
     * Delete a modification
     *
     * @param $id
     */
    public function delete($id)
    {
        $this->modification->deleteById($id);
        $this->index();
    }
}