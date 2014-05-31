<?php namespace SamuelJoos\Casapian\Fields;

use Illuminate\Support\Facades\Input;

class Choice extends Field
{
    /**
     * template name
     * @var string
     */
    protected $template = "casapian::fields.choice";
    protected $choices = array();

    public function __construct($name, $label, $choices)
    {
        $this->name = $name;
        $this->label = $label;
        $this->choices = $choices;
    }

    public function getChoices()
    {
        return $this->choices;
    }

    public function getValue()
    {
        return $this->value;
    }
    /**
     * Method overide from Fields class: doesn't set a value if empty
     * @param  Eloquent $model
     * @return void
     */
    public function saveTo(&$model)
    {
        $input = Input::get($this->name);
        if (!empty($input)) {
            $model->{$this->name} = $input;
        }
    }
}
