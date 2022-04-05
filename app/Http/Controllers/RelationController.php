<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Relation;

class RelationController extends Controller
{
    public function index() {

        $relations = Relation::all();

        return View::make('relations.index', 
            [
                "relations" => $relations
            ]
        );
    }
}
