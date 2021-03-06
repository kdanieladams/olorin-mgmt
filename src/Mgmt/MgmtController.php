<?php

namespace Olorin\Mgmt;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Olorin\Mgmt\MgmtException;
use Auth;

class MgmtController extends Controller
{
    protected $user         = null;
    protected $model        = null;
    protected $model_name   = '';
    protected $item         = null;
    protected $items        = null;

    public function __construct()
    {
        // parent::__construct();
    }

    /**
     * Display the Mgmt index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('mgmt::index');
    }

    /**
     * Try to interpret a given MGMT command.  This is the entry point for everything
     * MGMT can do.
     *
     * @param string $command
     * @param string $model
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function command(Request $request, $command, $model = null, $id = 0)
    {
        if($this->user == null) {
            $this->user = Auth::user();
        }
        if($this->hasCommand($command)){
            $this->getModel($model);

            if($id > 0)
                return $this->$command($id, $request);
            else
                return $this->$command($request);
        }
        else if($this->hasModel($command)){
            $this->getModel($command);

            return $this->getList();
        }

        flash()->error('Unable to resolve ' . $command);
        return redirect()->route('mgmt.index');
    }

    private function filterPermissions(&$item)
    {
        $fields = $item->mgmt_fields;

        foreach($fields as $k => $field){
            if(count($field->permissions) > 0 && !($this->user->hasPermission($fields[$k]->permissions))){
                unset($item->mgmt_fields[$k]);
            }
        }
    }

    /**
     * Check if the MgmtController has a method corresponding to the
     * given command.
     *
     * @param string $command
     * @return bool
     */
    protected function hasCommand($command)
    {
        return method_exists($this, $command);
    }

    /**
     * Check if a model exists corresponding to the given command.
     *
     * @param string $model
     * @return bool
     */
    protected function hasModel($model)
    {
        $hasModel = false;

        if(class_exists($model)) {
            $hasModel = true;
        }
        else if(is_string($model) && str_contains($model, "-")) {
            $model = str_replace("-", "\\", $model);
            $hasModel = class_exists($model);
        }
        else{
            $hasModel = class_exists("\\App\\" . $model);
        }

        return $hasModel;
    }

    protected function getModel($model_ref)
    {
        if(str_contains($model_ref, "-")) {
            $model_ref = str_replace("-", "\\", $model_ref);

            if(class_exists($model_ref)) {
                $ref_arr = explode("\\", $model_ref);
                $this->model_name = $ref_arr[count($ref_arr) - 1];
                $this->model = $model_ref;
            }
        }
        else if(class_exists("\\App\\" . $model_ref)){
            $this->model_name = $model_ref;
            $this->model = '\App\\' . $model_ref;
        }
    }

    /**
     * Get a particular item out of the database.
     *
     * @param $id
     * @return MgmtException || Illuminate\Database\Eloquent\Model
     */
    protected function getItem($id)
    {
        $model = $this->model;

        if($model == null || !$this->hasModel($model)) {
            throw new MgmtException('Model not found.', 1);
        }

        $item = $model::find($id);

        if(is_null($item)) {
            throw new MgmtException('Invalid '. $this->model_name .' id.', 1);
        }

        $fields = $item->mgmt_fields;

        if(empty($fields)) {
            throw new MgmtException($this->model_name . '\'s not configured for MGMT.', 1);
        }

        return $item;
    }

    /**
     * Gather the collection of all items from the given model.
     *
     * @return Model | Request
     */
    protected function getItems()
    {
        $model = $this->model;
        $items = $model::all();

        //if(count($items) == 0) {
        //   throw new MgmtException('No ' . $this->model_name . "s found", 2);
        //}

        $fields = (new $model())->mgmt_fields;

        if(empty($fields)) {
            throw new MgmtException($this->model_name . '\'s not configured for MGMT.', 1);
        }

        return $items;
    }

    /**
     * Show a list of all instances of a given model.
     *
     * @return \Illuminate\Http\Response
     */
    protected function getList()
    {
        $model = $this->model;
        $items = $this->getItems();

        if(count($items) == 0) {
            $items = [new $model()];
        }

        $list_fields = array();

        foreach($items[0]->mgmt_fields as $field){
            if($field->list){
                $list_fields[] = $field;
            }
        }

        if(empty($list_fields)) {
            // TODO: Try to find one or two field names which seem like they belong in the list, and use those
        }

        $label_key = $items[0]->label_key;

        if(count($items) == 1 && (!isset($items[0]->$label_key) || empty($items[0]->$label_key)))
        {
            $items = [];
        }

        return view('mgmt::list', [
            'model_name' => $this->model_name,
            'items' => $items,
            'list_fields' => $list_fields,
            'exmp' => new $model()
        ]);
    }

    /**
     * Render the view to create a new item.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws MgmtException
     */
    protected function create()
    {
        $item = new $this->model();

        if(!empty($item->create_permission) && !$this->user->hasPermission($item->create_permission)){
            throw new MgmtException('You are not authorized to create a '. $this->model_name .'.', 2);
        }

        return view('mgmt::create', [
            'model_name' => $this->model_name,
            'item' => $item,
            'has_sidebar' => $item->hasSidebar()
        ]);
    }

    /**
     * Handle the POST request to create a new item in the database.
     *
     * @param Request $request
     * @return RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function savenew(Request $request)
    {
        $input = $request->input();
        $model = $this->model;
        $item = new $model();
        $rules = $item->getValidationRules();

        if(!empty($item->create_permission) && !$this->user->hasPermission($item->create_permission)){
            throw new MgmtException('You are not authorized to create a '. $this->model_name .'.', 2);
        }

        if(method_exists($item, "user")){
            $item->user()->associate(Auth::user());
        }

        $this->validate($request, $rules);

        if($request->ajax()) {
            $item->translateInput($request, true);
            $item->save();
            return response()->json(['success' => true]);
        }

        $item->translateInput($request);
        $item->save();

        // return to list page with success message
        flash()->success("Successfully created " . $this->model_name . " " . $item->id . "!");
        return redirect('/mgmt/' . $item->getUrlFriendlyName());
    }

    /**
     * Render the edit view for a given item.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    protected function edit($id)
    {
        $item = $this->getItem($id);

        $this->filterPermissions($item);

        return view('mgmt::edit', [
            'item' => $item,
            'model_name' => $this->model_name,
            'has_sidebar' => $item->hasSidebar()
        ]);
    }

    /**
     * Handle the POST request to update a given item in the database.
     *
     * @param $id
     * @param Request $request
     * @return RedirectResponse
     */
    protected function update($id, Request $request)
    {
        $item = $this->getItem($id);
        $rules = $item->getValidationRules();
        $input = $request->input();

        $this->validate($request, $rules);
        // TODO: check if the request has files, and make sure the file doesn't already exist, if so confirm overwrite...
        $item->translateInput($request);

        $item->save();

        // return to list page with success message
        flash()->success("Successfully updated " . $this->model_name . " " . $item->id . "!");
        return redirect('/mgmt/' . $item->getUrlFriendlyName());
    }

    /**
     * Handle a GET request to delete an object.  This screen affirms that the action was intentional.
     *
     * @param $id
     * @return View | RedirectResponse
     */
    protected function delete($id)
    {
        $item = $this->getItem($id);

        return view('mgmt::delete', [
            'model_name' => $this->model_name,
            'item' => $item
        ]);
    }

    /**
     * Handle the POST request to delete an object.
     *
     * @param $id
     * @param Request $request
     * @return RedirectResponse
     */
    protected function remove($id, Request $request)
    {
        $model = $this->model;

        $model::destroy($id);

        flash()->success("Successfully deleted " . $this->model_name . " " . $id . ".");
        return redirect()->route('mgmt.index');
    }
}
