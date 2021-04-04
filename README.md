![Gas Locations](https://raw.githubusercontent.com/nicosli/sr_pago_project/master/public/img/gas_location.png)

## About this project
This app web shows the gas stations in a county and municipality, it shows a map with the gas markers and a table with the results.

## Requirements
Make sure to have this requirement to install the project locally.
```bash
PHP 7.4.2
node 12.16.1
npm 6.13.4
composer 1.7
```

## Install the project locally.
First, clone the project to some folder in the local.
```
git clone https://github.com/nicosli/sr_pago_project.git
```

Install the dependencies of the project. Run this command in the CLI console.
```
composer install
```

If the .env file is not created, then make this file at the root of the project and copy the values of .env.example, and put these vars in the .env file. 
```
MIX_API=http://localhost:8000
MIX_GOOGLE_KEY=AIzaSyDJd_yXAO_Kmuz3j9FqOmydUtK78pWuRBc
API_GAS=https://api.datos.gob.mx
```
Review the database vars in the .env file and put the username, password, port, and database name from your local installation
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

Run this command to generate the key of the project
```
php artisan key:generate
```

Run this command to clear the config cache of the new .env file.
```
php artisan config:cache
```

Run the migration to make the tables in the database.
```
php artisan migrate
```

Download the XML file with Addresses information (Make sure it's the xml file).
```
https://www.correosdemexico.gob.mx/SSLServicios/ConsultaCP/CodigoPostal_Exportar.aspx
```

Save the CPdescarga.xml file in this path of the project.
```
/storage/app/public
```

Run this command in the CLI Console to populate the database. This command read the XML file and put the info in the database. This process can take a long time (around 15 minutes).
```
php artisan XMLtoDataBase
```

The project handles a cache to improve the time response. To generate this cache run this command in the CLI Console.
```
php artisan cache:generate
```

Install dependencies of the package.json and compile the assets to use in the front view.
``` 
npm install && npm run dev
```

To run the project, use this command in the CLI Console
``` 
php artisan serve --port=8000
```

## To do
In order to improve this web application, it is proposed to create a cron to generate the cache every specific time. Similarly, there are areas of opportunity in the user experience on the frontend.

The testing phase is not implemented yet.

Lastly, the project needs to be prepared for deployment to production.

