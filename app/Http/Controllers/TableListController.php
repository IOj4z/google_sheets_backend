<?php

namespace App\Http\Controllers;

use App\Application;
use App\Http\Resources\ApplicationResource;
use Illuminate\Http\Request;

class TableListController extends Controller
{
    public function tableList(Request $request)
    {
        // Здесь вы можете возвращать представление или данные, необходимые для TableList компонента Vue.js
        return view('vue-app');
    }
    public function getTableList() {
        $tables = Application::paginate(15);

        return ApplicationResource::collection($tables);
    }
}
