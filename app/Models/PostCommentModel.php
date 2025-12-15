<?php

namespace App\Models;

use CodeIgniter\Model;

class PostCommentModel extends Model
{
    protected $table = 'post_comments';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'post_id',
        'author_name',
        'author_email',
        'content',
        'is_approved',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';
}
