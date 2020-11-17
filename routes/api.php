<?PHP

Route::add('/users', 'User@getAllUsers', 'get');

Route::add('/users/([0-9]*)', 'User@getUser', 'get');

Route::add('/users', 'User@createUser', 'post');

Route::add('/users/([0-9]*)/update', 'User@updateUser', 'post');

Route::add('/users/([0-9]*)', 'User@deleteUser', 'delete');


?>
