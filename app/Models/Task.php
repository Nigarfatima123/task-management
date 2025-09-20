<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Task extends Model
{
	use HasFactory;

	protected $fillable = [
		'title',
		'description',
		'is_completed',
		'position',
		'user_id',
	];

	protected $casts = [
		'is_completed' => 'boolean',
	];

	// Scopes for filtering
	public function scopeCompleted($query)
	{
		return $query->where('is_completed', true);
	}

	public function scopeIncomplete($query)
	{
		return $query->where('is_completed', false);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}