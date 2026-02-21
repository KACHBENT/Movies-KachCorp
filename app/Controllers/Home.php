<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {

        $db = \Config\Database::connect();
        
        $peliculasNetflix = $db->table('tbl_rel_pelicula p')
            ->select("
                p.peliculaId      AS peliculaId,
                p.pelicula_Nombre AS titulo,
                p.pelicula_Descripcion AS descripcion,
                p.pelicula_TrailerUrl  AS video,
                p.pelicula_Creacion    AS creacion,
                img.image_Url          AS imagen,
                g.genero_Valor         AS genero,
                c.clasificacion_Valor  AS clasificacion
            ")
            ->join('tbl_cat_genero g', 'g.generoId = p.generoId AND g.genero_Activo = 1', 'inner')
            ->join('tbl_cat_clasificacion c', 'c.clasificacionId = p.clasificacionId AND c.clasificacion_Activo = 1', 'inner')
            ->join('tbl_ope_image img', 'img.imageId = p.imageId AND img.image_Activo = 1', 'left')
            ->where('p.pelicula_Activo', 1)
            ->orderBy('p.peliculaId', 'DESC')
            ->get()->getResultArray();
            
        $recomendaciones = [
            [
                'titulo' => 'The Batman',
                'descripcion' => 'En esta versión dirigida por Matt Reeves, Gotham City se encuentra sumida en la corrupción y el miedo. Bruce Wayne, en sus primeros años como Batman, aún no es el héroe consolidado que conocemos, sino un vigilante obsesionado con impartir justicia.
',
                'imagen' => base_url('images/banners/batman.jpg'),
                'video' => 'https://geo.dailymotion.com/player/x7zhh.html?video=x87809z&mute=true'
            ],
            [
                'titulo' => 'Godzilla',
                'descripcion' => 'En un mundo contemporáneo, la humanidad descubre que criaturas gigantes llamadas MUTOs (Massive Unidentified Terrestrial Organisms) han despertado y amenazan con destruir ciudades enteras. En medio de este caos, surge Godzilla, un titán ancestral que se convierte en el inesperado defensor de la Tierra.
',
                'imagen' => base_url('images/banners/godzilla.jpg'),
                'video' => 'https://geo.dailymotion.com/player/x7zhh.html?video=x7tzgbu&mute=true',
            ],
            [
                'titulo' => 'La guerra de los mundos',
                'descripcion' => 'La historia sigue a Ray Ferrier (Tom Cruise), un hombre común y padre divorciado que intenta proteger a sus hijos cuando una invasión extraterrestre arrasa la Tierra. Gigantescas máquinas alienígenas emergen del suelo y comienzan a destruir ciudades con un poder devastador, dejando a la humanidad indefensa.
',
                'imagen' => base_url('images/banners/war_of_the_worlds.jpg'),
                'video' => 'https://geo.dailymotion.com/player/x7zhh.html?video=x8h4waw&mute=true',
            ],

            [
                'titulo' => 'Star Wars. Episodio III: La venganza de los Sith',
                'descripcion' => 'Dirigida por George Lucas, esta película es la tercera entrega de la trilogía de precuelas y muestra el momento más decisivo en la saga: la caída de Anakin Skywalker y el nacimiento de Darth Vader.
',
                'imagen' => base_url('images/banners/starwars3.jpg'),
                'video' => 'https://geo.dailymotion.com/player/x7zhh.html?video=x9hyuwo&mute=true'
            ],
            [
                'titulo' => 'A Silent Voice',
                'descripcion' => 'Dirigida por Naoko Yamada y producida por Kyoto Animation, esta película japonesa adapta el manga de Yoshitoki Ōima. Es un drama emocional que explora el bullying, la discapacidad y la búsqueda de redención.
',
                'imagen' => base_url('images/banners/koe_no_katachi_a_silent_voices.jpg'),
                'video' => 'https://geo.dailymotion.com/player/x7zhh.html?video=x7tzigr&mute=true',
            ],
            [
                'titulo' => 'Rompenieves (Snowpiercer)',
                'descripcion' => 'Un fallido experimento para detener el calentamiento global ha congelado el planeta, acabando con casi toda la vida en la Tierra. Los únicos supervivientes viajan a bordo del Snowpiercer, un tren que recorre el mundo sin detenerse gracias a un motor perpetuo.
',
                'imagen' => base_url('images/banners/snowpiercer.jpg'),
                'video' => 'https://geo.dailymotion.com/player/x7zhh.html?video=x7tzgcx&mute=true',
            ],
        ];

        return view('Inicio/inicio', [
            'recomendaciones' => $recomendaciones,
            'peliculasNetflix' => $peliculasNetflix,
        ]);
    }

}
