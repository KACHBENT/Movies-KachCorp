<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $recomendaciones = [
            [
                'titulo' => 'Video desarrollo circuitos logicos',
                'descripcion' => 'Video de pruebas desarrollo de carrito electrico',
                'imagen' => base_url('images/banners/banner1.png'),
                'video' => 'https://mega.nz/file/IEYF3ZaD#Ut0efKcE3Xemx28F9ucmcUcdU91ky_J8IhWizflM934',
                'tag' => 'MEGA'
            ],
            [
                'titulo' => 'Video trafico de fabuloso y pinol',
                'descripcion' => 'En la era de trafico de fabuloso y pinol',
                'imagen' => base_url('images/banners/banner1.png'),
                'video' => 'https://mega.nz/file/xFYEmDaT#SuA3ec5VQWXarq9nLoI8_yzHPHitgMQIthKM3DW5jT0',
                'tag' => 'MEGA'
            ],
            [
                'titulo' => 'YouTube Practica Examen',
                'descripcion' => 'Practica Examen para la lord Infografias',
                'imagen' => base_url('images/banners/banner1.png'),
                'video' => 'https://mega.nz/file/JUYUXABK#SWXUPLpPixu2VgE7zlEcjc8DIfshNn4CMl1qtK-SrOw',
                'tag' => 'KACHBENT YT'
            ],
        ];

        return view('Inicio/inicio', [
            'recomendaciones' => $recomendaciones
        ]);
    }

    public function enrique(): string
    {
        return view('EnriquePortafolio/iniE');
    }
    public function brandon(): string
    {
        return view('BrandonPortafolio/iniB');
    }
        public function julio(): string
    {
        return view('JulioPortafolio/iniA');
    }

}
