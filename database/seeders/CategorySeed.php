<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\GroupCategory;
use App\Models\SubCategory;

class CategorySeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //grupos:
        GroupCategory::truncate();
        Category::truncate();
        SubCategory::truncate();
        
        GroupCategory::create(['name'=>'Restauración', 'post_type_id' => 3, 'status'=> 1]);

            Category::create(['name' => 'Sala', 'featured'=>1,'group_id'=>1,'icon'=>'images/categories/casa.svg']);

                SubCategory::create(['name' => 'Jefe/a Restaurante o Sala/ maître','status'=>1,'category_id'=>1]);
                SubCategory::create(['name' => 'Jefe de Sector o Rango','status'=>1,'category_id'=>1]);
                SubCategory::create(['name' => 'Camarero/a','status'=>1,'category_id'=>1]);
                SubCategory::create(['name' => 'Ayudante/a Camarero Runner ','status'=>1,'category_id'=>1]);
                SubCategory::create(['name' => 'Barman/Barwoman','status'=>1,'category_id'=>1]);
                SubCategory::create(['name' => 'Sumiller/a','status'=>1,'category_id'=>1]);
                SubCategory::create(['name' => 'Barista','status'=>1,'category_id'=>1]);
                SubCategory::create(['name' => 'Coctelero / Mixologo','status'=>1,'category_id'=>1]);
                SubCategory::create(['name' => 'Auxiliar ( Hostess, Atención al cliente, entre otros )','status'=>1,'category_id'=>1]);

            Category::create(['name' => 'Cocina', 'group_id'=>1]);

                SubCategory::create(['name' => 'Jefe/a de Cocina','status'=>1,'category_id'=>2]);
                SubCategory::create(['name' => 'Segundo/a Jefe/a Cocina','status'=>1,'category_id'=>2]);
                SubCategory::create(['name' => 'Cocinero/a','status'=>1,'category_id'=>2]);
                SubCategory::create(['name' => 'Ayudante/a Cocina','status'=>1,'category_id'=>2]);
                SubCategory::create(['name' => 'Pastelero o Repostero/a','status'=>1,'category_id'=>2]);
                SubCategory::create(['name' => 'Panadero ','status'=>1,'category_id'=>2]);
                SubCategory::create(['name' => 'Repartidor/a a domicilio','status'=>1,'category_id'=>2]);
                SubCategory::create(['name' => 'Auxiliar cocina','status'=>1,'category_id'=>2]);

            Category::create(['name' => 'Mandos Directivos', 'group_id'=>1,'icon'=>'images/categories/liderazgo.svg']);

                SubCategory::create(['name' => 'Director','status'=>1,'category_id'=>3]);
                SubCategory::create(['name' => 'Gerente de Centro','status'=>1,'category_id'=>3]);
                SubCategory::create(['name' => 'Encargado','status'=>1,'category_id'=>3]);
                SubCategory::create(['name' => 'Encargado de Economato','status'=>1,'category_id'=>3]);

        GroupCategory::create(['name'=>'Hoteles', 'post_type_id' => 3,'status'=>1]);
        
            Category::create(['name' => 'Recepción', 'featured'=>1,'group_id'=>2,'icon'=>'images/categories/campana.svg']);

                SubCategory::create(['name' => 'Jefe/a de Recepción','status'=>1,'category_id'=>4]);
                SubCategory::create(['name' => 'Recepcionista','status'=>1,'category_id'=>4]);
                SubCategory::create(['name' => 'Ayudante de Recepción','status'=>1,'category_id'=>4]);
                SubCategory::create(['name' => 'Conserje','status'=>1,'category_id'=>4]);
                SubCategory::create(['name' => 'Relaciones Públicas','status'=>1,'category_id'=>4]);
                SubCategory::create(['name' => 'Telefonista','status'=>1,'category_id'=>4]);

            Category::create(['name' => 'Pisos y Limpieza', 'featured'=>1,'group_id'=>2,'icon'=>'images/categories/productos-de-limpieza.svg']);

                SubCategory::create(['name' => 'Gobernanta de Hotel','status'=>1,'category_id'=>5]);
                SubCategory::create(['name' => 'Camarero/a Pisos','status'=>1,'category_id'=>5]);
                SubCategory::create(['name' => 'Auxiliar Pisos y Limpieza','status'=>1,'category_id'=>5]);

            Category::create(['name' => 'Mantenimiento y Auxiliar', 'group_id'=>2,'icon'=>'images/categories/mantenimiento.svg']);

                SubCategory::create(['name' => 'Técnico/a Servicio','status'=>1,'category_id'=>6]);
                SubCategory::create(['name' => 'Especialista Servicio, Deporte, Animación, Spa y Similares','status'=>1,'category_id'=>6]);
                SubCategory::create(['name' => 'Encargado/a de Mantenimiento y Servicios Auxiliares','status'=>1,'category_id'=>6]);
                SubCategory::create(['name' => 'Auxiliar Mantenimiento y Servicios Auxiliares','status'=>1,'category_id'=>6]);

            Category::create(['name' => 'Comercial,Administrativo y Gestión', 'group_id'=>2]);
        
                SubCategory::create(['name' => 'Jefe/a Comercial','status'=>1,'category_id'=>7]);
                SubCategory::create(['name' => 'Comercial','status'=>1,'category_id'=>7]);
                SubCategory::create(['name' => 'Jefe/a Administración','status'=>1,'category_id'=>7]);
                SubCategory::create(['name' => 'Administrativo/a','status'=>1,'category_id'=>7]);
                SubCategory::create(['name' => 'Economato','status'=>1,'category_id'=>7]);

        GroupCategory::create(['name'=>'Catering', 'post_type_id' => 3,'status'=>1]);

            Category::create(['name' => 'Jefe/a de Catering o colectividades', 'group_id'=>3]);

                SubCategory::create(['name' => 'Jefe/a de sala de catering','status'=>1,'category_id'=>8]);
                SubCategory::create(['name' => 'Supervisor de Catering o Colectividades','status'=>1,'category_id'=>8]);

            Category::create(['name' => 'Ayudante de Catering', 'group_id'=>3]);
            Category::create(['name' => 'Auxiliar de Catering', 'group_id'=>3]);

                SubCategory::create(['name' => 'Preparador/a o montador/a de catering','status'=>1,'category_id'=>10]);
                SubCategory::create(['name' => 'Auxiliar preparador/a o montador/a de catering','status'=>1,'category_id'=>10]);

        // GroupCategory::create(['name'=>'Proveedores o Distribuidores', 'post_type_id' => 1,'status'=>1]);

        GroupCategory::create(['name' => 'Distribuidor de Bebidas','status'=>1,     'post_type_id'=>4]);//20
        GroupCategory::create(['name' => 'Distribuidor de Alimentos','status'=>1,   'post_type_id'=>4]);
        GroupCategory::create(['name' => 'Distribuidor Materiales (vajilla, cristalería, menaje cocina, textil )','status'=>1,'post_type_id'=>4]);
        GroupCategory::create(['name' => 'Distribuidor de Equipamiento y Maquinaria','status'=>1,'post_type_id'=>4]);
        GroupCategory::create(['name' => 'Distribuidor Mobiliario','status'=>1,'post_type_id'=>4]);
        GroupCategory::create(['name' => 'Distribuidor de Equipos Informáticos','status'=>1,'post_type_id'=>4]);

        // GroupCategory::create(['name'=>'Negocios', 'post_type_id' => 3,'status'=>1]);

        GroupCategory::create(['name' => 'Traspasos','status'=>1,'post_type_id'=>5]);
        GroupCategory::create(['name' => 'Alquiler de Negocios','status'=>1,'post_type_id'=>5]);
        GroupCategory::create(['name' => 'Franquicias','status'=>1,'post_type_id'=>5]);
        GroupCategory::create(['name' => 'Mayoristas','status'=>1,'post_type_id'=>5]);
        GroupCategory::create(['name' => 'Venta de Empresas','status'=>1,'post_type_id'=>5]);
        GroupCategory::create(['name' => 'Financiación','status'=>1,'post_type_id'=>5]);
        
        // GroupCategory::create(['name' => 'Mobiliario de Hostelería','status'=>1,'post_type_id'=>5]);

        // GroupCategory::create(['name'=>'Servicios', 'post_type_id' => 1,'status'=>1]);

            GroupCategory::create(['name' => 'Mantenimiento','status'=>1,'post_type_id'=>1]);//16

                Category::create(['name' => 'Obras y Reformas', 'group_id'=>16]);
                Category::create(['name' => 'Electricidad', 'group_id'=>16]);
                Category::create(['name' => 'Pintura', 'group_id'=>16]);
                Category::create(['name' => 'Fontanería y Saneamiento', 'group_id'=>16]);
                Category::create(['name' => 'Calefacción, Climatización y Gas', 'group_id'=>16]);
                Category::create(['name' => 'Carpintería de madera', 'group_id'=>16]);
                Category::create(['name' => 'Carpintería Metálica y Ventanas', 'group_id'=>16]);
                Category::create(['name' => 'Toldos, Persianas y Mosquiteras', 'group_id'=>16]);
                Category::create(['name' => 'Cerrajería', 'group_id'=>16]);
                Category::create(['name' => 'Limpieza y Control de Plagas', 'group_id'=>16]);
                Category::create(['name' => 'Desatascos', 'group_id'=>16]);
                Category::create(['name' => 'Prevención Contraincendios', 'group_id'=>16]);
                Category::create(['name' => 'Jardinero/a', 'group_id'=>16]);
                Category::create(['name' => 'Manitas', 'group_id'=>16]);

            GroupCategory::create(['name' => 'Administrativo','status'=>1,'post_type_id'=>1]);//17

                Category::create(['name' => 'Fiscal', 'group_id'=>17]);
                Category::create(['name' => 'Seguros', 'group_id'=>17]);
                Category::create(['name' => 'Laboral Contable', 'group_id'=>17]);
                Category::create(['name' => 'Asesoría jurídica', 'group_id'=>17]);
                Category::create(['name' => 'Gestión de licencias', 'group_id'=>17]);
                Category::create(['name' => 'Financiación y subvenciones', 'group_id'=>17]);
                Category::create(['name' => 'Calidad de seguridad alimentaria', 'group_id'=>17]);

            GroupCategory::create(['name' => 'Marketing y RRPP','status'=>1,'post_type_id'=>1]);
            GroupCategory::create(['name' => 'Servicio de Delivery','status'=>1,'post_type_id'=>1]);
            GroupCategory::create(['name' => 'Servicio de catering','status'=>1,'post_type_id'=>1]);
            GroupCategory::create(['name' => 'Consultoría y Formación','status'=>1,'post_type_id'=>1]);
            GroupCategory::create(['name' => 'Energía / Eficiencia Energética','status'=>1,'post_type_id'=>1]);
            GroupCategory::create(['name' => 'Diseño / Impresión Gráfica /Digitalización','status'=>1,'post_type_id'=>1]);
            GroupCategory::create(['name' => 'Técnico/a de Prevención de Riesgos Laborales','status'=>1,'post_type_id'=>1]);//19        

            // GroupCategory::create(['name'=>'Productos', 'post_type_id' => 2,'status'=>1]);
            GroupCategory::create(['name'=>'Barra y Sala', 'post_type_id' => 2,'status'=>1]);//25

                Category::create(['name' => 'Barra', 'group_id'=>25]);//32

                    SubCategory::create([ 'name' => 'Consumibles', 'category_id'=> 32 ]);
                    SubCategory::create([ 'name' => 'Bandejas', 'category_id'=> 32 ]);
                    SubCategory::create([ 'name' => 'Preparación cocteles', 'category_id'=> 32 ]);
                    SubCategory::create([ 'name' => 'Contenedores y dispensadores', 'category_id'=> 32 ]);

                Category::create(['name' => 'Utensilios de Servicio', 'group_id'=>25]);//33

                    SubCategory::create([ 'name' => 'Sacacorchos y abrebotellas', 'category_id'=> 33 ]);
                    SubCategory::create([ 'name' => 'Dosificadores', 'category_id'=> 33 ]);
                    SubCategory::create([ 'name' => 'Pinzas', 'category_id'=> 33 ]);
                    SubCategory::create([ 'name' => 'Porta facturas', 'category_id'=> 33 ]);
                    SubCategory::create([ 'name' => 'Picadora de hielo', 'category_id'=> 33 ]);
                    SubCategory::create([ 'name' => 'Dispensador de bebidas', 'category_id'=> 33 ]);

                Category::create(['name' => 'Té y Cafetería', 'group_id'=>25]);
                Category::create(['name' => 'Cubertería', 'group_id'=>25]);
                Category::create(['name' => 'Vajillas', 'group_id'=>25]);
                Category::create(['name' => 'Cristaleria', 'group_id'=>25]);
                Category::create(['name' => 'Manteleria', 'group_id'=>25]);
                Category::create(['name' => 'Presentación de Mesa', 'group_id'=>25]);//39
                
                    SubCategory::create([ 'name' => 'Aceiteros / vinagreras', 'category_id'=> 39 ]);
                    SubCategory::create([ 'name' => 'Condimentos', 'category_id'=> 39 ]);
                    SubCategory::create([ 'name' => 'Servicio del vino, champaneras/cubiteras', 'category_id'=> 39 ]);
                    SubCategory::create([ 'name' => 'Servilleteros / Ceniceros', 'category_id'=> 39 ]);
                    SubCategory::create([ 'name' => 'Paneras', 'category_id'=> 39 ]);

        GroupCategory::create(['name'=>'Cocina', 'post_type_id' => 2,'status'=>1]);//26

            Category::create(['name' => 'Utensilios de Cocina', 'group_id'=>26]);//40
            Category::create(['name' => 'Baterías de Cocina', 'group_id'=>26]);//41

                SubCategory::create([ 'name' => 'Juego de cazuelas', 'category_id'=> 41 ]);
                SubCategory::create([ 'name' => 'Olla sopera', 'category_id'=> 41 ]);
                SubCategory::create([ 'name' => 'Series de cazuelas/ollas', 'category_id'=> 41 ]);
                SubCategory::create([ 'name' => 'Cazuelas para asar', 'category_id'=> 41 ]);
                SubCategory::create([ 'name' => 'Cazuelas para pescado', 'category_id'=> 41 ]);
                SubCategory::create([ 'name' => 'Cazos con mango', 'category_id'=> 41 ]);
                SubCategory::create([ 'name' => 'Tapaderas para cazuelas/ollas', 'category_id'=> 41 ]);
                SubCategory::create([ 'name' => 'Sartenes', 'category_id'=> 41 ]);
                SubCategory::create([ 'name' => 'Accesorios para cazuelas/ollas', 'category_id'=> 41 ]);

            Category::create(['name' => 'Utensilios de Repostería', 'group_id'=>26]);//42

                SubCategory::create([ 'name' => 'Bandejas de horno', 'category_id'=> 42 ]);
                SubCategory::create([ 'name' => 'Whipper & embudos de fondant', 'category_id'=> 42 ]);
                SubCategory::create([ 'name' => 'Moldes repostería', 'category_id'=> 42 ]);
                SubCategory::create([ 'name' => 'Raspador & pincel de pastelería', 'category_id'=> 42 ]);
                SubCategory::create([ 'name' => 'Aros de postre & de tarta', 'category_id'=> 42 ]);
                SubCategory::create([ 'name' => 'Espumadera', 'category_id'=> 42 ]);

            Category::create(['name' => 'Utensilios para Pizza', 'group_id'=>26]);//43

                SubCategory::create([ 'name' => 'Cajas para pizzas', 'category_id'=> 43 ]);
                SubCategory::create([ 'name' => 'Bandejas para pizza', 'category_id'=> 43 ]);
                SubCategory::create([ 'name' => 'Corta pizzas', 'category_id'=> 43 ]);
                SubCategory::create([ 'name' => 'Palas para pizzas', 'category_id'=> 43 ]);

        GroupCategory::create(['name'=>'Carros de Transporte y Almacenaje', 'post_type_id' => 2,'status'=>1]);//27

            Category::create(['name' => 'Carros para Bandejas', 'group_id'=>27]);
            Category::create(['name' => 'Carros Calientes', 'group_id'=>27]);
            Category::create(['name' => 'Carros Porta-Platos', 'group_id'=>27]);
            Category::create(['name' => 'Carros de Servicio', 'group_id'=>27]);
            Category::create(['name' => 'Contenedores-Transporte', 'group_id'=>27]);
            Category::create(['name' => 'Cubetas GN', 'group_id'=>27]);
            Category::create(['name' => 'Recipiente de Almacenaje', 'group_id'=>27]);//50

        GroupCategory::create(['name'=>'Mobiliario', 'post_type_id' => 2,'status'=>1]);//28

            Category::create(['name' => 'Restaurante y Bar', 'group_id'=>28]);//51

                SubCategory::create([ 'name' => 'Sillas y sillones Hostelería', 'category_id'=> 51 ]);
                SubCategory::create([ 'name' => 'Mesas hostelería', 'category_id'=> 51 ]);
                SubCategory::create([ 'name' => 'Taburetes de bar', 'category_id'=> 51 ]);
                SubCategory::create([ 'name' => 'Menús y Pizarras', 'category_id'=> 51 ]);
                SubCategory::create([ 'name' => 'Postes separadores y barreras', 'category_id'=> 51 ]);
                SubCategory::create([ 'name' => 'Estufas de exterior', 'category_id'=> 51 ]);
                SubCategory::create([ 'name' => 'Sombrillas Hostelería', 'category_id'=> 51 ]);

            Category::create(['name' => 'Vitrinas Expositoras', 'group_id'=>28]);//52

                SubCategory::create([ 'name' => 'Vitrinas de Sobremesa', 'category_id'=> 52 ]);
                SubCategory::create([ 'name' => 'Vitrinas para Heladería', 'category_id'=> 52 ]);
                SubCategory::create([ 'name' => 'Vitrinas Verticales', 'category_id'=> 52 ]);
                SubCategory::create([ 'name' => 'Vitrina de Congelados', 'category_id'=> 52 ]);
                SubCategory::create([ 'name' => 'Vitrina para Carnicería', 'category_id'=> 52 ]);
                SubCategory::create([ 'name' => 'Vitrina para Pastelería', 'category_id'=> 52 ]);
                SubCategory::create([ 'name' => 'Vitrina para Hostelería', 'category_id'=> 52 ]);
                SubCategory::create([ 'name' => 'Vitrina Autoservicio', 'category_id'=> 52 ]);
                SubCategory::create([ 'name' => 'Vitrina para encastrar', 'category_id'=> 52 ]);

            Category::create(['name' => 'Mobiliario de Acero Inoxidable', 'group_id'=>28]);//53

                SubCategory::create([ 'name' => 'Estanterías', 'category_id'=> 53 ]);
                SubCategory::create([ 'name' => 'Mesas de trabajo & accesorios', 'category_id'=> 53 ]);
                SubCategory::create([ 'name' => 'Armarios de pared', 'category_id'=> 53 ]);
                SubCategory::create([ 'name' => 'Campana extractora', 'category_id'=> 53 ]);

            Category::create(['name' => 'Eventos', 'group_id'=>28]);//54
            Category::create(['name' => 'Rcepción', 'group_id'=>28]);
            Category::create(['name' => 'Hotel', 'group_id'=>28]);
            Category::create(['name' => 'Terrazas', 'group_id'=>28]);
            Category::create(['name' => 'Playa y Piscina', 'group_id'=>28]);//58

        GroupCategory::create(['name'=>'Maquinaria', 'post_type_id' => 2,'status'=>1]);//29

            Category::create(['name' => 'Maquinaria de Lavado', 'group_id'=>29]);//59

                SubCategory::create([ 'name' => 'Lavavajillas', 'category_id'=> 59 ]);
                SubCategory::create([ 'name' => 'Lavavasos', 'category_id'=> 59 ]);
                SubCategory::create([ 'name' => 'Lavautensilios', 'category_id'=> 59 ]);
                SubCategory::create([ 'name' => 'Mesas de lavado', 'category_id'=> 59 ]);
                SubCategory::create([ 'name' => 'Cestas y accesorios de lavado', 'category_id'=> 59 ]);

            Category::create(['name' => 'Cocción y Calor', 'group_id'=>29]);//60

                SubCategory::create([ 'name' => 'Hornos industriales', 'category_id'=> 60 ]);
                SubCategory::create([ 'name' => 'Cocina Modular ', 'category_id'=> 60 ]);
                SubCategory::create([ 'name' => 'Asadores de pollos', 'category_id'=> 60 ]);
                SubCategory::create([ 'name' => 'Baño maría', 'category_id'=> 60 ]);
                SubCategory::create([ 'name' => 'Barbacoas', 'category_id'=> 60 ]);
                SubCategory::create([ 'name' => 'Cocedores a Baja Temperatura', 'category_id'=> 60 ]);
                SubCategory::create([ 'name' => 'Freidoras', 'category_id'=> 60 ]);
                SubCategory::create([ 'name' => 'Gratinadores, grill', 'category_id'=> 60 ]);
                SubCategory::create([ 'name' => 'Kebab', 'category_id'=> 60 ]);
                SubCategory::create([ 'name' => 'Mesas calientes', 'category_id'=> 60 ]);
                SubCategory::create([ 'name' => 'Lámparas calentadoras', 'category_id'=> 60 ]);
                SubCategory::create([ 'name' => 'Paelleros', 'category_id'=> 60 ]);
                SubCategory::create([ 'name' => 'Planchas', 'category_id'=> 60 ]);

            Category::create(['name' => 'Frío y Refrigeración', 'group_id'=>29]);//61

                SubCategory::create([ 'name' => 'Arcón Congelador', 'category_id'=> 61 ]);
                SubCategory::create([ 'name' => 'Congeladores Industriales', 'category_id'=> 61 ]);
                SubCategory::create([ 'name' => 'Nevera expositora', 'category_id'=> 61 ]);
                SubCategory::create([ 'name' => 'Neveras Industriales', 'category_id'=> 61 ]);
                SubCategory::create([ 'name' => 'Congelador expositor', 'category_id'=> 61 ]);
                SubCategory::create([ 'name' => 'Abatidor de temperatura', 'category_id'=> 61 ]);
                SubCategory::create([ 'name' => 'Bajo mostrador y mesas', 'category_id'=> 61 ]);
                SubCategory::create([ 'name' => 'Máquina de hielo', 'category_id'=> 61 ]);
                SubCategory::create([ 'name' => 'Botellero frigorífico', 'category_id'=> 61 ]);
                SubCategory::create([ 'name' => 'Vinoteca', 'category_id'=> 61 ]);
                SubCategory::create([ 'name' => 'Armarios refrigerados', 'category_id'=> 61 ]);
                SubCategory::create([ 'name' => 'Cámaras frigoríficas', 'category_id'=> 61 ]);
                SubCategory::create([ 'name' => 'Armarios de maduración de carne', 'category_id'=> 61 ]);
                SubCategory::create([ 'name' => 'Heladeras', 'category_id'=> 61 ]);
                SubCategory::create([ 'name' => 'Fabricador de hielo', 'category_id'=> 61 ]);
                SubCategory::create([ 'name' => 'Escarchador de copas y vasos', 'category_id'=> 61 ]);

            Category::create(['name' => 'Aparatos de Cocina', 'group_id'=>29]);//62

                SubCategory::create([ 'name' => 'Batidora', 'category_id'=> 62 ]);
                SubCategory::create([ 'name' => 'Tostadoras', 'category_id'=> 62 ]);
                SubCategory::create([ 'name' => 'Salamandras', 'category_id'=> 62 ]);
                SubCategory::create([ 'name' => 'Microondas', 'category_id'=> 62 ]);
                SubCategory::create([ 'name' => 'Máquinas al vacío', 'category_id'=> 62 ]);
                SubCategory::create([ 'name' => 'Máquina para hacer cubitos de hielo & trituradora', 'category_id'=> 62 ]);
                SubCategory::create([ 'name' => 'Cúter', 'category_id'=> 62 ]);
                SubCategory::create([ 'name' => 'Hervidores de agua caliente', 'category_id'=> 62 ]);
                SubCategory::create([ 'name' => 'Gofreras/ Creperas', 'category_id'=> 62 ]);        

        GroupCategory::create(['name'=>'Alimentación', 'post_type_id' => 2,'status'=>1]);//30

            Category::create(['name' => 'Aceites,Vinagres y Aderezos', 'group_id'=>30]);//63
            Category::create(['name' => 'Aceitunas y Encurtidos', 'group_id'=>30]);
            Category::create(['name' => 'Aperitivos,Snacks y Frutos Secos', 'group_id'=>30]);
            Category::create(['name' => 'Cremas,Caldos y Purés', 'group_id'=>30]);
            Category::create(['name' => 'Especias,Condimentos y Salsas', 'group_id'=>30]);
            Category::create(['name' => 'Embutidos', 'group_id'=>30]);
            Category::create(['name' => 'Pasta y Arroces', 'group_id'=>30]);
            Category::create(['name' => 'Legumbres', 'group_id'=>30]);
            Category::create(['name' => 'Conservas', 'group_id'=>30]);
            Category::create(['name' => 'Repostería y Dulces', 'group_id'=>30]);
            Category::create(['name' => 'Harinas,Levaduras y Espesantes', 'group_id'=>30]);//73

        GroupCategory::create(['name'=>'Bebidas', 'post_type_id' => 2,'status'=>1]);//31

            Category::create(['name' => 'Cerveza', 'group_id'=>31]);
            Category::create(['name' => 'Refrescos, Aguas Minerales', 'group_id'=>31]);
            Category::create(['name' => 'Café,Té,Infusiones y Cacao', 'group_id'=>31]);
            Category::create(['name' => 'Zumos,Mosto y Batidos', 'group_id'=>31]);
            Category::create(['name' => 'Vinos', 'group_id'=>31]);
            Category::create(['name' => 'Champagne,Cavas,Granvas y Sidra', 'group_id'=>31]);
            Category::create(['name' => 'Licores', 'group_id'=>31]);//80

        GroupCategory::create(['name'=>'Suministro para Hotel', 'post_type_id' => 2,'status'=>1]);//32

            Category::create(['name' => 'Ropa de Cama para Hoteles', 'group_id'=>32]);
            Category::create(['name' => 'Textil de Baño', 'group_id'=>32]);
            Category::create(['name' => 'Comodidades', 'group_id'=>32]);
            Category::create(['name' => 'Baños de Hotel', 'group_id'=>32]);
            Category::create(['name' => 'Colchones para Hotel', 'group_id'=>32]);
            Category::create(['name' => 'Equipamiento de Habitación de Hotel', 'group_id'=>32]);
            Category::create(['name' => 'Almohadas y Rellenos', 'group_id'=>32]);
            Category::create(['name' => 'Camas para Hoteles', 'group_id'=>32]);
            Category::create(['name' => 'Accesorios para Hoteles', 'group_id'=>32]);
            Category::create(['name' => 'Articulos de Limpieza', 'group_id'=>32]);//90

        GroupCategory::create(['name'=>'Productos para Catering', 'post_type_id' => 2,'status'=>1]);//33

            Category::create(['name' => 'Desechables Catering', 'group_id'=>33]);
            Category::create(['name' => 'Utensilios Catering', 'group_id'=>33]);
            Category::create(['name' => 'Menaje para Catering', 'group_id'=>33]);

        GroupCategory::create(['name'=>'Ropa de Trabajo para Hostelería', 'post_type_id' => 2,'status'=>1]);//34
            Category::create(['name' => 'Ropa de Servicio', 'group_id'=>34]);
            Category::create(['name' => 'Ropa de Cocina', 'group_id'=>34]);
            Category::create(['name' => 'Uniformes Bienestar Belleza y Salud', 'group_id'=>34]);
            Category::create(['name' => 'Calzado Profesional', 'group_id'=>34]);
            Category::create(['name' => 'Trajes Típicos', 'group_id'=>34]);
           
        GroupCategory::create(['name'=>'Limpieza', 'post_type_id' => 2,'status'=>1]);//35
            Category::create(['name' => 'Productos para Limpieza y Desinfección', 'group_id'=>35]);
            Category::create(['name' => 'Lavamanos Industriales', 'group_id'=>35]);
            Category::create(['name' => 'Dispensador de Jabón Industrial', 'group_id'=>35]);
            Category::create(['name' => 'Secador de Manos', 'group_id'=>35]);
            Category::create(['name' => 'Dispensadores y Bobinas de Papel', 'group_id'=>35]);
            Category::create(['name' => 'Cubos de Basura', 'group_id'=>35]);
            Category::create(['name' => 'Bolsas de Basura', 'group_id'=>35]);
            Category::create(['name' => 'Recogedores', 'group_id'=>35]);
            Category::create(['name' => 'Rollos de Cocina', 'group_id'=>35]);
            Category::create(['name' => 'Mata Insectos', 'group_id'=>35]);
            Category::create(['name' => 'Productos Químicos de Limpieza', 'group_id'=>35]);

   
    }
    
}
