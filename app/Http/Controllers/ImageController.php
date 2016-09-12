<?php
namespace WA\Http\Controllers;

use WA\DataStore\Image\ImageTransformer;
use WA\DataStore\Image\Image;
use WA\Repositories\Image\ImageInterface;

use Illuminate\Support\Facades\Storage;
use Flysystem;
use Illuminate\Http\Response;
use Request;
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
        $image = Image::find($id);
        if($image == null){
            $error['errors']['get'] = 'the Image selected doesn\'t exists';   
            return response()->json($error)->setStatusCode(409);
        }
        
        return $this->response()->item($image, new ImageTransformer(),['key' => 'images']);
    }

   
    /**
     * Create a new Image
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create()   
    {        
        try{
            $file = Request::file('filename');

            $imageFile['originalName'] = $file->getClientOriginalName();
            $filename = $imageFile['filename'] = $file->getFilename();
            $imageFile['mimeType'] = $file->getClientMimeType();
            $extension = $imageFile['extension'] = $file->getClientOriginalExtension();
            $imageFile['size'] = $file->getClientSize();

            $value = Flysystem::put($filename.'.'.$extension, $imageFile);

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