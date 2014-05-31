<?php namespace SamuelJoos\Casapian\Fields;

use Illuminate\Support\Facades\Input;

class Datetime extends Field
{
    /**
     * template name
     * @var string
     */
    protected $template = "casapian::fields.datetime";
    /**
     * Date time format
     * @var string
     */
    protected $format;
    /**
     * Datetime constructor
     * @param string $name
     * @param string $label
     * @param string $format Datetime format
     */
    public function __construct($name, $label, $format = "d-m-Y H:i:s")
    {
        $this->name = $name;
        $this->label = $label;
        $this->format = $format;
    }
    /**
     * Get time in the correct format
     * @return [type] [description]
     */
    public function getTime()
    {
        $time = null;
        if (isset($this->admin->data)) {
            $time = date($this->format,
                strtotime($this->admin->data[$this->name]));
        }

        return $time;
    }
    /**
     * Get the datetime format
     * @return string
     */
    public function getFormat()
    {
        return $this->phpToJsFormat($this->format);
    }
    /**
     * Converts php datetime format to js datetime format
     * @todo needs a rewrite or other solution
     * @param  string $php_format
     * @return string
     */
    protected function phpToJsFormat($php_format)
    {
        $pattern = array(

            //day
            'd',		//day of the month
            'j',		//3 letter name of the day
            'l',		//full name of the day
            'z',		//day of the year

            //month
            'F',		//Month name full
            'M',		//Month name short
            'n',		//numeric month no leading zeros
            'm',		//numeric month leading zeros

            //year
            'Y', 		//full numeric year
            'y',		//numeric year: 2 digit

            //time,
            'H',
            'i',
            's'
        );
        $replace = array(
            'dd','d','DD','o',
            'MM','M','m','MM',
            'yy','y',
            'HH','mm','ss'
        );
        foreach ($pattern as &$p) {
            $p = '/'.$p.'/';
        }

        return preg_replace($pattern, $replace, $php_format);
    }
    /**
     * Method overide from Fields class: converts datetime to mysql timestamp
     * @param  Eloquent $model
     * @return void
     */
    public function saveTo(&$model)
    {
        $input = Input::get($this->name);
        if (!empty($input)) {
            $date = date_create_from_format($this->format, $input);
            $model->{$this->name} = date_format($date, "Y-m-d H:i:s");
        }
    }
}
