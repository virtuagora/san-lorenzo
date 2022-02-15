![alt text](/docs/header.png "Logo Virtuagora")

# Presupuesto Participativo de San Lorenzo
*By [Virtuágora](https://virtuagora.org). With the collaboration of [Democracia en Red](https://democraciaenred.org) & SantaLab (Laboratorio de participación ciudadana de Santa Fe)*

Maintained by 
- Guillermo Croppi / Twitter [@guillermocroppi]([https://twitter.com/guillermocroppi]) / Github [@guillecro](https://github.com/guillecro)
- Augusto Mathurin / Twitter [@augusthur]([https://twitter.com/augusthur]) / Github [@augusthur](https://github.com/augusthur)

![image](https://user-images.githubusercontent.com/8771166/153970264-32933c98-d6ae-4ada-8344-0898c4191789.png)

### Note

> **NOTE 1:** Currently only available in Spanish.

> **NOTE 2:** This repository is an example of one of the upcoming features for Virtuagora 2.0. It's a portal that support a whole public budget process were citizens can contribute with projects, go through a voting period, and get the results.

# Status del proyecto

> Actualmente el mantenimiento del repositorio lo hace [Virtuágora](https://virtuagora.org).

# Acerca de

La plataforma de Presupuesto Participativo del Municipio de San Lorenzo de la provincia de Santa Fe, Argentina. El comenzó desde el año 2018 y hasta el año 2019 se ha ido agregando distintos features que fueron permitiendo. 

Ademas de la creacion de proyectos, es la primera vez para el equipo de Virtuagora desarrollar una plataforma que permite el *voto privado*, donde se desasocia todo tipo de informacion de un ciudadano con su voto.

La plataforma de Presupuesto Participativo de San Lorenzo fue un proyecto de codigo abierto desarrollado por [Virtuágora](https://virtuagora.org) hecho en conjunto con la colaboracion de [Democracia en Red](https://democraciaenred.org) y SantaLab (Laboratorio de participación ciudadana de Santa Fe)* para el [Municipio de San Lorenzo](https://sanlorenzo.gob.ar/) de la Provincia de Santa Fe, Argentina

### Features

- Panel de control del administrador - Cuenta con uan serie de funcionalidades como:
  - Creación, edición, y gestión de los proyectos, como definicion de sentencia de factibilidad
  - Gestión del padrón de ciudadanos
  - Gestión de la plataforma.
- Subida de proyectos, tanto por ciudadanos como por administradores
- Bitacora de cambios en los proyectos: Cada cambio se mantiene registrado, especificando si fue la administración o el ciudadano creador dle proyecto
- Padrón online - Los usuarios pueden validarse contra el padrón, durante el registro, o posteriormente registrado ingresando al ciudadano en el padron en el panel de control.
- Varios metodos de votación:
- Dependiendo del estado del presupuesto, la plataforma se adapta:
  - Pre-subida de propuestas: Anuncio de la fecha de, exploracion de otra secciones como preguntas frecuentes, etc.
  - Subida de propuestas: Etapa para la subida de propuestas y cuando se define la sentencia de factibilidad (con Fecha de inicio y fecha de cierre del formulario)
  - Pre-votacion: 
  - Votacion: En sus modalidades web (Online - Solo con padron) / Tablet (Presencial) / Whatsapp (Presencial) / Boleta Fisica (Presencial - Soporte en papel - A cargo de la administracion). Con respecto al padron, solamente se deja constancia en el padron que la persona votó y tambien se puede llevar estadisticas dia a dia de la votación (No los votos, sino el tipo de votacion, ademas de otros datos). Tambien se puede tomar snapshots de la base de datos de los votos y sumarlos a la cadena de [Blockchain Federal Argentina](https://bfa.ar/)
  - Pre-resultados: Etapa donde se suben las boletas en papel (de haber), se realiza el escrutinio, y se eligen los ganadores.
  - Resultados: Se enfoca en mostrar los resultados de la votacion en la web, con el escrutinio hecho desde el sistema

# Requisitos

- Slim Framework 3 (PHP +7.0)
- Eloquent (ORM de DB)
- MySQL
- VueJS +2
- Laravel-mix  (Node +10 y NPM +6) para development y build

# Development

Ir a la carpeta `/app` y hacer una copia de `settings.php.dist` con nombre `settings.php`

```
$ cd app/
$ cp settings.php.dist settings.php
```

Editar `settings.php` y configurar `swiftmailer` y `eloquent`. Para `google` es necesario que obtenga una KEY de Google reCaptcha para, y por ultimo, `facebook` para el sharer de Facebook

```
$ nano settings.php
```

```
'swiftmailer' => [
    'transport' => 'smtp',
    'options' => [
        'host' => 'some.smtp.url.com',
        'port' => 25,
        'username' => 'aaaaaaaaaaaa',
        'password' => 'bbbbbbbbbbbb',
        'security' => 'tls',
        'sender' => ['myorganization@gmail.com' => "My Organization"]
    ],
],
'eloquent' => [
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'database' => 'my_database',
    'username' => 'user',
    'password' => 'pass',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_general_ci',
    'prefix' => '',
],
'facebook' => [
    'app_id' => '11111111111111',
    'app_secret' => '111111111111111111111111111111111111',
    'default_graph_version' => 'v2.12',
],
'recaptcha' => [
    'public_key' => 'aaaaaaaaa5555555555555555555555555555555',
    'secret_key' => 'aaaaaaaaa6666666666666666666666666666666',
],

```

Instalar dependencias con composer y npm 

```
$ composer install
$ npm install
```

Levantar un web server con php 

```
$ cd public
$ php -S 0.0.0.0:8000 -t .
```

A continuación hay que instalar la base de dato. Para eso se corre un script ya preparado pero se debe hacer un `GET http://localhost:8000/install`

Antes de hacerlo, hay que ir a `app/routes.php` y descomentar la siguiente linea:

```
$app->get('/install[/{extra}]', 'miscAction:runInstall');
```

Guardar el archivo y hacer el `GET` a [http://localhost:8000/install](http://localhost:8000/install)

El servidor responderá con un mensaje de exito. Si ocurre un error, se guardará un log en `var/logs/app.log`.

### Acerca de Laravel Mix - VueJS - Sass

Laravel Mix funciona como una interfaz contra webpack para comprimir css y js en un solo archivo. Tambien asiste al build de los archivos .scss y .vue en un archivo .css y .js compacto y listo para producción (sea asi el caso).

Si desea hacer algun cambio en la configuracion puede revisar `webpack.mix.js` al igual que la [Documentacion de Laravel Mix](https://laravel.com/docs/6.x/mix).

### Styles - SCSS - CSS

El archivo `public/assets/css/virtuagora.css` se buildea a partir de lo que se define en el archivo `app/stylesheet/virtuagora.scss`. Debe estar familiarizado a la estructura de los archivos SASS / SCSS

La Webapp utiliza BulmaCSS como framework CSS y Buefy por el lado de componentes reactivos.

Recomendamos ver la documentacion acerca de *customization* en (Bulma)[https://bulma.io/documentation/customize/variables/] y en (Buefy)[https://buefy.org/documentation/customization/] (Verificar las versiones de ambos en `package-lock.json`)

> *NOTA: En los componentes .vue se puede utilizar `<style lang="scss" scoped>` para estilar los componentes.*

### Componentes reactivos - VueJS

Los archivos estan todos bajo el directiorio `app/components`. Dentro de la carpeta esta todo lo referido a los componentes reactivos hechos en VueJS siendo el main file el archivo `virtuagora.js`. Debe estar familiarizado con la estructura de Single File Components de VueJS.

Durante development, puede buildear los componentes utilizando el build-on-save que provee Laravel Mix. Por cada cambio que haga, Laravel Mix buildear el archivo `public/assets/js/virtuagora.js`.

Para eso llamar a la rutina `watch`

```
npm run watch
```

Pero recuerde: No suba a produccion ni a github el build de development. **SIEMPRE** haga un build de produccion antes de pushear todo cambio. Para eso:

```
npm run prod
```


# Deploy a Produccion

Previo a subir a produccion, revisar que el build de los assets haya sido en modo produccion:

```
npm run prod
```

Se puede utilizar Apache como Web Server. Es requerido tener instalado MySQL y PHP (No asi Node, ya que los assets fueron buildeados para produccion)

Se debe crear un virtual host para el puerto `:80`. Preferentemente luego levantar un certificado SSL para encriptar. Se puede utilizar Lets Encrypt tranquilamente.

Descargar el repositorio en el servidor `/var/www`

Seguir los mismos pasos que en development con las siguientes consideraciones:

Una vez que se configuro `settings.php`, dejar la variable en `'mode' => 'prod'`

```
'settings' => [
    'mode' => 'prod',
    ...
]
```
Instalar la base de datos. Seguir el mismo procedimiento que en development ir a `app/routes.php` y descomentar la siguiente linea:

```
$app->get('/install[/{extra}]', 'miscAction:runInstall');
```

Luego hacer `GET http://midominio.com/install`. 

> *NOTA: Si siguió el proceso de descomentar la linea del codigo dentro de `app/routes.php` **No se olvide de deshabilitarlo comentando la linea de código***

> *NOTA: Puede evitar hacer el procedimiento anterior si importa una base de dato ya estructurada al MySQL de producción*
