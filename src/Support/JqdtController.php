<?php

namespace Olorin\Support;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\Controller;
use View;

class JqdtController extends Controller
{
    private $instance = null;
    private $data = [];
    private $input = [];
    private $qry = null;
    private $filteredCount = 0;

    public function getListPage(Request $request, $model)
    {
        $this->input = $request->all();
        $classname = strpos($model, "-") ? "\\" . str_replace("-", "\\", $model)
            : '\App\\' . $model;
        $total = 0;
        $this->instance = null;
        $orderBy = '';
        $this->data = [];
        $retArr = [];

        if(!class_exists($classname)) {
            return response()->json(['error' => 'Whoops!  No class found...']);
        }

        $total = $classname::all()->count();
        $this->instance = new $classname();
        $this->getData($classname);

        return response()->json(new JqdtPageResponse($this->input['draw'],
            $total, $this->filteredCount,
            $this->resolveValues()
        ));
    }

    private function getData($classname)
    {
        $this->data = null;
        $this->filterQry($classname);
        $this->orderQry($classname);
        $this->data = $this->qry
            ->skip($this->input['start'])
            ->take($this->input['length'])
            ->get();
    }

    private function filterQry($classname)
    {
        $searchQry = $this->input['search']['value'];
        if(!empty($searchQry)) {
            $searchQry = '%' . $this->input['search']['value'] . '%';
            foreach($this->input['columns'] as $k => $col) {
                if(!empty($col['name'])) {
                    $field = $this->instance->mgmt_fields[$col['name']];
                    if($field->related) {
                        if($field->relationship == 'belongsTo') {
                            $col['name'] = $col['name'] . '_id';
                            $relatedClassname = '\\' . $field->{$field->relationship};
                            $relatedResults = array_flatten($relatedClassname::select('id')
                                ->where($field->getLabelKey($this->instance), 'like', $searchQry)
                                ->get()->toArray()
                            );
                            if($k == 0)
                                $this->qry = $classname::orWhere(function($query) use($col, $relatedResults) {
                                    $query->whereIn($col['name'], $relatedResults);
                                });
                            else
                                $this->qry->orWhere(function($query) use($col, $relatedResults) {
                                    $query->whereIn($col['name'], $relatedResults);
                                });

                            continue;
                        }
                        else {
                            continue;
                        }
                    }
                    if($k == 0)
                        $this->qry = $classname::where($col['name'], 'like', $searchQry);
                    else
                        $this->qry->orWhere($col['name'], 'like', $searchQry);
                }
            }
        }
    }

    private function orderQry($classname)
    {
        foreach($this->input['order'] as $k => $ord) {
            $colName = $this->input['columns'][$ord['column']]['name'];
            $field = $this->instance->mgmt_fields[$colName];
            if($field->related) {
                if($field->relationship == 'belongsTo') {
                    $colName = $colName . '_id';
                    $relatedClassname = '\\' . $field->{$field->relationship};
                    $relatedInstance = new $relatedClassname();
                    if(!($this->qry instanceof Builder))
                        $this->qry = $classname::join($relatedInstance->getTable(),
                            $relatedInstance->getTable() . '.id', '=',
                            $this->instance->getTable() . '.' . $colName)
                            ->select($this->instance->getTable() . ".*", $relatedInstance->getTable() . '.' . $field->getLabelKey($this->instance))
                            ->orderBy($relatedInstance->getTable() . '.' . $field->getLabelKey($this->instance), $ord['dir']);
                    else
                        $this->qry->join($relatedInstance->getTable(),
                            $relatedInstance->getTable() . '.id', '=',
                            $this->instance->getTable() . '.' . $colName)
                            ->select($this->instance->getTable() . ".*", $relatedInstance->getTable() . '.' . $field->getLabelKey($this->instance))
                            ->orderBy($relatedInstance->getTable() . '.' . $field->getLabelKey($this->instance), $ord['dir']);

                    continue;
                }
                else {
                    continue;
                }
            }

            if(!($this->qry instanceof Builder))
                $this->qry = $classname::orderBy($colName, $ord['dir']);
            else
                $this->qry->orderBy($colName, $ord['dir']);
        }

        $this->filteredCount = $this->qry->count();
    }
    
    private function resolveValues()
    {
        $retArr = [];
        foreach($this->data as $k => $d) {
            foreach($this->input['columns'] as $j => $col) {
                if(strlen($col['name']) > 0) {
                    $value = $d->{$col['name']};
                    $field = $this->instance->mgmt_fields[$col['name']];
                    $view = null;

                    if($field->type == 'related') {
                        $view = View::make('mgmt::fields._' . $field->relationship, [
                            'list' => true,
                            'value' => $field->getRelatedOptions($d),
                            'selected' => $field->getRelatedId($d),
                            'name' => $field->name,
                            'label' => $field->label,
                            'view_options' => $field->view_options
                        ]);
                    }
                    else {
                        $view = View::make('mgmt::fields._' . $field->type, [
                            'list' => true,
                            'name' => $field->name,
                            'value' => $value,
                            'view_options' => $field->view_options
                        ]);
                    }
                    $retArr[$k][] = $view->render();
                }
                else {
                    $retArr[$k][] = "<div class=\"btn-group pull-right\">
                        <a href=\"/mgmt/edit/" . $d->getUrlFriendlyName() . "/" . $d->id . "\" class=\"btn btn-hollow\">
                            <span class=\"glyphicon glyphicon-edit\"></span>
                            Edit
                        </a>
                        <a href=\"/mgmt/delete/" . $d->getUrlFriendlyName() . "/" . $d->id . "\" class=\"btn btn-hollow-danger\">
                            <span class=\"glyphicon glyphicon-trash\"></span>
                            Delete
                        </a>
                    </div>";
                }
            }
        }
        return $retArr;
    }
}