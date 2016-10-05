<?php

namespace WA\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Request;
use WA\DataStore\Image\Image;
use WA\DataStore\Image\ImageTransformer;
use WA\Repositories\Image\ImageInterface;

use League\Flysystem\Filesystem;

use DB;

use Illuminate\Support\Facades\Lang;

/**
 * Image resource.
 *
 * @Resource("Image", uri="/image")
 */
class ImageController extends ApiController
{
    /**
     * @var ImageInterface
     */
    protected $image;

    protected $urlFile;

    /**
     * Image Controller constructor
     *
     * @param ImageInterface $image
     */
    public function __construct(ImageInterface $image)
    {
        $this->image = $image;
    }

    /**
     * Show all Images
     *
     * Get a payload of all Image
     *
     */
    public function index()
    {

        $criteria = $this->getRequestCriteria();
        $this->image->setCriteria($criteria);
        $image = $this->image->byPage();

        $response = $this->response()->withPaginator($image, new ImageTransformer(), ['key' => 'images']);
        $response = $this->applyMeta($response);
        return $response;
    }

    /**
     * Show a single Image
     *
     * Get a payload of a single Image
     *
     * @Get("/{id}")
     */
    public function show($id)
    {

        $criteria = $this->getRequestCriteria();
        $this->image->setCriteria($criteria);
        $image = Image::find($id);

        if($image == null){
            $error['errors']['get'] = Lang::get('messages.NotExistClass', ['class' => 'Image']);   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        $path = $image->filename . '.' . $image->extension;

        $value = Storage::get($path);

        return response($value, 200)->header('Content-Type', $image->mimeType);
    }

    /**
     * Show a single Image Information
     *
     * Get a payload of a single Image information
     *
     * @Get("/{id}")
     */
    public function info($id) {

        $image = Image::find($id);
        if($image == null){
            $error['errors']['get'] = Lang::get('messages.NotExistClass', ['class' => 'Image']);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        return $this->response()->item($image, new ImageTransformer(),['key' => 'images'])->setStatusCode($this->status_codes['created']);
    }
   
    /**
     * Create a new Image
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create()
    {

        try {
            $file = Request::file('filename');

            $imageFile['originalName'] = $file->getClientOriginalName();
            $filename = $imageFile['filename'] = $file->getFilename();
            $imageFile['mimeType'] = $file->getClientMimeType();
            $extension = $imageFile['extension'] = $file->getClientOriginalExtension();
            $imageFile['size'] = $file->getClientSize();
            $imageFile['url'] = $filename . '.' . $extension;

            $value = Storage::put($filename . '.' . $extension, file_get_contents($file));

            if ($value) {
                $image = $this->image->create($imageFile);
            } else {
                Storage::delete($file);
            }
        } catch (\Exception $e) {
            $error['errors']['image'] = Lang::get('messages.NotOptionIncludeClass', ['class' => 'Image', 'option' => 'created', 'include' => '']);
            //$error['errors']['imageMessage'] = $e->getMessage();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        return $this->response()->item($image, new ImageTransformer(),
            ['key' => 'images'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Delete an Image
     *
     * @param $id
     */
    public function delete($id)
    {

        $image = Image::find($id);
        if ($image <> null) {
            $this->image->deleteById($id);
            Storage::delete($path = $image->filename . '.' . $image->extension);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'Image']);   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }
        
        $image = Image::find($id);        
        if($image == null){
            return array("success" => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'Image']);   
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}