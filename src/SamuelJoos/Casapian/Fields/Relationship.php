<?php namespace SamuelJoos\Casapian\Fields;

use Illuminate\Support\Facades\Input;

class Relationship extends Field
{
    /**
     * template name
     * @var string
     */
    protected $template = "casapian::fields.relationship";
    /**
     * Column or accesor
     * @var string
     */
    public $fieldname;
    /**
     * Show add btn
     * @var boolean
     */
    public $add;
    /**
     * RelatedAdmin used if add is true
     * @var string
     */
    public $relatedAdmin;
    /**
     * Relationship constructor
     * @param string  $name      the name of the form field
     * @param string  $label     the label for the form field
     * @param string  $fieldname column or accesor of the related model
     * @param boolean $add       show an add button next to the select list
     */
    public function __construct($name, $label, $fieldname, $relatedAdmin = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->fieldname = $fieldname;
        $this->add = ($relatedAdmin != null);
        $this->relatedAdmin = $relatedAdmin;
    }
    /**
     * admin setter
     * @param CasapianAdmin $admin the related admin of the field
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;
        if ($this->isMultiple() || $this->isHasMany()) {
            $this->afterSave = true;
        }
    }
    /**
     * Gets the name value vor the form select
     * @return string
     */
    public function getName()
    {
        if ($this->isMultiple()) {
            return $this->name.'[]';
        }

        return $this->name;
    }
    /**
     * Get the primary key name
     * @return string primary key name
     */
    public function getKey()
    {
        $model = new $this->admin->model;

        return $model->getKeyName();
    }
    /**
     * Get the relation class name
     * @return string relation class name
     */
    protected function getRelationType()
    {
        $model = new $this->admin->model;
        $relatedClass = $model->{$this->name}();

        return get_class($relatedClass);
    }
    /**
     * Get the related model query
     * @return [type] [description]
     */
    protected function getRelatedModel()
    {
        $model = new $this->admin->model;
        $relatedClass = $model->{$this->name}();

        return $relatedClass->getRelated();
    }
    /**
     * Get the related Model Class
     * @return string
     */
    protected function getRelatedModelClass()
    {
        $relatedModel = $this->getRelatedModel();

        return get_class($relatedModel);
    }
    /**
     * Check if Model relation can have Multiple records or just one
     * @return boolean
     */
    public function isMultiple()
    {
        if ($this->getRelationType() ==
            'Illuminate\Database\Eloquent\Relations\BelongsTo') {
            return false;
        }

        return true;
    }

    public function isHasMany()
    {
        if ($this->getRelationType() ==
            'Illuminate\Database\Eloquent\Relations\HasMany') {
            return true;
        }

        return false;
    }
    /**
     * Get saved the relationships
     * @return string/array returns a string or an array
     *                          depending on the relationship type
     */
    public function getSelected()
    {
        if (isset($this->admin->data)) {

            if ($this->isMultiple() || $this->isHasMany()) {
                $selected = array();
                foreach ($this->admin->data[$this->name] as $select) {
                    $selected[] = $select->getKey();
                }

                return $selected;
            } else {

                if ($this->admin->data[$this->name] != null) {
                    return array($this->admin->data[$this->name]->getKey());
                }

            }

        }

        return array();
    }
    /**
     * Get posible options for the select list
     * @return [type] [description]
     */
    public function getOptions()
    {
        // todo: is multiple only show unused refrences
        $relatedModelClass = $this->getRelatedModelClass();
        $relatedModel = $this->getRelatedModel();

        $data = $relatedModelClass::all()->lists(
            $this->fieldname,
            $relatedModel->getKeyName()
        );

        $options = array(""=>"");
        foreach ($data as $key => $result) {
            $options[$key] = $result;
        }

        return $options;
    }
    /**
     * Method overide from Fields class: sets the correct value on the model.
     * @param  Eloquent $model
     * @return void
     */
    public function saveTo(&$model)
    {
        if ($this->isHasMany()) {
            $items = Input::get($this->name, array());
            $relatedModelClass = $this->getRelatedModelClass();
            $relatedModel = $this->getRelatedModel();
            $itemsData = array();
            if (!empty($items)) {
                $itemsData = $relatedModelClass::whereIn($relatedModel->getKeyName(), $items)->get();
            }
            $key = $model->{$this->name}()->getPlainForeignKey();
            foreach ($model->{$this->name}()->get() as $existingItem) {
                $found = false;
                foreach ($itemsData as $item) {
                    if ($existingItem->getKey() == $item->getKey()) {
                        $found = true;
                    }
                }
                if (!$found) {
                    $existingItem->{$key} = null;
                    $existingItem->save();
                }
            }
            foreach ($itemsData as $item) {
                $item->{$key} = $model->getKey();
                $item->save();
            }
        } else {
            if ($this->isMultiple()) {
                $items = Input::get($this->name, array());
                $model->{$this->name}()->sync($items);
            } else {
                $relatedModelClass = $this->getRelatedModelClass();
                $result = $relatedModelClass::where(
                    $this->getRelatedModel()->getKeyName(),
                    "=",
                    Input::get($this->name)
                )->first();
                if ($result != null) {
                    $model->{$this->name}()->associate($result);
                }
            }
        }

    }
}
