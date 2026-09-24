<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $post_id
 * @property string $file_path
 * @property string $file_type
 * @property string|null $file_size
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Post $post
 */
#[Fillable(['post_id', 'file_path', 'file_type', 'file_size'])]
class PostAttachment extends Model
{
    /** @return BelongsTo<Post, $this> */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
