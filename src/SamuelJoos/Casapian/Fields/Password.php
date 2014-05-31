<?php namespace SamuelJoos\Casapian\Fields;

use Illuminate\Support\Facades\Input;

class Password extends Field
{
    /**
     * template name
     * @var string
     */
    protected $template = "casapian::fields.password";
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
