<?php namespace SamuelJoos\Casapian;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\File\File as SFile;
use Illuminate\Support\Facades\Validator as LValidator;

class CasapianController extends Controller
{
    /**
     * CasapianController constructor
     */
    public function __construct()
    {
    }
    /**
     * Convert camelcase to slug
     * @param string $str
     * @return string
     */
    private function camelToSlug($str)
    {
        return preg_replace_callback(
            '/[A-Z]/',
            create_function('$match', 'return "-" . strtolower($match[0]);'),
            $str
        );
    }
    /**
     * Conver slug to camelcase
     * @param  string $str
     * @return string
     */
    private function slugToCamel($str)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $str)));
    }

    public function dashboard()
    {
        $slug = $this->SlugToCamel(Config::get("casapian::casapian.home"));
        return $this->overview($slug);
    }
    /**
     * Overview action
     * @param  CasapianAdmin $admin
     * @return Response
     */
    public function overview ($admin)
    {
        $camel = $this->SlugToCamel($admin);
        $admin = new $camel;
        return $admin->overview();
    }
    /**
     * Create action
     * @param  CasapianAdmin $admin
     * @return Response
     */
    public function create($admin)
    {
        $camel = $this->SlugToCamel($admin);
        $admin = new $camel;
        return $admin->create();
    }
    /**
     * Edit action
     * @param  CasapianAdmin $admin
     * @param  int $key   primarykey
     * @return Response
     */
    public function edit($admin, $key)
    {
        $camel = $this->SlugToCamel($admin);
        $admin = new $camel;
        return $admin->edit($key);
    }
    /**
     * Save action
     * @param  CasapianAdmin $admin
     * @return Response
     */
    public function save($admin)
    {
        $camel = $this->SlugToCamel($admin);
        $admin = new $camel;
        return $admin->save();
    }
}
