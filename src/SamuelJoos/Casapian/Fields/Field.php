<?php namespace SamuelJoos\Casapian\Fields;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;

class Field
{
    /**
     * Name of the template
     * @var string
     */
    protected $template;
    /**
     * The admin related to the field
     * @var CasapianAdmin
     */
    protected $admin;
    /**
     * Name of the form field
     * @var string
     */
    public $name;
    /**
     * Label for the form field
     * @var string
     */
    public $label;
    /**
     * Value for the form field
     * @var string
     */
    public $value;

    /**
     * Set to true if the field needs to be saved after the record is created
     * @var boolean
     */
    public $afterSave = false;
    /**
     * Constructor
     * @param string $name
     * @param string $label
     */
    public function __construct($name, $label)
    {
        $this->name = $name;
        $this->label = $label;
    }
    /**
     * Gets the path to twig template
     * @return string
     */
    public function getTemplate()
    {
        return View::make(
            $this->template,
            array(
                "field"=>$this,
                "admin"=>get_class($this->admin)
            )
        )->render();
    }

    /**
     * set the related admin
     * @param CasapianAdmin $admin
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;
    }
    /**
     * Get the value for the form field
     * @return string
     */
    public function getValue()
    {
        if (isset($this->admin->data)) {
            return $this->admin->data[$this->name];
        }

        return null;
    }
    /**
     * Sets the correct value on the model.
     * @param  Eloquent $model
     * @return void
     */
    public function saveTo(&$model)
    {
        $model->{$this->name} = Input::get($this->name);
    }
}
