## Descargar proyecto

git clone https://github.com/rtro-dev/User_Administration_App.git

sudo chown -R user:www-data User_Administration_App/  
sudo chmod -R 775 User_Administration_App/

cd userApp/

sudo composer require laravel/ui

sudo composer install

Crear base de datos y usuario

Actualizar el .env

php artisan migrate

php artisan ui:auth