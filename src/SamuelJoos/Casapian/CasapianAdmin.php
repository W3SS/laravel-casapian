<?php namespace SamuelJoos\Casapian;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Database\Eloquent\Model;

class CasapianAdmin
{
    /**
     * The model for your admin
     * @var Eloquent
     */
    public $model;
    /**
     * The name for the admin section in single form
     * @var [type]
     */
    public $single;
    /**
     * The editable fields
     * @var array
     */
    private $edit = array();
    /**
     * The list Columns
     * @var [type]
     */
    public $list;
    /**
     * The data when for the model if it already exists
     * @var [type]
     */
    public $data;
    /**
     * Adds a field to the $edit array
     * @param Field $field an instance of the Field class
     */
    private $overviewQuery;

    public function setOverviewQuery($query)
    {
        $this->overviewQuery = $query;
    }
    public function getOverviewQuery()
    {
        return $this->overviewQuery;
    }
    public function addField($field)
    {
        $field->setAdmin($this);
        $this->edit[] = $field;
    }
    /**
     * Overview action
     * @return Response
     */
    public function overview ()
    {
        $model = $this->model;
        if (!isset($this->overviewQuery)) {
            $this->overviewQuery = $model::orderBy("id", "desc");
        }

        $items = $this->overviewQuery->get();
        $modelClass = new $model;

        return View::make(
            'casapian::overview',
            array(
                'items' => $items,
                'admin' => get_class($this),
                'single' => $this->single,
                'fields' => $this->list,
                'key' => $modelClass->getKeyName(),
            )
        );
    }
    /**
     * Edit action
     * @param  int      $key
     * @return Response
     */
    public function edit($key)
    {
        $model = $this->model;
        $modelClass = new $model;
        $this->data = $model::find($key);

        return View::make('casapian::edit', array(
            'data' => $this->data,
            'key' => $modelClass->getKeyName(),
            'admin' => get_class($this),
            'fields' => $this->edit,
            'single' => $this->single,
        ));
    }
    /**
     * Create action
     * @return Response
     */
    public function create()
    {
        return View::make('casapian::create', array(
            'admin' => get_class($this),
            'fields' => $this->edit,
            'single' => $this->single,
        ));
    }
    /**
     * Save action
     * @return Response
     */
    public function save ()
    {
        if (Input::has('delete')) {
            return $this->delete();
        } else {
            $model = $this->model;
            $modelClass = new $model;
            $key = Input::get($modelClass->getKeyName());

            if (isset($key)) {
                $model = $model::find($key);
            } else {
                $model = new $this->model();
            }

            $afterSave = false;
            foreach ($this->edit as $field) {
                if (!$field->afterSave) {
                    $field->saveTo($model);
                } else {
                    $afterSave = true;
                }
            }
            $model->save();
            if ($afterSave) {
                foreach ($this->edit as $field) {
                    if ($field->afterSave) {
                        $field->saveTo($model);
                    }
                }
                $model->save();
            }

            if (Request::ajax()) {
                return Response::json($model);
            } else {

                if (Input::has('save_next')) {
                    return Redirect::route('admin_create', array(
                            get_class($this)
                        )
                    );
                }

                return Redirect::route('admin_overview', array(
                        get_class($this)
                    )
                );
            }
        }
    }
    /**
     * Delete action
     * @return Response
     */
    public function delete()
    {
        $model = $this->model;
        $modelClass = new $model;
        $key = Input::get($modelClass->getKeyName());

        if (is_array($key)) {
            $model = $model::whereIn($modelClass->getKeyName(), $key);
        } else {
            $model = $model::find($key);
        }

        $model->delete();

        return Redirect::route('admin_overview', array(get_class($this)));
    }
}
