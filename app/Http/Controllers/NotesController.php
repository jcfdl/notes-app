<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notes;

class NotesController extends Controller {
  // all posts
  public function index(){
      $posts = Notes::all()->toArray();
      $data = array('response' => 'ok', 'data' => $posts);
      return json_encode($data);
  }
}
