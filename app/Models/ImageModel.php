<?php

namespace App\Models;

use CodeIgniter\Model;

class ImageModel extends Model
{
    protected $table            = 'tbl_ope_image';
    protected $primaryKey       = 'imageId';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields = [
        'image_Url',
        'image_FileName',
        'image_Mime',
        'image_SizeBytes',
        'image_Hash',
        'image_Activo',
        'image_CreatedAt',
        'image_UpdatedAt',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'image_CreatedAt';
    protected $updatedField  = 'image_UpdatedAt';

    protected $validationRules = [
        'image_Url'       => 'required|max_length[2048]',
        'image_FileName'  => 'permit_empty|max_length[255]',
        'image_Mime'      => 'permit_empty|max_length[100]',
        'image_SizeBytes' => 'permit_empty|is_natural',
        'image_Hash'      => 'permit_empty|exact_length[64]',
        'image_Activo'    => 'permit_empty|in_list[0,1]',
    ];
}
