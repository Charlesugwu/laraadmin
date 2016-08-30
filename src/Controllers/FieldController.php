<?php
/**
 * Controller genrated using LaraAdmin
 * Help: http://laraadmin.com
 */

namespace Dwij\Laraadmin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Schema;

use Dwij\Laraadmin\Models\Module;
use Dwij\Laraadmin\Models\ModuleFields;
use Dwij\Laraadmin\Models\ModuleFieldTypes;
use Dwij\Laraadmin\Helpers\LAHelper;

class FieldController extends Controller
{
    
    public function __construct() {
        // for authentication (optional)
        // $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $module = Module::find($request->module_id);
        $module_id = $request->module_id;
        
        $field_id = ModuleFields::createField($request);
        
        return redirect()->route(config('laraadmin.adminRoute') . '.modules.show', [$module_id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $ftypes = ModuleFieldTypes::getFTypes2();
        // $module = Module::find($id);
        // $module = Module::get($module->name);
        // return view('la.modules.show', [
        //     'no_header' => true,
        //     'no_padding' => "no-padding",
        //     'ftypes' => $ftypes
        // ])->with('module', $module);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $field = ModuleFields::find($id);
        
        $module = Module::find($field->module);
        $ftypes = ModuleFieldTypes::getFTypes2();
        
        $tables = LAHelper::getDBTables([]);
        
        return view('la.modules.field_edit', [
            'module' => $module,
            'ftypes' => $ftypes,
            'tables' => $tables
        ])->with('field', $field);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $module_id = $request->module_id;
        // $module = Module::find($field->module);
        $field = ModuleFields::find($id);

        // Update the Schema
        $this->updateFieldSchema($module_id, $field, $request);

        // Update Context in ModuleFields
        $field->colname = $request->colname;
        $field->label = $request->label;
        $field->module = $request->module_id;
        $field->field_type = $request->field_type;
        if($request->readonly) {
            $field->readonly = true;
        } else {
            $field->readonly = false;
        }
        $field->defaultvalue = $request->defaultvalue;
        $field->minlength = $request->minlength;
        $field->maxlength = $request->maxlength;
        if($request->required) {
            $field->required = true;
        } else {
            $field->required = false;
        }
        if($request->field_type == 7 || $request->field_type == 15 || $request->field_type == 18 || $request->field_type == 20) {
            if($request->popup_value_type == "table") {
                $field->popup_vals = "@".$request->popup_vals_table;
            } else if($request->popup_value_type == "list") {
                $request->popup_vals_list = json_encode($request->popup_vals_list);
                $field->popup_vals = $request->popup_vals_list;
            }
        }
        $field->save();

        return redirect()->route(config('laraadmin.adminRoute') . '.modules.show', [$module_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function updateFieldSchema($module_id, $data_old, $data_new)
    {
        $module = Module::find($module_id);

        // Change Column Name if Different
        if($data_old->colname != $data_new->colname) {
            Schema::table($module->name_db, function ($table) use ($data_old, $data_new) {
                $table->renameColumn($data_old->colname, $data_new->colname);
            });
        }

        // Change Column Type if Different
        
    }
}
