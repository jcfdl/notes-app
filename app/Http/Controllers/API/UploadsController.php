<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadsController extends Controller
{
    public function fileUpload(Request $request) {
      $path = Storage::disk('s3')->put('files', $request->image);
      $path = Storage::disk('s3')->url($path);
      
      $data = array('response' => true, 'data' => $path, 'mesage' => 'Successfully uploaded the file!');
      return json_encode($data);
    }
}
