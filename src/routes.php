<?php

Route::filter('old', function() {
    if (Input::get('age') < 200) {
        return Redirect::to('home');
    }
});
Route::group(array('prefix' => 'casapian'), function(){
    Route::get('/', array(
        'as' => 'casapian_dashboard',
        'uses' => 'SamuelJoos\Casapian\CasapianController@dashboard',
    ));
    Route::get('{admin}/overview', array('as' => 'admin_overview', 'uses' => 'SamuelJoos\Casapian\CasapianController@overview'));
    Route::get('{admin}/edit/{key}', array('as' => 'admin_edit', 'uses' => 'SamuelJoos\Casapian\CasapianController@edit'));
    Route::get('{admin}/create', array('as' => 'admin_create', 'uses' => 'SamuelJoos\Casapian\CasapianController@create'));
    Route::post('{admin}/save', array('as' => 'admin_save', 'uses' => 'SamuelJoos\Casapian\CasapianController@save'));

});
