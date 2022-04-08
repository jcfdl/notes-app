<?php

namespace App\Http\Controllers\API;

use App\Models\Notes;
use App\Models\NoteShare;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotesController extends Controller {
  
  public function index() {
      $notes = Notes::where('user_id', Auth::id())
      ->leftJoin('users', 'users.id', '=', 'notes.updated_by')
      ->select('notes.*', 'users.name AS updated_by_name')
      ->orderBy('id', 'DESC')->get();
      
      $data = array('response' => 'ok', 'data' => $notes);
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
          'updated_by' => Auth::id(),
          'file_attachments' => $request->file_attachments
      ]);
      $book->save();
    } catch (\Illuminate\Database\QueryException $ex) {
        $data['response'] = false;
        $data['message'] = $ex->getMessage();
    }
    
    return response()->json($data);
  }
  
  public function delete($id) {
    $note = Notes::find($id);
    $data = [
        'response' => true,
        'message' => 'Successfully deleted the note!',
    ];
    
    if ($note->user_id == Auth::id())
      $note->delete();
    else {
      $data['response'] = false;
      $data['message'] = "Cannot find the order!";
    }
    
    return response()->json($data);
  }
    
  public function edit($id) {
    $data = [
        'response' => true,
        'data' => "",
        'message' => ""
    ];
    
    $note = Notes::where('notes.id', $id)
    ->where(function($query) {
      $query->where('notes.user_id', Auth::id())->orWhere('note_shares.user_id', Auth::id());
    })
    ->leftJoin('users', 'users.id', '=', 'notes.updated_by')
    ->leftJoin('note_shares', 'note_shares.note_id', '=', 'notes.id')
    ->select('notes.*', 'users.name AS updated_by_name')
    ->first();
    
    if (empty($note)) {
      $data["response"] = false;
      $data["message"] = "Note not found!";
    }
    else {
      $data["response"] = true;
      $data["data"] = $note;
      $data["message"] = "Note found!";
    }
    
    return response()->json($data);
  }

  public function update($id, Request $request) {
      $note = Notes::where('notes.id', $id)
      ->where(function($query) {
        $query->where('notes.user_id', Auth::id())->orWhere('note_shares.user_id', Auth::id());
      })
      ->leftJoin('users', 'users.id', '=', 'notes.updated_by')
      ->leftJoin('note_shares', 'note_shares.note_id', '=', 'notes.id')
      ->select('notes.*', 'users.name AS updated_by_name')
      ->first();
      
      if (empty($note)) {
        $data = [
            'response' => false,
            'message' => "Cannot find the order!",
        ];
        return response()->json($data);
      }
      
      
      $update = $request->all();
      $update['updated_by'] = Auth::id();
      $note->update($update);
      
      if ($note->user_id == Auth::id())
        $this->deleteNoteShare($id);
      if (!empty($request->share) && $note->user_id == Auth::id())
        $this->addNoteShare($id, $request->share);
      
      $data = [
          'response' => true,
          'message' => "Successfully updated the note!",
      ];
      return response()->json($data);
  }
  
  function deleteNoteShare($id) {
    $note = NoteShare::where('note_id', $id)->delete();
  }
  
  function addNoteShare($id, $users) {
    $data = array();
    foreach ($users as $user) {
      array_push($data, array('note_id' => $id, 'user_id' => $user));
    }
    NoteShare::insert($data);
  }

  public function sharedNoteUserList(Request $request) {
    $list = NoteShare::where('note_shares.note_id', $request->note_id)
    ->leftJoin('users', 'users.id', '=', 'note_shares.user_id')
    ->select('note_shares.*', 'users.name AS name')
    ->get();
    
    $data = [
        'response' => true,
        'data' => $list
    ];
    return response()->json($data);
  }
  
  public function sharedNotesList() {
    $notes = NoteShare::where('note_shares.user_id', Auth::id())
    ->leftJoin('notes', 'notes.id', '=', 'note_shares.note_id')
    ->leftJoin('users', 'users.id', '=', 'notes.updated_by')
    ->select('notes.*', 'users.name AS updated_by_name')
    ->orderBy('notes.id', 'DESC')->get();
    
    $data = array('response' => 'ok', 'data' => $notes);
    return json_encode($data);
  }
}
