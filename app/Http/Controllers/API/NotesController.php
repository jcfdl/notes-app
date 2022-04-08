<?php

namespace App\Http\Controllers\API;

use App\Models\Notes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotesController extends Controller
{
  public function index(){
      $posts = Notes::all()->toArray();
      $data = array('response' => 'ok', 'data' => $posts);
      return json_encode($data);
  }
  
  public function add(Request $request) {
    // response
    $data = [
        'response' => true,
        'message' => 'Successfully added the note!',
    ];
    
    try {
      $book = new Notes([
          'user_id' => Auth::id(),
          'title' => $request->title,
          'body' => $request->body,
          'updated_by' => Auth::id()
      ]);
      $book->save();
    } catch (\Illuminate\Database\QueryException $ex) {
        $data['response'] = false;
        $data['message'] = $ex->getMessage();
    }
    
    return response()->json($data);
  }
  
    public function delete($id) {
        $book = Notes::find($id);
        $book->delete();
        
        // response
        $data = [
            'response' => true,
            'message' => 'Successfully deleted the note!',
        ];
        return response()->json($data);
    }
    
    public function edit($id) {
        $note = Notes::find($id);
        
        $data = [
            'response' => true,
            'data' => $note,
        ];
        
        return response()->json($data);
    }
    
    public function update($id, Request $request) {
        $note = Notes::find($id);
        $update = $request->all();
        $update['updated_by'] = Auth::id();
        $note->update($update);
        
        $data = [
            'response' => true,
            'message' => "Successfully updated the note!",
        ];
        return response()->json($data);
    }
}
