<?php
namespace WA\Http\Controllers;

use WA\DataStore\Image\Image;
use WA\DataStore\Image\ImageTransformer;
use WA\Repositories\Image\ImageInterface;
//use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
//use App\Fileentry;
use Request;
//use ProductRequest;
//use Uploader;
use Illuminate\Support\Facades\Input;
 
use Illuminate\Support\Facades\Storage;
use Flysystem;
//use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;
//use Fileentry;
//use Storage;
use DB;

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
     * Show all Image
     *
     * Get a payload of all Image
     *
     */
    public function index()
    {
        $image = $this->image->byPage();
        return $this->response()->withPaginator($image, new ImageTransformer(),['key' => 'images']);

    }

    public function show($id){
        return $this->image->byId($id);
    }

   
    /**
     * Create a new Image
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create()   
    {        
        try{
            DB::beginTransaction();

            $file = Request::file('filename');

            $extension = $imageFile->extension = $file->getClientOriginalExtension();
            $filename = $imageFile->filename = $file->getFilename();
            $imageFile->mime = $file->getClientMimeType();
            $imageFile->original = $file->getClientOriginalName();
            $imageFile->size = $file->getClientSize();    
            
            $value = Flysystem::put($filename.'.'.$extension, $file);

            if($value){
                $image = $this->image->create($imageFile);
            } else {
                Flysystem::delete($file);
            }   
        } catch (\Exception $e) {
            $error['errors']['image'] = 'the Image can not be created';
            $error['errors']['imageMessage'] = $this->getErrorAndParse($e);
            return response()->json($error)->setStatusCode(409);
        }
        return $this->response()->item($image, new ImageTransformer(),['key' => 'images']);
    }

    /**
     * Delete a Image
     *
     * @param $id
     */
    public function delete($id)
    {
        $this->image->deleteById($id);
        $this->index();
    }

    private function getErrorAndParse($error){
        try{
            $reflectorResponse = new \ReflectionClass($error);
            $classResponse = $reflectorResponse->getProperty('message');    
            $classResponse->setAccessible(true);
            $dataResponse = $classResponse->getValue($error);
            return $dataResponse;    
        } catch (\Exception $e){
            return 'Generic Error';
        }
    }
}