## Documentación

# Arquitectura

Mercado de monedas es una aplicación de compra-venta de monedas desarrollada en Laravel 8, la última versión del framework de php. Para más información sobre Laravel: https://laravel.com/.

Toda la funcionalidad de autenticación está creada con Laravel Breeze, una herramienta del framework completa que trae toda la lógica de registro y login del usuario, migración de tablas relacionadas, como users, password_resets, etc., validación estándar de registro y login y barra de navegación. Para más información: https://laravel.com/docs/8.x/starter-kits#laravel-breeze.

Su arquitectura sigue el patrón modelo-vista-controlador que ofrece el framework y su diseño es full-stack (back-end + front-end) con programación orientada a objetos.

La base de datos está creada en MySQL. Las tablas creadas específicamente para esta aplicación son: accounts, currencies y saving_boxes. El nombre de la base de datos es pruebafip.

Las librerías externas utilizadas son: Laravel-Fixerio, para consumir la api de tasas de cambio de fixer.io; Sweet-Alert, un paquete para mostrar mensajes al usuario; y Carbon, un paquete de parseo de fechas incorporada a Laravel, para mostrarle al usuario las fechas en su huso horario.

El estilo de la aplición está casi enteramente armado con Tailwind, un framework CSS que permite estilizar los elementos html desde las mismas etiquetas. Para más información: https://tailwindcss.com/. Solo hay algunos detalles entrados en una plantilla costumizada de css, que se compila desde la terminal.

Algunas validaciones y mensajes del lado del usuario están hechas con Javascript.

# Diseño

--Registro--

La aplicación tiene una landing page que le cuenta al usuario para qué sirve y le muestra los accesos al registro y login. 

--Página de bienvenida--

No bien se registra o una vez que el usuario se autentica, se abre el dashboard de la aplicación. Allí el usuario recibe un saludo personalizado y tiene un botón que le indica cuál es el primer paso que debe tomar: crearse una caja en pesos. En el backend, con la creación de la caja en pesos, se crea simultáneamente la cuenta monetaria del usuario, que estará vinculada a todas sus cajas de divisas. Una vez creada la cuenta, verá un mensaje de confirmación que le indica que todo salió bien y se mostrará su caja en pesos con el saldo por default y la última actualización de movimientos. También cambiará la barra de navegación y ahora verá dos botones: Depositar pesos argentinos y abrir una caja en otra moneda. Para simplificar la utilización de los datos, elegí 14 monedas de entre las opciones que maneja Fixer.io, que me parecían ser las que más útiles le resultarían a un usuario en Argentina. Una posibilidad de mejora para la app es agregar un menú para que este pueda crear nuevas monedas según las opciones ofrecidas por la api.

El botón de depositar pesos abre un modal donde el usuario puede entrar una nueva cantidad de dinero y guardar. No bien cierre y vea el mensaje de éxito, se mostrará el saldo actualizado de su caja en pesos.

El botón de abrir caja en otra moneda le permite elegir entre las opciones de la tabla currencies. No podrá crear una nueva caja en una divisa que ya tenga relacionada a su cuenta monetaria. Una vez elegida la moneda y creada la segunda caja, verá la información actualizada pertinente con su nueva caja en otra moneda. Además, aparecerán los botones de COMPRA y VENTA para que pueda comenzar a operar.

--Compra y venta--

La vista de compra y la vista de venta tienen validaciones para que las monedas origen y destino no sean iguales, para que el costo de compra no sea superior al saldo de la moneda compradora, para que la cantidad de para vender no sea superior al saldo de la moneda vendedora y para que se ingrese una cantidad de dinero válida. Los saldos de las cajas se actualizan del lado del usuario, con javascript, según la divisa elegida. Si la operación elegida salió bien, se verá un mensaje de éxito y se redigirá al usuario a la vista del dashboard, donde verá los montos actualizados de sus cuentas y las fechas de operaciones.


# Instrucciones para compilar y ejecutar

1) Clonar el repositorio o guardar la carpeta zipeada.
2) Para poder levantar la aplicación, yo utilicé Xampp, donde seteé los puertos de localhost y mysql. 
2) En app/database, encontrarán un dump de la base de datos. Crear una conexión en un cliente mysql e importar el archivo a un schema para ver las tablas y la información de monedas.
3) Duplicar el archivo .env.example y llamarlo .env. Ahí tendrán que completar los datos de su conexión a mysql y de la base de datos. Allí también verán el endpoint y la access_key de la api de fixerio. Al momento de enviar la prueba, tenía hechas 160 llamadas a la api.
4) Comandos a correr:
 - `npm install` -> para instalar node_modules
 - `composer install` -> para instalar los paquetes de vendor de Laravel
 - `php artisan migrate` -> para correr migraciones 
 - `composer dump-autoload` -> para sumar archivos descriptos allí
 - `php artisan storage:link` -> para vincular la carpeta public con storage y ver las imágenes utilizadas

Para levantar la aplicación, correr `php artisan serve`, que levantará el servidor. Luego, correr `npm run dev` para que complile Laravel-Mix y puedan ver las modificaciones de js y css.

Si tienen alguna duda, por favor, comuníquense conmigo: aldana.michelino@gmail.com o 3424363304.

Muchas gracias por su interés en mi trabajo.
